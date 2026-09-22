<?php

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;

class Trainee extends GlobalModel
{
    use SoftDeletes, Notifiable, LogsActivity;

    protected $table = 'trainees';
    public $timestamps = true;
    public $primaryKey = 'id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = ['user_id','email', 'age', 'weight', 'height', 'membership_start', 'membership_end', 'training_level', 'status'];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function workoutPlans()
    {
        return $this->hasMany(WorkoutPlan::class, 'trainee_id', 'id');
    }

    public function activeWorkoutPlans()
    {
        return $this->hasMany(WorkoutPlan::class, 'trainee_id', 'id')->where('status', 'active');
    }

    public function archivedWorkoutPlans()
    {
        return $this->hasMany(WorkoutPlan::class, 'trainee_id', 'id')->where('status', 'archived');
    }

    public function nutritionPlans()
    {
        return $this->hasMany(NutritionPlan::class, 'trainee_id', 'id');
    }

    public function activeNutritionPlans()
    {
        return $this->hasMany(NutritionPlan::class, 'trainee_id', 'id')->where('status', 'active');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($trainee) {
            $workoutPlans = $trainee->workoutPlans()->get();
            foreach ($workoutPlans as $workoutPlan) {
                $trainee->isForceDeleting() ? $workoutPlan->forceDelete() : $workoutPlan->delete();
            }

            $nutritionPlans = $trainee->nutritionPlans()->get();
            foreach ($nutritionPlans as $nutritionPlan) {
                $trainee->isForceDeleting() ? $nutritionPlan->forceDelete() : $nutritionPlan->delete();
            }
        });

        static::restoring(function ($trainee) {
            WorkoutPlan::onlyTrashed()->where('trainee_id', $trainee->id)->get()->each->restore();
            NutritionPlan::onlyTrashed()->where('trainee_id', $trainee->id)->get()->each->restore();
        });
    }
}
