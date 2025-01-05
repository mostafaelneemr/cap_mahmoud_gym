<?php

namespace App\Services;

use App\Filters\{CreatedAtFrom,CreatedAtTo,Email,Id,Name,PermissionGroupId};
use App\Repositories\ActivityLog\ActivityLogRepository;
use App\Repositories\PermissionGroup\PermissionGroupRepository;
use App\Repositories\User\UserRepository;
use Datatables;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService extends BaseService
{
    protected $user_repository, $activity_log_repository, $permission_group_repository, $activity_log_service, $auth_session_service;

    public function __construct(UserRepository $user_repository,
                                ActivityLogRepository $activity_log_repository,
                                PermissionGroupRepository $permission_group_repository,
                                AuthSessionService $auth_session_service,
                                ActivityLogService $activity_log_service
    )
    {
        parent::__construct();
        $this->user_repository = $user_repository;
        $this->permission_group_repository = $permission_group_repository;
        $this->activity_log_service = $activity_log_service;
        $this->activity_log_repository = $activity_log_repository;
        $this->auth_session_service = $auth_session_service;

    }

    public function findById($id)
    {
        $activityLogData = $this->activity_log_service->loadViewData();
        $activityLogData['datatableURL'] = route('system.get-user-activity-log', $id);
        $activityLogData['datatableID'] = 'activity-log';

        $authSessionData = $this->auth_session_service->loadViewData();
        $authSessionData['datatableURL'] = route('system.get-auth-session', $id);
        $authSessionData['datatableID'] = 'auth-session';

        $datatablesData = [
            'authSessionData' => $authSessionData,
            'activityLogData' => $activityLogData,
        ];

        $this->clearRetunData();


        $this->pageTitle('View User');
        $this->breadcrumb('User', 'system.user.index');

        $this->otherData($datatablesData);


        $this->otherData(['result' => $this->user_repository->find($id)]);
        $this->otherData(['UserArray' => $this->user_repository->get()->toArray()]);

        return $this->retunData;
    }

    public function userArray()
    {
        return array_column($this->user_repository->get()->toArray(), 'name', 'id');
    }

    public function loadViewData(): array
    {
        $this->pageTitle('User List');
        $this->tableColumns([
            __('ID'),
            __('Name'),
            __('Telephone'),
            __('Permission'),
            __('Status'),
            __('Created'),
            __('Action')

        ]);

        $this->jsColumns([
            'id' => 'user.id',
            'name' => 'user.name',
            'mobile' => 'user.mobile',
            'permission_group' => 'permission_groups.name',
            'status' => 'user.status',
            'created_at' => 'user.date_added',
            'action' => 'action'
        ]);

        $this->breadcrumb('User', 'system.user.index');
        $this->addButton('system.user.create');
        $this->filterIgnoreColumns(['action', 'status', 'department_id', 'created_at']);
        $this->otherData(['PermissionGroup' => (new PermissionGroupService($this->permission_group_repository))->permissionArray()]);

        return $this->retunData;
    }


    public function loadDataTableData()
    {
        $query = $this->user_repository->getDataTableQuery();

        if (!empty(request()->columns[1]['search']['value'])) {
            $query = $query->where('name', 'like', '%' . request()->columns[1]['search']['value'] . '%');
        }

        $eloquentData = app(Pipeline::class)
            ->send($query)
            ->through([
                Id::class,
                PermissionGroupId::class,
                Name::class,
                Email::class,
                CreatedAtFrom::class,
                CreatedAtTo::class
            ])->thenReturn();
        return Datatables::eloquent($eloquentData)
            ->addColumn('id', '{{$id}}')
            ->addColumn('name', function ($data) {
                if ($data->name)
                    return datatable_links('system.user.show', route('system.user.show', $data->id), $data->name);
            })
            ->editColumn('mobile', '{{$mobile}}')
            ->editColumn('status', function ($data) {
                return status_icon($data->status);
            })

            ->addColumn('permission_group', function ($data) {
                if (isset($data->permission_group->id)) {
                    return datatable_links('system.permission-group.edit', route('system.permission-group.edit', $data->permission_group->id), $data->permission_group_name);
                }
            })
            ->addColumn('created_at', function ($data) {
                if ($data->created_at)
                    return $data->created_at->format('Y-m-d H:i');
                return '--';

            })
            ->editColumn('action', function ($data) {
                 $this->actionButtons(datatable_menu_show(route('system.user.show', $data->id), 'system.user.show'));
                $this->actionButtons(datatable_menu_edit(route('system.user.edit', $data->id), 'system.user.edit'));
                return $this->actionButtonsRender($this->user_repository->modelPath(), $data->id);
            })->escapeColumns([])
            ->make(true);
    }

    public function create(): array
    {
        $this->pageTitle('Create User');
        $this->breadcrumb('User', 'system.user.index');
        $this->otherData([
            'PermissionGroup' => (new PermissionGroupService($this->permission_group_repository))->permissionArray(),
            'telephone_code' => '+20',
            'code' => 'eg'
        ]);
        return $this->retunData;
    }

    public function edit($id): array
    {
        $user = $this->user_repository->find($id);

        $this->pageTitle('Update User');
        $this->breadcrumb('User', 'system.user.index');

        $this->otherData(['result' => $user]);

        $this->otherData([
            'PermissionGroup' => (new PermissionGroupService($this->permission_group_repository))->permissionArray(),
            'telephone' => strlen($user->mobile) < 12 ? $user->mobile : substr($user->mobile, 3),
            'telephone_code' => strlen($user->mobile) < 12 ? '+20' : substr($user->mobile, 0, 3),
            'code' => $this->getCode($user->mobile)

        ]);

        return $this->retunData;
    }

    public function updateProfile($id): array
    {
        $user = $this->user_repository->find($id);
        $this->pageTitle('Update Profile');
        $this->breadcrumb('Profile', 'system.user.index');

        $this->otherData(['result' => $user]);

        return $this->retunData;
    }

    /**
     * @param $request
     */
    public function store($request)
    {
        DB::beginTransaction();

        try {
            $theRequest = $request->all();

            if ($request->password) {
                $theRequest['password'] = $this->userPassword($theRequest['password']);
            } else {
                unset($theRequest['password']);
            }

            if ($request->telephone)
            $theRequest['mobile'] = fixMobileNumber($request->telephone);

            $user = $this->user_repository->store($theRequest);
            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollback();
            errorLog($e->getMessage());
            return false;
        }

    }

    /**
     * @param $request
     * @param $id
     *
     */
    public function update($request, $id)
    {
        DB::beginTransaction();

        try {
            $theRequest = $request->all();
            if ($request->file('image')) {
                $theRequest['image'] = $this->uploadFileS3($request->image, 'image/user');
            } else {
                unset($theRequest['image']);
            }

            if ($request->password) {
                $theRequest['password'] = $this->userPassword($theRequest['password']);
            } else {
                unset($theRequest['password']);
            }
            if ($request->telephone)
                $theRequest['mobile'] = fixMobileNumber($request->telephone);
            $update = $this->user_repository->update($theRequest, $id);
            DB::commit();
            return $update;
        } catch (\Exception $e) {
            DB::rollback();
            errorLog($e->getMessage());
            return false;
        }

    }


    public function userPassword($password): string
    {
        return Hash::make($password);
    }

    public function loadActivityLogDetails($id)
    {
        return $this->activity_log_service->loadDataTableData($id);

    }

    public function loadAuthSessionDetails($id)
    {
        return $this->auth_session_service->loadDataTableData($id);

    }
}
