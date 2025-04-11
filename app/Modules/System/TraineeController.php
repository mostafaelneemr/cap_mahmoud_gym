<?php

namespace App\Modules\System;

use App\Http\Requests\ProfileFormRequest;
use App\Services\TraineeService;
use Illuminate\Http\Request;
use App\Http\Requests\UserFormRequest;
use Illuminate\Support\Facades\Auth;

class TraineeController extends SystemController
{

    protected $traineeService;

    public function __construct(TraineeService $traineeService)
    {
        parent::__construct();
        $this->traineeService = $traineeService;
    }

    public function index(Request $request)
    {
        if ($request->isDataTable) {
            return $this->traineeService->loadDataTableData();
        }
        return $this->view('trainee.index', $this->traineeService->loadViewData());
    }

    public function create()
    {
        return $this->view('trainee.create', $this->traineeService->create());
    }

    public function store(Request $request)
    {
        $trainer = $this->traineeService->store($request);
        if ($trainer) {
            flash_msg('success',__('Data Added successfully'));
            return $this->success( __( 'Data added successfully' ),
                [  'url' => route( 'system.trainee.index' )] );
        } else {
            return $this->fail(__( 'Sorry, we could not add the data' ) );
        }
    }


    public function show($id,Request $request)
    {
        return $this->view('user.show', $this->traineeService->findById($id));
    }

    public function edit($id)
    {
        return $this->view('user.create', $this->traineeService->edit($id));

    }
    public function editProfile()
    {
        return $this->view('user.edit-profile', $this->traineeService->updateProfile(auth()->user()->id));
    }

    public function showProfile()
    {
        return $this->view('user.show-profile', $this->traineeService->findById(Auth::id()));
    }

    public function updateProfile(ProfileFormRequest $request)
    {
        $update = $this->traineeService->update($request,auth()->id());
        if ($update) {
            flash_msg('success',__( 'Profile Updated successfully' ));
            return $this->success( __( 'Profile Updated successfully' ));
        } else {
            return $this->fail(__( 'Sorry, we could not Update the data' ) );
        }

    }

    public function update(UserFormRequest $request, $id)
    {
        $update = $this->traineeService->update($request, $id);
        if ($update) {
            flash_msg('success',__( 'Data Updated successfully' ));
            return $this->success( __( 'Data Updated successfully' ),
                ['url'=>route('system.user.show',$id)]);
        } else {
            return $this->fail(__( 'Sorry, we could not Update the data' ) );
        }

    }
}
