<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Trainee extends Authenticatable implements AuthenticatableContract
{
    use SoftDeletes, Notifiable, LogsActivity;

    protected $table = 'trainees';
    public $timestamps = true;
    public $primaryKey = 'id';
    public $modelPath = 'App\Models\Trainee';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'age',
        'weight',
        'height',
        'membership_start',
        'membership_end',
        'training_level',
        'remember_token',
        // NOTE: 'google_id' and 'status' are intentionally excluded from $fillable.
        // Set them explicitly: $trainee->google_id = ...; $trainee->status = ...; $trainee->save();
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
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

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($trainee) {
            $workoutPlans = $trainee->workoutPlans()->get();
            foreach ($workoutPlans as $workoutPlan) {
                $trainee->isForceDeleting() ? $workoutPlan->forceDelete() : $workoutPlan->delete();
            }
        });

        static::restoring(function ($trainee) {
            WorkoutPlan::onlyTrashed()->where('trainee_id', $trainee->id)->get()->each->restore();
        });
    }
}

