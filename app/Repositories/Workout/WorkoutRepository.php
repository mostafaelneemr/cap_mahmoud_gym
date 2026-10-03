<?php

namespace App\Repositories\Workout;

use App\Models\Exercise;
use App\Models\WorkoutPlan;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class WorkoutRepository extends BaseRepository
{
    protected $modeler = WorkoutPlan::class;

    public function getDataTableQuery()
    {
        return $this->modeler->active()->with('trainee.user')
            ->select(['id', 'trainee_id', 'day_name', 'day_name_ar', 'created_at']);
    }

    public function syncDayExercises(int $dayId, array $exercisesData, array $dayData = [])
    {
        $day = $this->modeler::find($dayId);
        if (!$day) {
            return null;
        }

        if (!empty($dayData)) {
            if (empty($dayData['day_name']) && !empty($dayData['day_name_ar'])) {
                $dayData['day_name'] = $dayData['day_name_ar'];
            }
            $day->update($dayData);
        }

        $keptExerciseIds = [];
        foreach ($exercisesData as $exData) {
            if (empty($exData['name']) && !empty($exData['name_ar'])) {
                $exData['name'] = $exData['name_ar'];
            }

            if (empty($exData['name']) && empty($exData['name_ar'])) {
                continue;
            }

            if (!empty($exData['exercise_id'])) {
                // Update
                $exercise = Exercise::find($exData['exercise_id']);
                if ($exercise && $exercise->workout_plan_id == $dayId) {
                    $exercise->update($exData);
                    $keptExerciseIds[] = $exercise->id;
                }
            } else {
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
        return $this->modeler->where('trainee_id', $id)->where('status', 'active')->update(['status' => 'archived']);
    }

    public function restoreLatestArchivedWorkout(int $traineeId, ?string $archivedAt = null): bool
    {
        return DB::transaction(function () use ($traineeId, $archivedAt) {
            $query = WorkoutPlan::where('trainee_id', $traineeId)->where('status', 'archived');

            if ($archivedAt) {
                $plansToRestore = (clone $query)->where('updated_at', $archivedAt)->get();
                if ($plansToRestore->isEmpty()) {
                    $plansToRestore = (clone $query)->whereDate('updated_at', substr($archivedAt, 0, 10))->get();
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

            // 1. Swap: If trainee currently has active days, archive them first
            $this->modeler
                ->where('trainee_id', $traineeId)
                ->where('status', 'active')
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
        return $this->modeler->where('trainee_id', $traineeId)->active()->with('exercises')->get();
    }
}
