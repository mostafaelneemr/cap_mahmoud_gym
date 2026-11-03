<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppCampaign extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_campaigns';

    protected $fillable = [
        'coach_id',
        'title',
        'message_template',
        'total_recipients',
        'opened_count',
        'confirmed_count',
        'skipped_count',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    public const STATUS_DRAFT       = 'draft';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED   = 'completed';
    public const STATUS_CANCELLED   = 'cancelled';

    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function recipients()
    {
        return $this->hasMany(WhatsAppCampaignRecipient::class, 'campaign_id')->orderBy('id', 'asc');
    }

    public function scopeForCoach($query, int $coachId)
    {
        return $query->where('coach_id', $coachId);
    }

    /**
     * Recompute campaign counters based on recipients' states.
     */
    public function syncCounters(bool $save = true): void
    {
        $this->total_recipients = $this->recipients()->count();
        $this->opened_count     = $this->recipients()->whereNotNull('opened_at')->count();
        $this->confirmed_count  = $this->recipients()->where('status', WhatsAppCampaignRecipient::STATUS_CONFIRMED)->count();
        $this->skipped_count    = $this->recipients()->where('status', WhatsAppCampaignRecipient::STATUS_SKIPPED)->count();

        $processedCount = $this->confirmed_count + $this->skipped_count;
        if ($this->total_recipients > 0 && $processedCount >= $this->total_recipients) {
            if ($this->status !== self::STATUS_CANCELLED) {
                $this->status = self::STATUS_COMPLETED;
            }
            if (is_null($this->completed_at)) {
                $this->completed_at = now();
            }
        } elseif ($this->status === self::STATUS_DRAFT && $this->opened_count > 0) {
            $this->status = self::STATUS_IN_PROGRESS;
            if (is_null($this->started_at)) {
                $this->started_at = now();
            }
        }

        if ($save) {
            $this->save();
        }
    }
}