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
            ->with('permission_group:id,name')
            ->select(['id', 'permission_group_id', 'status', 'name', 'email', 'mobile', 'created_at'])
            ->where(function ($query) {
                $query->where('user_type', 1)
                    ->orWhereNull('user_type');
            });
    }
    public function get(array $columns = [ '*' ])
    {
        return $this->modeler
            ->select([
                'user.id as id',
                DB::raw("name"),
           ])->get();
    }

    public function getUserTrainee()
    {
        return $this->modeler->where('user_type', 2)->get();
    }
}
