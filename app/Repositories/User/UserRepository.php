<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class UserRepository extends BaseRepository
{

    protected $modeler = User::class;

    /**
     * @param $data
     * @return array
     */
    public function getDataTableQuery()
    {

        return $this->modeler
         ->select([
             'user.id',
             'permission_groups.name as permission_group_name',
             'user.permission_group_id',
             'user.status',
             "user.name",
             'user.email',
             'user.mobile',
             'user.created_at'])
            ->leftjoin('permission_groups','permission_groups.id','user.permission_group_id');
    }
    public function get(array $columns = [ '*' ])
    {
        return $this->modeler
            ->select([
                'user.id as id',
                DB::raw("name"),
           ])->get();
    }
}
