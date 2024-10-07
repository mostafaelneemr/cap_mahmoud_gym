<?php

namespace App\Repositories\ActivityLog;

use App\Models\AuthSession;
use App\Repositories\BaseRepository;
use Spatie\Activitylog\Models\Activity;

class ActivityLogRepository extends BaseRepository
{

    protected $modeler = Activity::class;

    /**
     * @param $data
     * @return array
     */
    public function getDataTableQuery($id = null)
    {
        $data =  $this->modeler ->select([
                'id',
                'log_name',
                'event',
                'subject_id',
                'subject_type',
                'causer_id',
                'causer_type',
                'created_at'
            ])->where('log_name','niceone_admin');
        if ($id){
            $data = $data->where('causer_id',$id);
        }
        return $data ;
    }

}
