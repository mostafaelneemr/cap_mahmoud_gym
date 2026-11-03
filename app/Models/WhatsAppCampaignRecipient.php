<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppCampaignRecipient extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_campaign_recipients';

    protected $fillable = [
        'campaign_id',
        'trainee_id',
        'recipient_name',
        'phone_raw',
        'phone_normalized',
        'message_rendered',
        'status',
        'opened_at',
        'confirmed_at',
        'notes',
    ];

    protected $casts = [
        'opened_at'    => 'datetime',
        'confirmed_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    public const STATUS_PENDING   = 'pending';
    public const STATUS_OPENED    = 'opened';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_SKIPPED   = 'skipped';
    public const STATUS_FAILED    = 'failed';

    public function campaign()
    {
        return $this->belongsTo(WhatsAppCampaign::class, 'campaign_id');
    }

    public function trainee()
    {
        return $this->belongsTo(Trainee::class, 'trainee_id');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_OPENED]);
    }

    /**
     * Build the official wa.me link with encoded personalized text.
     */
    public function getWaMeUrlAttribute(): ?string
    {
        if (empty($this->phone_normalized)) {
            return null;
        }

        $encodedText = rawurlencode($this->message_rendered ?? '');
        return "https://wa.me/{$this->phone_normalized}?text={$encodedText}";
    }
}

