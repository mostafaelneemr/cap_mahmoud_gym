<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;

class ExerciseLibrary extends GlobalModel
{
    use SoftDeletes, Notifiable, LogsActivity;

    protected $table = 'exercise_libraries';
    public $timestamps = true;
    public $primaryKey = 'id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'name',
        'video_url',
        'description',
        'status',
    ];

    /**
     * Scope for active library items (eligible for selection in new workout assignments).
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Exercises assigned across workout plans linked to this library entry.
     */
    public function exercises()
    {
        return $this->hasMany(Exercise::class, 'exercise_library_id', 'id');
    }

    /**
     * Determine if this library item is active.
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Convert YouTube or Google Drive links to embeddable preview URLs.
     */
    public function getFormattedVideoUrlAttribute(): ?string
    {
        $url = $this->video_url;
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
