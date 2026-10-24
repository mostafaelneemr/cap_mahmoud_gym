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
     * عناصر المجموعات الفردية المرتبطة بهذا الطلب
     */
    public function items()
    {
        return $this->hasMany(TraineeLoadAdjustmentItem::class, 'load_adjustment_id')->orderBy('set_number', 'asc');
    }

    /**
     * التحقق مما إذا كان الطلب يتضمن عناصر مجموعات فردية مفصلة
     */
    public function hasSetItems(): bool
    {
        if ($this->relationLoaded('items')) {
            return $this->items->isNotEmpty();
        }
        return $this->items()->exists();
    }

    /**
     * إعادة احتساب وتحديث الحالة التراكمية للطلب بناءً على حالات مجموعاته
     */
    public function recalculateAggregateStatus(bool $save = true): string
    {
        $items = $this->relationLoaded('items') ? $this->items : $this->items()->get();

        if ($items->isEmpty()) {
            return $this->status;
        }

        $allApprovedOrRejected = $items->every(fn ($i) => in_array($i->status, [
            TraineeLoadAdjustmentItem::STATUS_APPROVED,
            TraineeLoadAdjustmentItem::STATUS_REJECTED
        ], true));

        $hasConflict = $items->contains(fn ($i) => $i->status === TraineeLoadAdjustmentItem::STATUS_CONFLICT);
        $hasApprovedOrRejected = $items->contains(fn ($i) => in_array($i->status, [
            TraineeLoadAdjustmentItem::STATUS_APPROVED,
            TraineeLoadAdjustmentItem::STATUS_REJECTED
        ], true));
        $hasPending = $items->contains(fn ($i) => $i->status === TraineeLoadAdjustmentItem::STATUS_PENDING);

        if ($allApprovedOrRejected) {
            $newStatus = 'reviewed';
        } elseif ($hasConflict) {
            $newStatus = 'conflict';
        } elseif ($hasApprovedOrRejected && $hasPending) {
            $newStatus = 'partially_reviewed';
        } else {
            $newStatus = 'pending';
        }

        if ($save && $this->status !== $newStatus) {
            $this->status = $newStatus;
            $this->save();
        }

        return $newStatus;
    }

    /**
     * نطاق استعلام التقارير المعلقة
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'partially_reviewed', 'conflict']);
    }

    /**
     * نطاق استعلام التقارير الخاصة بمتدرب محدد
     */
    public function scopeForTrainee($query, int $traineeId)
    {
        return $query->where('trainee_id', $traineeId);
    }

    /**
     * اسم صاحب الملاحظة (المتدرب)
     */
    public function getTraineeAuthorNameAttribute(): ?string
    {
        return $this->trainee?->name ?? $this->trainee?->user?->name;
    }

    /**
     * اسم كاتب ملاحظة المراجعة (المدرب)
     */
    public function getCoachAuthorNameAttribute(): ?string
    {
        return $this->reviewer?->name;
    }

    /**
     * توقيت إضافة ملاحظة المراجعة للمدرب
     */
    public function getCoachNotesTimestampAttribute()
    {
        return $this->reviewed_at ?? $this->updated_at;
    }
}

