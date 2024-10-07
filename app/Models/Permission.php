<?php

namespace App\Models;



class Permission extends GlobalModel
{
     protected $table = 'permissions';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $fillable = array('route_name', 'permission_group_id');

    // Log Activity


    public function permission_group(){
        return $this->hasOne('App\Models\PermissionGroup');
    }

}
