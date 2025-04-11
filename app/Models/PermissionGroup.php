<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;


class PermissionGroup extends GlobalModel
{
    use SoftDeletes;
     protected $table = 'permission_groups';
     public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $fillable = ['name', 'is_supervisor', 'whitelist_ip', 'default_route', 'new_admin_default_route', 'system'];


    public function permission()
    {
        return $this->hasMany('App\Models\Permission');
    }

    public function user()
    {
        return $this->hasMany('App\Models\User');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i');
    }

}
