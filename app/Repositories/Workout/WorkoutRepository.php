<?php

namespace App\Repositories\Workout;

use App\Models\Exercise;
use App\Models\WorkoutPlan;
use App\Repositories\BaseRepository;
use App\Services\ExerciseMachineImageService;
use Illuminate\Support\Facades\DB;

class WorkoutRepository extends BaseRepository
{
    protected $modeler = WorkoutPlan::class;

    public function getDataTableQuery()
    {
        return $this->modeler->active()->with('trainee.user')
            ->select(['id', 'trainee_id', 'day_name', 'created_at']);
    }

    public function syncDayExercises(int $dayId, array $exercisesData, array $dayData = [])
    {
        $day = $this->modeler::find($dayId);
        if (!$day) {
            return null;
        }

        if (!empty($dayData)) {
            unset($dayData['day_name_ar'], $dayData['warmup_ar'], $dayData['post_workout_ar']);
            $day->update($dayData);
        }

        $imageService = app(ExerciseMachineImageService::class);

        $keptExerciseIds = [];
        foreach ($exercisesData as $exData) {
            if (empty($exData['name'])) {
                continue;
            }

            unset($exData['name_ar'], $exData['notes_ar']);

            // Multi-weight support: accept `internal_weight[]` (or legacy `weight`), max 4 values.
            $exData['internal_weight'] = Exercise::normalizeWeights(
                $exData['internal_weight'] ?? ($exData['weight'] ?? null)
            );
            unset($exData['weight']);

            if (empty($exData['type'])) {
                $exData['type'] = 'main';
            }

            if (array_key_exists('exercise_library_id', $exData)) {
                $exData['exercise_library_id'] = !empty($exData['exercise_library_id']) ? (int) $exData['exercise_library_id'] : null;
            }

            if (!empty($exData['exercise_id'])) {
                // Update
                $exercise = Exercise::find($exData['exercise_id']);
                if ($exercise && $exercise->workout_plan_id == $dayId) {
                    [$exData, $oldImage] = $imageService->resolve($exData, $exercise);
                    $exercise->update($exData);
                    $imageService->delete($oldImage);
                    $keptExerciseIds[] = $exercise->id;
                }
            } else {
                [$exData] = $imageService->resolve($exData);
                $exData['workout_plan_id'] = $dayId;
                $newEx = Exercise::create($exData);
                $keptExerciseIds[] = $newEx->id;
            }
        }

        // Delete exercises that were removed from the repeater
        Exercise::where('workout_plan_id', $dayId)->whereNotIn('id', $keptExerciseIds)->delete();

        return $day;
    }

    public function updateTraineeWorkout($id)
    {
        return $this->modeler
            ->where('trainee_id', $id)
            ->whereIn('status', ['active', 'inactive'])
            ->update(['status' => 'archived']);
    }

    public function restoreLatestArchivedWorkout(int $traineeId, $batchOrIds = null): bool
    {
        return DB::transaction(function () use ($traineeId, $batchOrIds) {
            $query = WorkoutPlan::where('trainee_id', $traineeId)->where('status', 'archived');
            $plansToRestore = collect();

            if (!empty($batchOrIds)) {
                if (is_array($batchOrIds)) {
                    $ids = array_filter(array_map('intval', $batchOrIds));
                    $plansToRestore = (clone $query)->whereIn('id', $ids)->get();
                } elseif (is_string($batchOrIds) && str_contains($batchOrIds, ',')) {
                    $ids = array_filter(array_map('intval', explode(',', $batchOrIds)));
                    $plansToRestore = (clone $query)->whereIn('id', $ids)->get();
                } elseif (is_numeric($batchOrIds)) {
                    $plansToRestore = (clone $query)->where('id', (int) $batchOrIds)->get();
                } else {
                    $plansToRestore = (clone $query)->where('updated_at', $batchOrIds)->get();
                    if ($plansToRestore->isEmpty()) {
                        $plansToRestore = (clone $query)->whereDate('updated_at', substr($batchOrIds, 0, 10))->get();
                    }
                }
            } else {
                $latestTimestamp = (clone $query)->max('updated_at');
                if (!$latestTimestamp) {
                    return false;
                }
                $plansToRestore = (clone $query)->where('updated_at', $latestTimestamp)->get();
            }

            if ($plansToRestore->isEmpty()) {
                return false;
            }

            // 1. Swap: Archive all current active/inactive routines for this trainee
            $this->modeler
                ->where('trainee_id', $traineeId)
                ->whereIn('status', ['active', 'inactive'])
                ->update(['status' => 'archived']);

            // 2. Restore chosen archived days back to active
            $this->modeler
                ->where('trainee_id', $traineeId)
                ->whereIn('id', $plansToRestore->pluck('id'))
                ->update(['status' => 'active']);

            return true;
        });
    }

    public function getExcercisesByTrainee($traineeId)
    {
        $relations = [
            'exercises',
            'exercises.exerciseLibrary',
            'warmupExercises',
            'warmupExercises.exerciseLibrary',
            'mainExercises',
            'mainExercises.exerciseLibrary',
            'postWorkoutExercises',
            'postWorkoutExercises.exerciseLibrary'
        ];
        if (\Illuminate\Support\Facades\Schema::hasTable('trainee_load_adjustments')) {
            $relations[] = 'loadAdjustments';
            $relations[] = 'loadAdjustments.reviewer';
            $relations[] = 'loadAdjustments.trainee';
            $relations[] = 'loadAdjustments.trainee.user';
            if (\Illuminate\Support\Facades\Schema::hasTable('trainee_load_adjustment_items')) {
                $relations[] = 'loadAdjustments.items';
                $relations[] = 'loadAdjustments.items.reviewer';
            }
        }
        return $this->modeler->where('trainee_id', $traineeId)->active()->with($relations)->get();
    }
}

