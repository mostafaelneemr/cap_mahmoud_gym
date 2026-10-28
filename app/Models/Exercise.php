<?php

namespace App\Models;


use App\Services\ExerciseMachineImageService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;

class Exercise extends GlobalModel
{
    use SoftDeletes, Notifiable, LogsActivity;

    protected $table = 'exercises';
    public $timestamps = true;
    public $primaryKey = 'id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'workout_plan_id',
        'exercise_library_id',
        'type',
        'name',
        'sets',
        'reps',
        'rest',
        'internal_weight',
        'tempo',
        'link',
        'notes_en',
        'machine_image',
        'status'
    ];

    public function scopeWarmup($query)
    {
        return $query->where('type', 'warmup');
    }

    public function scopeMain($query)
    {
        return $query->where('type', 'main');
    }

    public function scopePostWorkout($query)
    {
        return $query->where('type', 'post_workout');
    }

    /** Public URL of the machine / equipment photo (or null). */
    public function getMachineImageUrlAttribute(): ?string
    {
        if (empty($this->machine_image)) {
            return null;
        }

        return Storage::disk(ExerciseMachineImageService::DISK)->url($this->machine_image);
    }

    protected static function booted()
    {
        // Soft-deleted exercises keep their image (they can be restored);
        // only a permanent delete removes the file from storage.
        static::forceDeleted(function (Exercise $exercise) {
            app(ExerciseMachineImageService::class)->delete($exercise->machine_image);
        });
    }

    /** Maximum number of recommended weights per exercise. */
    public const MAX_WEIGHTS = 4;

    /** Physical size of the `internal_weight` varchar column. */
    public const INTERNAL_WEIGHT_COLUMN_LENGTH = 100;

    /**
     * Normalize any weight input (array, JSON string, legacy plain string) into
     * a clean list: trimmed, no empty values, at most MAX_WEIGHTS items.
     */
    public static function normalizeWeights(mixed $value): array
    {
        if (is_null($value)) {
            return [];
        }

        if (is_string($value)) {
            $trimmed = trim($value);
            if ($trimmed === '') {
                return [];
            }
            $decoded = json_decode($trimmed, true);
            // Legacy rows store a single plain value such as "20kg".
            $value = is_array($decoded) ? $decoded : [$trimmed];
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        $weights = array_filter(
            array_map(fn ($w) => is_scalar($w) ? trim((string) $w) : '', $value),
            fn ($w) => $w !== ''
        );

        return array_slice(array_values($weights), 0, self::MAX_WEIGHTS);
    }

    /**
     * Stored as a JSON array in the existing varchar(100) column.
     * Reading always yields an array; writing accepts array or string.
     */
    protected function internalWeight(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => self::normalizeWeights($value),
            set: function ($value) {
                $weights = self::normalizeWeights($value);
                if (empty($weights)) {
                    return null;
                }

                $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
                $json = json_encode($weights, $flags);

                // Safety net: never exceed the column length (drop trailing items).
                while (count($weights) > 1 && mb_strlen($json) > self::INTERNAL_WEIGHT_COLUMN_LENGTH) {
                    array_pop($weights);
                    $json = json_encode($weights, $flags);
                }

                return mb_substr($json, 0, self::INTERNAL_WEIGHT_COLUMN_LENGTH) === $json ? $json : null;
            }
        );
    }

    /** Human readable weights, e.g. "10kg / 20kg / 30kg". */
    public function getInternalWeightDisplayAttribute(): ?string
    {
        $weights = $this->internal_weight;
        return !empty($weights) ? implode(' / ', $weights) : null;
    }

    /**
     * إرجاع الوزن المحدد لمجموعة معينة (1-indexed)
     */
    public function getWeightForSet(int $setNumber): ?string
    {
        $weights = $this->internal_weight;
        if (empty($weights) || $setNumber < 1 || $setNumber > self::MAX_WEIGHTS) {
            return null;
        }

        $idx = $setNumber - 1;
        if (isset($weights[$idx]) && $weights[$idx] !== '') {
            return $weights[$idx];
        }

        // إذا كان هناك وزن وحيد مسجل، فإنه ينطبق افتراضياً على كافة المجموعات
        if (count($weights) === 1 && isset($weights[0])) {
            return $weights[0];
        }

        return null;
    }

    /**
     * تعيين أو تحديث الوزن الخاص بمجموعة معينة مع الحفاظ على أوزان باقي المجموعات
     */
    public function setWeightForSet(int $setNumber, ?string $weight): void
    {
        if ($setNumber < 1 || $setNumber > self::MAX_WEIGHTS) {
            return;
        }

        $weights = $this->internal_weight ?? [];
        $targetIdx = $setNumber - 1;

        // إذا كان هناك وزن مفرد سابق، وتعديلنا لمجموعة تالية، نقوم بملء المجموعات السابقة بالوزن الأصلي
        if (count($weights) === 1 && $targetIdx > 0) {
            $baseSingle = $weights[0];
            $expanded = [];
            for ($i = 0; $i < $targetIdx; $i++) {
                $expanded[$i] = $baseSingle;
            }
            $weights = $expanded;
        }

        // ملء أي فجوات سابقة قبل المجموعة المستهدفة
        for ($i = 0; $i < $targetIdx; $i++) {
            if (!isset($weights[$i]) || $weights[$i] === '') {
                $weights[$i] = !empty($weights) ? reset($weights) : ($weight ?? '');
            }
        }

        if (!empty($weight)) {
            $weights[$targetIdx] = trim($weight);
        } else {
            unset($weights[$targetIdx]);
        }

        ksort($weights);
        $this->internal_weight = array_slice(array_values($weights), 0, self::MAX_WEIGHTS);
    }

    /**
     * إرجاع العدد الفعلي للمجموعات المقررة لهذا التمرين (بحد أقصى 4)
     */
    public function getTotalSetsCount(): int
    {
        $sets = (int) ($this->sets ?: 1);
        return max(1, min(self::MAX_WEIGHTS, $sets));
    }


    public function getDisplayNameAttribute(): ?string
    {
        return $this->name;
    }

    public function workoutPlan()
    {
        return $this->belongsTo(WorkoutPlan::class, 'workout_plan_id', 'id');
    }

    /**
     * Central exercise library relationship (if linked).
     */
    public function exerciseLibrary()
    {
        return $this->belongsTo(ExerciseLibrary::class, 'exercise_library_id', 'id');
    }

    /**
     * Determine if this exercise is linked to the central library.
     */
    public function getIsLinkedToLibraryAttribute(): bool
    {
        return !empty($this->exercise_library_id);
    }

    /**
     * Resolve current video link:
     * If linked to an exercise library record with a non-empty video URL,
     * resolve from the central library; otherwise preserve the legacy local link.
     */
    public function getLinkAttribute($value): ?string
    {
        if (!empty($this->exercise_library_id)) {
            $library = $this->relationLoaded('exerciseLibrary')
                ? $this->getRelation('exerciseLibrary')
                : $this->exerciseLibrary;

            if ($library && !empty($library->video_url)) {
                return $library->video_url;
            }
        }

        return $value;
    }

    /**
     * تقارير تعديل أوزان التمارين المرتبطة بهذا التمرين
     */
    public function loadAdjustments()
    {
        return $this->hasMany(TraineeLoadAdjustment::class, 'exercise_id', 'id')->orderBy('created_at', 'desc');
    }

    public function getFormattedLinkAttribute()
    {
        $url = $this->link;
        if (empty($url)) {
            return null;
        }

        if (str_contains($url, 'drive.google.com')) {
            preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $url, $matches);
            if (isset($matches[1])) {
                return "https://drive.google.com/file/d/" . $matches[1] . "/preview";
            }
        }

        if (str_contains($url, 'youtu.be') || str_contains($url, 'youtube.com')) {
            preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/', $url, $matches);
            if (isset($matches[1])) {
                return "https://www.youtube.com/embed/" . $matches[1];
            }
        }

        return $url;
    }
}
