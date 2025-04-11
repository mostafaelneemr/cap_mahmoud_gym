<?php

namespace App\Models;


use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;

class Trainee extends GlobalModel
{
    use  Notifiable,LogsActivity;

    protected $table = 'trainees';
    public $timestamps = true;
    public $primaryKey = 'id';
    protected $dates = ['created_at', 'updated_at'];
    protected $fillable = [ 'id', 'user_id','age','weight','height','membership_start','membership_end' ,'training_level','status'];


    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }



}
