<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TraineeProgressCheckin extends Model
{
    protected $table = 'trainee_progress_checkins';
    public $timestamps = true;

    protected $fillable = [
        'trainee_id',
        'coach_id',
        'checkin_date',
        'weight',
        'notes',
        'coach_notes',
    ];

    protected $casts = [
        'checkin_date' => 'date',
        'weight'       => 'decimal:2',
    ];

    public function photos()
    {
        return $this->hasMany(TraineeProgressPhoto::class, 'checkin_id', 'id');
    }

    public function trainee()
    {
        return $this->belongsTo(Trainee::class, 'trainee_id', 'id');
    }

    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($checkin) {
            foreach ($checkin->photos as $photo) {
                $photo->delete(); // Triggers TraineeProgressPhoto deleting boot listener to remove physical file
            }
        });
    }
}
