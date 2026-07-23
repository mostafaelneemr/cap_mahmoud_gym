<?php

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;

class Exercise extends GlobalModel
{
    use SoftDeletes, Notifiable, LogsActivity;

    protected $table = 'exercises';
    public $timestamps = true;
    public $primaryKey = 'id';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = ['workout_plan_id', 'name', 'sets', 'reps', 'rest', 'internal_weight', 'tempo', 'link'];

    public function workoutPlan()
    {
        return $this->belongsTo(WorkoutPlan::class, 'workout_plan_id', 'id');
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
