<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;

class WorkoutPlan extends GlobalModel
{
    use SoftDeletes, Notifiable, LogsActivity;

    protected $table = 'workout_plans';
    public $timestamps = true;
    public $primaryKey = 'id';
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    protected $fillable = ['trainee_id', 'day_name', 'warmup', 'post_workout', 'status'];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function trainee()
    {
        return $this->belongsTo(Trainee::class, 'trainee_id', 'id');
    }

    public function exercises()
    {
        return $this->hasMany(Exercise::class, 'workout_plan_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($workoutPlan) {
            $exercises = $workoutPlan->exercises()->get();
            foreach ($exercises as $exercise) {
                $workoutPlan->isForceDeleting() ? $exercise->forceDelete() : $exercise->delete();
            }
        });

        static::restoring(function ($workoutPlan) {
            Exercise::onlyTrashed()->where('workout_plan_id', $workoutPlan->id)->get()->each->restore();
        });
    }
}
