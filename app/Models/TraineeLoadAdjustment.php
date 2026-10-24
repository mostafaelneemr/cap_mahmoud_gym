<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * نموذج تقارير تحديث أوزان التمارين للمتدربين.
 * يتم إنشاء سجل منفصل لكل تقرير مع حفظ لقطات ثابتة لبيانات التمرين واليوم التدريبي.
 */
class TraineeLoadAdjustment extends Model
{
    use HasFactory;

    protected $table = 'trainee_load_adjustments';

    protected $fillable = [
        'trainee_id',
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

    protected $casts = [
        'reviewed_at' => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    /**
     * المتدرب صاحب التقرير
     */
    public function trainee()
    {
        return $this->belongsTo(Trainee::class, 'trainee_id');
    }

    /**
     * الخطة أو اليوم التدريبي المرتبط
     */
    public function workoutPlan()
    {
        return $this->belongsTo(WorkoutPlan::class, 'workout_plan_id');
    }

    /**
     * التمرين المرتبط (إن وجد)
     */
    public function exercise()
    {
        return $this->belongsTo(Exercise::class, 'exercise_id');
    }

    /**
     * المدرب الذي قام بالمراجعة
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * نطاق استعلام التقارير المعلقة
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * نطاق استعلام التقارير الخاصة بمتدرب محدد
     */
    public function scopeForTrainee($query, int $traineeId)
    {
        return $query->where('trainee_id', $traineeId);
    }
}
