<?php

namespace App\Repositories\AuthSession;

use App\Models\AuthSession;
use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class AuthSessionRepository extends BaseRepository
{

    protected $modeler = AuthSession::class;

    /**
     * @param $data
     * @return array
     */
    public function getDataTableQuery($user_id)
    {
        $query = $user_id ? $this->modeler->where('user_id',$user_id):$this->modeler;
        return $query->where('guard_name','user')
         ->select([
             'id',
             'ip',
             'guard_name',
             'user_id',
             'user_agent',
             'created_at',
             'updated_at',
         ]) ;
    }
}
