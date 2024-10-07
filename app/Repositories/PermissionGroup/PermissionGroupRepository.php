<?php

namespace App\Repositories\PermissionGroup;

use App\Models\PermissionGroup;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class PermissionGroupRepository extends BaseRepository
{

    protected $modeler = PermissionGroup::class;

    /**
     * @param $data
     * @return array
     */
    public function getDataTableQuery()
    {
        return $this->modeler
         ->select([
             'permission_groups.id',
             'permission_groups.name',
             "permission_groups.updated_at",
             DB::raw( "(SELECT COUNT(*) FROM `user` WHERE permission_group_id = `permission_groups`.`id`) as `count`" )
         ])->where('system','niceone_admin');
    }
    public function get(array $columns = [ '*' ])
    {
        return $this->modeler->where('system','niceone_admin')->get($columns);
    }
}
