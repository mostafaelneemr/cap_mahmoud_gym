<?php

namespace App\Modules\System;

use App\Models\PermissionGroup;
use App\Services\PermissionGroupService;
use App\Http\Requests\PermissionGroupFormRequest;
use Illuminate\Http\Request;
use Auth;


class PermissionGroupsController extends SystemController
{

    protected $permission_group_service;

    public function __construct(PermissionGroupService $permission_group_service)
    {
        parent::__construct();
        $this->permission_group_service = $permission_group_service;
    }

    public function index(Request $request)
    {

        if ($request->isDataTable) {
            return $this->permission_group_service->loadDataTableData();
        }
        return $this->view('permission-group.index', $this->permission_group_service->loadViewData());
    }

    public function create()
    {
        return $this->view('permission-group.create', $this->permission_group_service->create());
    }


    public function store(PermissionGroupFormRequest $request)
    {


        $permissionGroup = $this->permission_group_service->store($request);
        if ($permissionGroup) {
            flash_msg('success', __('Data Added successfully'));
            return $this->success(__('Data added successfully'),
                ['url' => route('system.permission-group.index')]);
        } else {
            return $this->fail(__('Sorry, we could not add the data'));
        }


    }


    public function show(PermissionGroup $permission_group)
    {
        return back();
    }


    public function edit($id)
    {
        return $this->view('permission-group.create', $this->permission_group_service->edit($id));
    }

    public function update(PermissionGroupFormRequest $request, $id)
    {
        $update = $this->permission_group_service->update($request, $id);

        if ($update) {
            flash_msg('success', __('Data Updated successfully'));
            return $this->success(__('Data Updated successfully'),
                ['url' => route('system.permission-group.index')]);
        } else {
            return $this->fail(__('Sorry, we could not Update the data'));
        }
    }


    public function destroy(PermissionGroup $permission_group)
    {
        return back();
    }

}
