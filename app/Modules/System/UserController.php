<?php

namespace App\Modules\System;

use App\Http\Requests\ProfileFormRequest;
use App\Services\AuthSessionService;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Http\Requests\UserFormRequest;
use Illuminate\Support\Facades\Auth;

class UserController extends SystemController
{

    protected $user_service ,$auth_session_service;

    public function __construct(UserService $user_service, AuthSessionService $auth_session_service)
    {
        parent::__construct();
        $this->user_service = $user_service;
        $this->auth_session_service = $auth_session_service;

    }

    public function index(Request $request)
    {
        if ($request->isDataTable) {
            return $this->user_service->loadDataTableData();
        }
        return $this->view('user.index', $this->user_service->loadViewData());
    }

    public function create()
    {
        return $this->view('user.create', $this->user_service->create());
    }


    public function store(UserFormRequest $request)
    {
        $user = $this->user_service->store($request);
        if ($user) {
            flash_msg('success',__('Data Added successfully'));
            return $this->success( __( 'Data added successfully' ),
                [  'url' => route( 'system.user.create' )] );
        } else {
            return $this->fail(__( 'Sorry, we could not add the data' ) );
        }
    }


    public function show($id,Request $request)
    {
        return $this->view('user.show', $this->user_service->findById($id));
    }

    public function edit($id)
    {
        return $this->view('user.create', $this->user_service->edit($id));

    }
    public function editProfile()
    {
        return $this->view('user.edit-profile', $this->user_service->updateProfile(auth()->user()->id));
    }

    public function showProfile()
    {
        return $this->view('user.show-profile', $this->user_service->findById(Auth::id()));
    }

    public function updateProfile(ProfileFormRequest $request)
    {
        $update = $this->user_service->update($request,auth()->id());
        if ($update) {
            flash_msg('success',__( 'Profile Updated successfully' ));
            return $this->success( __( 'Profile Updated successfully' ));
        } else {
            return $this->fail(__( 'Sorry, we could not Update the data' ) );
        }

    }

    public function update(UserFormRequest $request, $id)
    {
        $update = $this->user_service->update($request, $id);
        if ($update) {
            flash_msg('success',__( 'Data Updated successfully' ));
            return $this->success( __( 'Data Updated successfully' ),
                ['url'=>route('system.user.show',$id)]);
        } else {
            return $this->fail(__( 'Sorry, we could not Update the data' ) );
        }

    }

    public function getUserActivityLog($id)
    {
        return $this->user_service->loadActivityLogDetails($id);
    }

    public function getAuthSession($id)
    {
        return $this->user_service->loadAuthSessionDetails($id);
    }

    public function getUserFile($id)
    {
        return $this->user_service->loadUserFileDetails($id);
    }

    public function getUserSharedFile($id)
    {

        return $this->user_service->loadUserSharedFile($id);
    }

}
