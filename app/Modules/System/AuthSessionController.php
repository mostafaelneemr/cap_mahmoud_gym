<?php

namespace App\Modules\System;

use App\Services\AuthSessionService;

use Illuminate\Http\Request;

class AuthSessionController extends SystemController
{

    protected $auth_session_service;

    public function __construct(AuthSessionService $auth_session_service)
    {
        parent::__construct();
        $this->auth_session_service = $auth_session_service;
    }

    public function index(Request $request)
    {
        if ($request->isDataTable) {
            return $this->auth_session_service->loadDataTableData();
        }
        return $this->view('auth-session.index', $this->auth_session_service->loadViewData());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,Request $request)
    {
        return $this->auth_session_service->findById($id);
    }

    public function destroy($id ,Request $request)
    {
        $deleted = $this->auth_session_service->delete($id);

        if ($deleted) {
            return $this->success(__('Session Deleted'),
                ['url' => route('system.auth-sessions.index')]);
        }
        return ['status' => false, 'msg' => __('Session Not Deleted')];

    }

    public function authSessionForUser(Request $request)
    {
        if ($request->isDataTable) {
            return $this->auth_session_service->loadDataTableData(auth('user')->id());
        }
        return $this->view('auth-session.index', $this->auth_session_service->loadViewData());
    }


}
