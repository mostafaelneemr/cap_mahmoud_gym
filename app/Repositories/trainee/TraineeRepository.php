<?php

namespace App\Repositories\Trainee;

use App\Models\Trainee;
use App\Repositories\BaseRepository;

class TraineeRepository extends BaseRepository
{
    protected $modeler = Trainee::class;

    public function getDataTableQuery()
    {
        return $this->modeler->select(['id','user_id','age','weight','height','membership_start','membership_end' ,'training_level','status']);
    }
}
