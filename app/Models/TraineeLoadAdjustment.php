<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;

class TraineeLoadAdjustment extends GlobalModel
{
    use SoftDeletes, Notifiable, LogsActivity;

    protected $table = 'trainee_load_adjustments';
    public $timestamps = true;
    public $primaryKey = 'id';

    protected $casts = [
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'reviewed_at' => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    protected $fillable = [
        'trainee_id',
        'trainee_name_snapshot',
        'workout_plan_id',
        'exercise_id',
        'workout_day_name',
        'exercise_name',
        'prescribed_load',
        'reported_load',
        'notes',
        'status',
        'coach_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    /**
     * الحالات المعتمدة لدورة مراجعة التقرير.
     */
    public const STATUS_PENDING   = 'pending';
    public const STATUS_REVIEWED  = 'reviewed';
    public const STATUS_DISMISSED = 'dismissed';

    public function trainee()
    {
        return $this->belongsTo(Trainee::class, 'trainee_id', 'id');
    }

    public function workoutPlan()
    {
        return $this->belongsTo(WorkoutPlan::class, 'workout_plan_id', 'id');
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class, 'exercise_id', 'id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by', 'id');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeReviewed($query)
    {
        return $query->where('status', self::STATUS_REVIEWED);
    }

    public function scopeDismissed($query)
    {
        return $query->where('status', self::STATUS_DISMISSED);
    }

    public function scopeForTrainee($query, $traineeId)
    {
        return $query->where('trainee_id', $traineeId);
    }
}
