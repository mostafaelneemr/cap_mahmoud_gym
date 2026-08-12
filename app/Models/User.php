<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use SoftDeletes, Notifiable, LogsActivity;

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
        'user_type', // 1 mean user moderator / 2 mean trainer without permission / 3 mean trainee
        'google_id'
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


    protected static function boot()
    {
        parent::boot();

        static::saved(function ($user) {
            if ($user->isDirty('permission_group_id') || $user->isDirty('status')) {
                \Illuminate\Support\Facades\Cache::forget("user_perms_{$user->id}");
            }
        });

        static::deleting(function ($user) {
            \Illuminate\Support\Facades\Cache::forget("user_perms_{$user->id}");

            $trainees = Trainee::where('user_id', $user->id)->get();
            foreach ($trainees as $trainee) {
                $user->isForceDeleting() ? $trainee->forceDelete() : $trainee->delete();
            }

            AuthSession::where('user_id', $user->id)->delete();
        });

        static::restoring(function ($user) {
            Trainee::onlyTrashed()->where('user_id', $user->id)->get()->each->restore();
        });
    }

    public static function UserPerms($userID)
    {
        return \Illuminate\Support\Facades\Cache::remember("user_perms_{$userID}", 1800, function () use ($userID) {
            $user = User::select('id', 'permission_group_id')->find($userID);
            return $user && $user->permission_group_id 
                ? \App\Models\Permission::where('permission_group_id', $user->permission_group_id)->pluck('route_name')
                : collect([]);
        });
    }

    public function permission_group()
    {
        return $this->belongsTo('App\Models\PermissionGroup', 'permission_group_id', 'id');
    }

    public function permissionList()
    {
        return $this->hasManyThrough('App\Models\Permission', 'App\Models\PermissionGroup', 'id', 'permission_group_id', 'permission_group_id');
    }

    public function trainer()
    {
        return $this->hasOne(Trainee::class, 'user_id', 'id');
    }

    public function getTrainerAttribute()
    {
        return $this->trainer()->first();
    }
}
