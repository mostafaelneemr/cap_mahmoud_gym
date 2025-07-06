<?php

namespace App\Services;

use App\Repositories\Trainee\TraineeRepository;
use App\Repositories\User\UserRepository;
use Datatables;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TraineeService extends BaseService
{
    protected $traineeRepository,$userRepository;

    public function __construct(TraineeRepository $traineeRepository, UserRepository $userRepository)
    {
        parent::__construct();
        $this->traineeRepository = $traineeRepository;
        $this->userRepository = $userRepository;
    }

    public function loadViewData(): array
    {
        $this->pageTitle('Trainee List');
        $this->tableColumns([
            __('ID'),
            __('Name'),
            __('Age'),
            __('Weight'),
            __('Height'),
            __('Date Start'),
            __('Date End'),
            __('Level'),
            __('Status'),
            __('Action')

        ]);

        $this->jsColumns([
            'id' => '',
            'user_id' => '',
            'age' => '',
            'weight' => '',
            'height' => '',
            'membership_start' => '',
            'membership_end' => '',
            'training_level' => '',
            'status' => '',
            'action' => ''
        ]);

        $this->addButton('system.trainee.create');
        $this->filterIgnoreColumns(['action']);

        return $this->retunData;
    }

    public function loadDataTableData()
    {
        $query = $this->traineeRepository->getDataTableQuery();

        $eloquentData = app(Pipeline::class)
            ->send($query)
            ->through([
//                Id::class,
//                PermissionGroupId::class,
//                Name::class,
//                Email::class,
//                CreatedAtFrom::class,
//                CreatedAtTo::class
            ])->thenReturn();
        return Datatables::eloquent($eloquentData)
            ->addColumn('id', '{{$id}}')
            ->addColumn('user_id', function ($data) {
                if ($data->user_id)
                    return datatable_links('system.user.show', route('system.user.show', $data->user_id), $data->user->name);
            })
            ->editColumn('weight', '{{$weight}}')
            ->editColumn('height', '{{$height}}')
            ->editColumn('status', function ($data) {
                return status_icon($data->status);
            })
            ->addColumn('membership_start', function ($data) {
                return $data->membership_start;
            })
            ->addColumn('membership_end', function ($data) {
                return $data->membership_end;
            })
            ->editColumn('action', function ($data) {
//                 $this->actionButtons(datatable_menu_show(route('system.user.show', $data->id), 'system.user.show'));
//                $this->actionButtons(datatable_menu_edit(route('system.user.edit', $data->id), 'system.user.edit'));
                return $this->actionButtonsRender($this->traineeRepository->modelPath(), $data->id);
            })->escapeColumns([])
            ->make(true);
    }

    public function create(): array
    {
        $this->pageTitle('Create Trainee');
        $this->breadcrumb('Trainee', 'system.trainee.index');
        $this->otherData([
//            'PermissionGroup' => (new PermissionGroupService($this->permission_group_repository))->permissionArray(),
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

    public function store($request)
    {
        DB::beginTransaction();

        try {
            $mobile = $request->telephone_code . $request->telephone;
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'status' => $request->status,
                'user_type' => 2,
                'password' => $this->userPassword($request->password),
                'mobile' => $mobile ?? '',
            ];

            $user = $this->userRepository->store($userData);

            $traineeData = [
                'user_id' => $user->id,
                'training_level' => $request->training_level,
                'membership_start' => $request->membership_start,
                'membership_end' => $request->membership_end,
                'age' => $request->age,
                'weight' => $request->weight,
                'height' => $request->height,
                'status' => $request->status,
            ];
            $trainee = $this->traineeRepository->store($traineeData);

            DB::commit();
            return $trainee;
        } catch (\Exception $e) {
            DB::rollback();
            errorLog($e->getMessage());
            return false;
        }

    }


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
