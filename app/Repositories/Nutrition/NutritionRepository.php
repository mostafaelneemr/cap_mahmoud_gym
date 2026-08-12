<?php

namespace App\Repositories\Nutrition;

use App\Models\NutritionPlan;
use App\Repositories\BaseRepository;

class NutritionRepository extends BaseRepository
{
    protected $modeler = NutritionPlan::class;

    public function getDataTableQuery()
    {
        return $this->modeler->active()->with('trainee.user')
            ->select(['id', 'trainee_id', 'name', 'status', 'created_at']);
    }

    public function updateTraineeNutrition($traineeId)
    {
        return $this->modeler->where('trainee_id', $traineeId)->where('status', 'active')->update(['status' => 'archived']);
    }

    public function getActiveNutritionByTrainee($traineeId)
    {
        return $this->modeler->where('trainee_id', $traineeId)->active()->latest()->first();
    }

    public function getNutritionsByTrainee($traineeId)
    {
        return $this->modeler->where('trainee_id', $traineeId)->latest()->get();
    }
}
