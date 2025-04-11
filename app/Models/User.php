<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use SoftDeletes, Notifiable,LogsActivity;

    protected $table = 'user';
    public $timestamps = true;
    public $primaryKey = 'id';
    public $modelPath = 'App\Models\User';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = [
        'id',
        'name',
        'email',
        'mobile',
        'password',
        'status',
        'permission_group_id',
        'default_language',
        'two_fa_secret',
        'department_id',
        'force_reset_password',
        'user_type' // 1 mean user moderator / 2 mean trainer without permission
    ];

    //Log Activity
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $hidden = array('password', 'remember_token');


    public static function UserPerms($userID)
    {
        return User::find($userID)->permissionList->pluck('route_name');
    }

    public function permission_group()
    {
        return $this->belongsTo('App\Models\PermissionGroup', 'permission_group_id','id');
    }

    public function permissionList()
    {
        return $this->hasManyThrough('App\Models\Permission', 'App\Models\PermissionGroup', 'id', 'permission_group_id', 'permission_group_id');
    }





}
