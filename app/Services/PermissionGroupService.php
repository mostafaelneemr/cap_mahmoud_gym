<?php

namespace App\Services;

use App\Filters\CreatedAtFrom;
use App\Filters\CreatedAtTo;
use App\Filters\CustomerId;
use App\Filters\Email;
use App\Filters\Id;
use App\Filters\Name;
use App\Filters\PermissionGroupId;
use App\Filters\Status;
use App\Filters\UserId;
use App\Filters\Username;
use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Repositories\PermissionGroup\PermissionGroupRepository;
use App\Repositories\User\UserRepository;
use Carbon\Carbon;
use Datatables;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class PermissionGroupService extends BaseService
{
    protected $permission_group_repository;

    public function __construct(PermissionGroupRepository $permission_group_repository)
    {
        parent::__construct();
        $this->permission_group_repository = $permission_group_repository;
    }

    /**
     * @param $id
     */
    public function findById($id): array
    {
        $user = $this->permission_group_repository->find($id);
        $this->pageTitle('Show Permission');
        $this->breadcrumb('User', 'system.permission-group.index');
        $this->viewData['result'] = $user;
        return $this->viewData;
    }

    public function permissionArray()
    {
        return array_column($this->permission_group_repository->get()->toArray(), 'name', 'id');
    }

    public function loadViewData(): array
    {
        $this->pageTitle('Permission Group List');
        $this->tableColumns([
            __('ID'),
            __('Name'),
            __('Num. User'),
            __('Last Update'),
            __('Action')]);

        $this->jsColumns([
            'id' => 'permission_groups.id',
            'name' => 'permission_groups.name',
            'count' => 'count',
            'updated_at' => 'permission_groups.updated_at',
            'action' => 'action'
        ]);
        $this->breadcrumb('User');
        $this->addButton('system.permission-group.create');
        $this->filterIgnoreColumns(['action', 'count', 'updated_at']);
        return $this->retunData;
    }


    /**
     * @return mixed
     */
    public function loadDataTableData()
    {
        $query = $this->permission_group_repository->getDataTableQuery();
        $eloquentData = app(Pipeline::class)
            ->send($query)
            ->through([
                Id::class,
                Name::class,
            ])->thenReturn();
        return Datatables::eloquent($eloquentData)
            ->addColumn('id', '{{$id}}')
             ->editColumn('name', function ($data) {
                  return html_entity_decode($data->name);
             })
            ->addColumn('count', '{{$count}}')
            ->addColumn('updated_at', '{{$updated_at}}')
            ->editColumn('action', function ($data) {
                $this->actionButtons(datatable_menu_edit(route('system.permission-group.edit', $data->id),'system.permission-group.edit'));
                return $this->actionButtonsRender($this->permission_group_repository->modelPath(), $data->id);
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function create(): array
    {
        $this->pageTitle('Create User Permission');
        $this->breadcrumb('User');
        $this->breadcrumb('Permission Group', 'system.permission-group.index');
        $this->otherData([
            'permissions' => $this->permissions(),
            'routes' => $this->getMianRoutes()
        ]);
        return $this->retunData;
    }

    public function edit($id): array
    {
        $permission_group = $this->permission_group_repository->find($id);
        $this->pageTitle('Update User Permission');
        $this->breadcrumb('User');
        $this->breadcrumb('Permission Group', 'system.permission-group.index');
        $this->otherData(['permissions' => $this->permissions(),
            'routes' => $this->getMianRoutes(),
            'currentpermissions' => $permission_group->permission()->get()->pluck('route_name')->toArray(),
            'permission_group' => $permission_group
        ]);
        return $this->retunData;
    }

    /**
     * @param $request
     */
    public function store($request)
    {
        DB::beginTransaction();
        try {
            $permissions = array();
            $perms = recursiveFind($this->permissions(), 'permissions');
            foreach ($perms as $val) {
                foreach ($val as $key => $oneperm) {
                    $permissions[$key] = $oneperm;
                }
            }

            $coll = new Collection();

            $requestData = $request->all();
            $requestData['system'] = 'niceone_admin';
            if ($row = $this->permission_group_repository->store($requestData)) {
                array_map(function ($oneperm) use ($permissions, $row, &$coll) {
                    foreach ($permissions[$oneperm] as $oneroute) {
                        $coll->push(new Permission(['route_name' => $oneroute, 'permission_group_id' => $row->id]));
                    }
                }, $request->all()['permissions']);
                $row->permission()->insert($coll->toArray());
                DB::commit();
                return true;
            }
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
        $permission_group = $this->permission_group_repository->find($id);
        $permissions = array();
        $perms = recursiveFind($this->permissions(), 'permissions');
        foreach ($perms as $val) {
            foreach ($val as $key => $oneperm) {
                $permissions[$key] = $oneperm;
            }
        }
        $requestData = $request->all();
        DB::beginTransaction();
        try {
            if ($request->only(['permissions'])['permissions'] !== null) {
                $coll = new Collection();
                array_map(function ($oneperm) use ($permissions, &$coll, $permission_group) {
                    foreach ($permissions[$oneperm] as $oneroute) {
                        $coll->push(new Permission(['route_name' => $oneroute, 'permission_group_id' => $permission_group->id]));
                    }
                }, $request->all()['permissions']);
            }
            $attributes = [];
            $attributes['old'] = json_encode($permission_group->permission()->select(['route_name', 'permission_group_id'])->get());

            $permission_group->update($requestData);
            $permission_group->permission()->delete();
            if (isset($coll) && $coll->count()) {
                $permission_group->permission()->insert($coll->toArray());
            }
            $attributes['new'] = json_encode($permission_group->permission()->select(['route_name', 'permission_group_id'])->get());
            activity()
                ->performedOn($permission_group)
                ->causedBy(auth()->user())
                ->withProperties(['name' => $permission_group->name, 'attributes' => $attributes])
                ->log('Update');
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            errorLog($e->getMessage());
            return false;
        }

    }

    public function permissions($permission = false)
    {
        $permissions = \Illuminate\Support\Facades\File::getRequire('../app/Modules/System/Permissions.php');
        return $permission ? isset($permissions[$permission]) ? $permissions[$permission] : false : $permissions;
    }

    function getMianRoutes()
    {
        return [
            __('Dashboard') => 'system.dashboard',
        ];
    }
}
