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

    public function currentWorkoutPlans()
    {
        return $this->hasMany(WorkoutPlan::class, 'trainee_id', 'id')
            ->whereIn('status', ['active', 'inactive'])
            ->orderBy('id', 'asc');
    }

    public function archivedWorkoutPlans()
    {
        return $this->hasMany(WorkoutPlan::class, 'trainee_id', 'id')
            ->where('status', 'archived')
            ->orderBy('updated_at', 'desc')
            ->orderBy('id', 'asc');
    }

    /**
     * Group archived workout days into distinct program versions/batches.
     */
    public function getArchivedProgramBatchesAttribute(): \Illuminate\Support\Collection
    {
        $archived = $this->archivedWorkoutPlans;
        if ($archived->isEmpty()) {
            return collect();
        }

        $batches = [];
        $sorted = $archived->sortByDesc('updated_at');

        foreach ($sorted as $plan) {
            $ts = $plan->updated_at ? $plan->updated_at->timestamp : 0;
            $matchedKey = null;

            // Group days archived within 120 seconds of each other into the same routine batch
            foreach ($batches as $key => $batch) {
                if (abs($batch['timestamp'] - $ts) <= 120) {
                    $matchedKey = $key;
                    break;
                }
            }

            if ($matchedKey === null) {
                $matchedKey = 'batch_' . ($plan->updated_at ? $plan->updated_at->format('Ymd_His') : $plan->id) . '_' . $plan->id;
                $batches[$matchedKey] = [
                    'key' => $matchedKey,
                    'timestamp' => $ts,
                    'archived_at' => $plan->updated_at,
                    'archived_date_formatted' => $plan->updated_at ? $plan->updated_at->format('M d, Y - h:i A') : __('Unknown Date'),
                    'plans' => collect(),
                ];
            }

            $batches[$matchedKey]['plans']->push($plan);
        }

        $result = [];
        foreach ($batches as $key => $batch) {
            $plans = $batch['plans']->sortBy('id')->values();
            $daysCount = $plans->count();
            $exercisesCount = $plans->sum(fn ($p) => $p->exercises ? $p->exercises->count() : 0);
            $dayNames = $plans->pluck('day_name')->filter()->values()->all();

            $routineTitle = !empty($dayNames)
                ? implode(' • ', array_slice($dayNames, 0, 4)) . (count($dayNames) > 4 ? '...' : '')
                : __('Workout Routine');

            $result[] = [
                'key' => $key,
                'batch_key' => $key,
                'archived_at' => $batch['archived_at'],
                'archived_date_formatted' => $batch['archived_date_formatted'],
                'plan_ids' => $plans->pluck('id')->all(),
                'plan_ids_str' => $plans->pluck('id')->implode(','),
                'days_count' => $daysCount,
                'exercises_count' => $exercisesCount,
                'routine_title' => $routineTitle,
                'routine_name' => $routineTitle,
                'plans' => $plans,
            ];
        }

        return collect($result);
    }

    public function nutritionPlans()
    {
        return $this->hasMany(NutritionPlan::class, 'trainee_id', 'id');
    }

    public function activeNutritionPlans()
    {
        return $this->hasMany(NutritionPlan::class, 'trainee_id', 'id')->where('status', 'active');
    }

    public function progressCheckins()
    {
        return $this->hasMany(TraineeProgressCheckin::class, 'trainee_id', 'id')->orderBy('checkin_date', 'desc');
    }

    /**
     * تقارير تعديل أوزان التمارين المقدمة من المتدرب
     */
    public function loadAdjustments()
    {
        return $this->hasMany(TraineeLoadAdjustment::class, 'trainee_id', 'id')->orderBy('created_at', 'desc');
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
