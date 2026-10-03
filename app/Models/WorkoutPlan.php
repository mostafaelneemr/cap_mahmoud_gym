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

    protected $fillable = ['trainee_id', 'day_name', 'day_name_ar', 'warmup', 'warmup_ar', 'post_workout', 'post_workout_ar', 'status'];

    public function getDisplayDayNameAttribute(): ?string
    {
        $locale = request()->get('lang', app()->getLocale());
        if ($locale === 'ar' && filled($this->day_name_ar)) {
            return $this->day_name_ar;
        }
        return $this->day_name;
    }

    public function getDisplayWarmupAttribute(): ?string
    {
        $locale = request()->get('lang', app()->getLocale());
        if ($locale === 'ar' && filled($this->warmup_ar)) {
            return $this->warmup_ar;
        }
        return $this->warmup;
    }

    public function getDisplayPostWorkoutAttribute(): ?string
    {
        $locale = request()->get('lang', app()->getLocale());
        if ($locale === 'ar' && filled($this->post_workout_ar)) {
            return $this->post_workout_ar;
        }
        return $this->post_workout;
    }

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
