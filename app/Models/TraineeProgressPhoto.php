<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TraineeProgressPhoto extends Model
{
    protected $table = 'trainee_progress_photos';
    public $timestamps = true;

    protected $fillable = [
        'checkin_id',
        'photo_path',
        'caption',
    ];

    protected $appends = ['photo_url'];

    public function checkin()
    {
        return $this->belongsTo(TraineeProgressCheckin::class, 'checkin_id', 'id');
    }

    public function getPhotoUrlAttribute()
    {
        if (empty($this->photo_path)) {
            return asset('assets/media/avatars/blank.png');
        }

        if (filter_var($this->photo_path, FILTER_VALIDATE_URL)) {
            return $this->photo_path;
        }

        return asset('storage/' . $this->photo_path);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($photo) {
            if ($photo->photo_path && Storage::disk('public')->exists($photo->photo_path)) {
                Storage::disk('public')->delete($photo->photo_path);
            }
        });
    }
}
