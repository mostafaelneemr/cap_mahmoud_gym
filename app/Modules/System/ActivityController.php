<?php

namespace App\Modules\System;

use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use Auth;
use Jenssegers\Agent\Agent;

class ActivityController extends SystemController
{
    protected $activity_log_service;

    public function __construct(ActivityLogService $activity_log_service)
    {
        parent::__construct();
        $this->activity_log_service = $activity_log_service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        if ($request->isDataTable) {
            return $this->activity_log_service->loadDataTableData();
        }
        return $this->view('activity-log.index', $this->activity_log_service->loadViewData());
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Repositories\ActivityLog\  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        return $this->view('activity-log.show', $this->activity_log_service->findById($id));
    }

}
