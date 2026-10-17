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
        'warmup_links' => 'array',
    ];

    protected $fillable = ['trainee_id', 'day_name', 'warmup', 'warmup_links', 'post_workout', 'status'];

    /** Maximum number of warmup video links per workout day. */
    public const MAX_WARMUP_LINKS = 5;

    /**
     * Normalize warmup links input: trimmed strings, no empty values,
     * at most MAX_WARMUP_LINKS items. Returns null when nothing is left
     * so the JSON column stays NULL instead of "[]".
     */
    public static function normalizeWarmupLinks(mixed $value): ?array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = is_array($decoded) ? $decoded : [$value];
        }

        if (!is_array($value)) {
            return null;
        }

        $links = array_filter(
            array_map(fn ($link) => is_scalar($link) ? trim((string) $link) : '', $value),
            fn ($link) => $link !== ''
        );

        $links = array_slice(array_values($links), 0, self::MAX_WARMUP_LINKS);

        return empty($links) ? null : $links;
    }

    /** Always returns a clean list of warmup links (never null). */
    public function getWarmupLinksListAttribute(): array
    {
        return self::normalizeWarmupLinks($this->warmup_links) ?? [];
    }

    public function getDayNameAttribute($value): ?string
    {
        if ($value === null) {
            return null;
        }
        return trim(str_replace('[INACTIVE]', '', (string)$value));
    }

    public function setDayNameAttribute($value): void
    {
        $clean = trim(str_replace('[INACTIVE]', '', (string)$value));
        $currentRaw = $this->attributes['day_name'] ?? '';
        if (str_starts_with((string)$currentRaw, '[INACTIVE]')) {
            $this->attributes['day_name'] = '[INACTIVE] ' . $clean;
        } else {
            $this->attributes['day_name'] = $clean;
        }
    }

    public function getStatusAttribute($value): string
    {
        if ($value === 'archived') {
            return 'archived';
        }
        $rawDayName = $this->attributes['day_name'] ?? '';
        if (str_starts_with((string)$rawDayName, '[INACTIVE]')) {
            return 'inactive';
        }
        return $value ?: 'active';
    }

    public function setStatusAttribute($value): void
    {
        if ($value === 'inactive') {
            $this->attributes['status'] = 'active';
            $rawDayName = $this->attributes['day_name'] ?? '';
            if (!str_starts_with((string)$rawDayName, '[INACTIVE]')) {
                $this->attributes['day_name'] = '[INACTIVE] ' . trim((string)$rawDayName);
            }
        } elseif ($value === 'active') {
            $this->attributes['status'] = 'active';
            $rawDayName = $this->attributes['day_name'] ?? '';
            $this->attributes['day_name'] = trim(str_replace('[INACTIVE]', '', (string)$rawDayName));
        } else {
            $this->attributes['status'] = $value;
        }
    }

    public function getDisplayDayNameAttribute(): ?string
    {
        return $this->day_name;
    }

    public function getDisplayWarmupAttribute(): ?string
    {
        return $this->warmup;
    }

    public function getDisplayPostWorkoutAttribute(): ?string
    {
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
