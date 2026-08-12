<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;

class NutritionPlan extends GlobalModel
{
    use Notifiable, LogsActivity;

    protected $table = 'nutrition_plans';
    public $timestamps = true;
    public $primaryKey = 'id';

    protected $fillable = [
        'trainee_id',
        'name',
        'description',
        'status'
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function trainee()
    {
        return $this->belongsTo(Trainee::class, 'trainee_id', 'id');
    }

    public function user()
    {
        return $this->hasOneThrough(User::class, Trainee::class, 'id', 'id', 'trainee_id', 'user_id');
    }
}
