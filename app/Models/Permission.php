<?php

namespace App\Models;



class Permission extends GlobalModel
{
     protected $table = 'permissions';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $fillable = array('route_name', 'permission_group_id');

    // Log Activity


    /**
     * العلاقة العكسية: الصلاحية تنتمي إلى مجموعة صلاحيات محددة.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function permission_group()
    {
        return $this->belongsTo(PermissionGroup::class, 'permission_group_id', 'id');
    }
}
