<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * نموذج يمثل طلب تعديل وزن لمجموعة تدريبية فردية محددة.
 * يدعم القرارات المستقلة للمدرب وكشف التعارض والاحتفاظ بسجل تاريخي دقيق.
 */
class TraineeLoadAdjustmentItem extends Model
{
    use HasFactory;

    protected $table = 'trainee_load_adjustment_items';

    public const STATUS_PENDING  = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CONFLICT = 'conflict';

    protected $fillable = [
        'load_adjustment_id',
        'set_number',
        'base_weight',
        'suggested_weight',
        'approved_weight',
        'status',
        'coach_notes',
        'reviewed_by',
        'reviewed_at',
        'applied_at',
        'conflict_detected_at',
        'conflict_current_weight',
    ];

    protected $casts = [
        'set_number'           => 'integer',
        'reviewed_at'          => 'datetime',
        'applied_at'           => 'datetime',
        'conflict_detected_at' => 'datetime',
        'created_at'           => 'datetime',
        'updated_at'           => 'datetime',
    ];

    /**
     * الطلب الرئيسي التابع له هذا العنصر
     */
    public function loadAdjustment()
    {
        return $this->belongsTo(TraineeLoadAdjustment::class, 'load_adjustment_id');
    }

    /**
     * المدرب الذي راجع هذا العنصر
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeConflict($query)
    {
        return $query->where('status', self::STATUS_CONFLICT);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function hasConflict(): bool
    {
        return $this->status === self::STATUS_CONFLICT;
    }

    /**
     * اسم كاتب ملاحظة المراجعة للمجموعة (المدرب)
     */
    public function getCoachAuthorNameAttribute(): ?string
    {
        return $this->reviewer?->name ?? $this->loadAdjustment?->reviewer?->name;
    }

    /**
     * توقيت إضافة ملاحظة المراجعة للمجموعة
     */
    public function getCoachNotesTimestampAttribute()
    {
        return $this->reviewed_at ?? $this->updated_at;
    }
}
