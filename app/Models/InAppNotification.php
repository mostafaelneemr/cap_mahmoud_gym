<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InAppNotification extends Model
{
    use HasFactory;

    public const TYPE_GENERAL = 'general';
    public const TYPE_COACH_NEW_LOAD_ADJUSTMENT = 'coach_new_load_adjustment';
    public const TYPE_TRAINEE_LOAD_ADJUSTMENT_REVIEWED = 'trainee_load_adjustment_reviewed';
    public const TYPE_TRAINEE_WORKOUT_UPDATED = 'trainee_workout_updated';
    public const TYPE_TRAINEE_NUTRITION_UPDATED = 'trainee_nutrition_updated';
    public const TYPE_COACH_NEW_PROGRESS = 'coach_new_progress';
    public const TYPE_TRAINEE_COACH_NOTES_UPDATED = 'trainee_coach_notes_updated';
    public const TYPE_TRAINEE_SUBSCRIPTION_EXPIRING = 'trainee_subscription_expiring';

    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'action_url',
        'icon',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function markAsRead(): bool
    {
        if (is_null($this->read_at)) {
            $this->read_at = now();
            return $this->save();
        }
        return true;
    }

    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }
}