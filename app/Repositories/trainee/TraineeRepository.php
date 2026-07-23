<?php

namespace App\Repositories\Trainee;

use App\Models\Trainee;
use App\Repositories\BaseRepository;

class TraineeRepository extends BaseRepository
{
    protected $modeler = Trainee::class;
    public function getDataTableQuery()
    {
        return $this->modeler->select(['id', 'user_id', 'age', 'weight', 'height', 'membership_start', 'membership_end',
            'training_level', 'status']);
    }
    public function getTraineeFirst($userId)
    {
        return $this->modeler->where('user_id', $userId)->first();
    }
    public function getWithUser()
    {
        return $this->modeler->with('user')->whereDoesntHave('activeWorkoutPlans')->get();
    }
}
