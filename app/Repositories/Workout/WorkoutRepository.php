<?php

namespace App\Repositories\Workout;

use App\Models\Exercise;
use App\Models\WorkoutPlan;
use App\Repositories\BaseRepository;

class WorkoutRepository extends BaseRepository
{
    protected $modeler = WorkoutPlan::class;

    public function getDataTableQuery()
    {
        return $this->modeler->active()->with('trainee.user')
            ->select(['id', 'trainee_id', 'day_name', 'created_at']);
    }

    public function syncDayExercises(int $dayId, array $exercisesData)
    {
        $day = $this->modeler::find($dayId);

        $keptExerciseIds = [];
        foreach ($exercisesData as $exData) {
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

    public function getExcercisesByTrainee($traineeId)
    {
        return $this->modeler->where('trainee_id', $traineeId)->active()->with('exercises')->get();
    }
}
