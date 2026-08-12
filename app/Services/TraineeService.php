<?php

namespace App\Services;

use App\Repositories\Trainee\TraineeRepository;
use App\Repositories\User\UserRepository;
use Datatables;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TraineeService extends BaseService
{
    protected $traineeRepository, $userRepository, $activity_log_service, $auth_session_service, $workoutService;

    public function __construct(
        TraineeRepository  $traineeRepository,
        UserRepository     $userRepository,
        ActivityLogService $activity_log_service,
        AuthSessionService $auth_session_service,
        WorkoutService     $workoutService
    ) {
        parent::__construct();
        $this->traineeRepository = $traineeRepository;
        $this->userRepository = $userRepository;
        $this->activity_log_service = $activity_log_service;
        $this->auth_session_service = $auth_session_service;
        $this->workoutService = $workoutService;
    }

    public function findById($id)
    {
        $trainee = $this->traineeRepository->find($id);
        $trainee->load(['activeWorkoutPlans.exercises', 'archivedWorkoutPlans.exercises', 'activeNutritionPlans']);
        $user_id = $trainee->user_id ?? 0;

        $activityLogData = $this->activity_log_service->loadViewData();
        $activityLogData['datatableURL'] = route('system.trainee.get-activity-log', $user_id);
        $activityLogData['datatableID'] = 'activity-log';

        $authSessionData = $this->auth_session_service->loadViewData();
        $authSessionData['datatableURL'] = route('system.trainee.get-auth-session', $user_id);
        $authSessionData['datatableID'] = 'auth-session';

        $workoutData = $this->workoutService->loadViewData();
        $workoutData['datatableURL'] = route('system.trainee.get-workout', $id);
        $workoutData['datatableID'] = 'workout';

        $datatablesData = [
            'authSessionData' => $authSessionData,
            'activityLogData' => $activityLogData,
            'workoutData' => $workoutData,
        ];

        $this->clearRetunData();

        $this->pageTitle('View Trainee');
        $this->breadcrumb('Trainee', 'system.trainee.index');

        $this->otherData($datatablesData);
        $this->otherData(['result' => $trainee]);

        return $this->retunData;
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

        //        $eloquentData = app(Pipeline::class)
        //            ->send($query)
        //            ->through([
        //                Id::class,
        //                PermissionGroupId::class,
        //                Name::class,
        //                Email::class,
        //                CreatedAtFrom::class,
        //                CreatedAtTo::class
        //            ])->thenReturn();
        return Datatables::eloquent($query)
            ->addColumn('id', '{{$id}}')
            ->addColumn('user_id', function ($data) {
                if ($data->user_id)
                    return datatable_links('system.trainee.show', route('system.trainee.show', $data->id), $data->user?->name);
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
                if ($data->activeWorkoutPlans->isEmpty()) {
                    $this->actionButtons(
                        datatable_menu_workout(
                            route('system.workout.create', ['trainee' => Crypt::encrypt($data->id)]),
                            'system.workout.create'
                        )
                    );
                }
                $this->actionButtons(datatable_menu_edit(route('system.trainee.edit', $data->user_id), 'system.trainee.edit'));
                $this->actionButtons(datatable_menu_show(route('system.trainee.show', $data->id), 'system.trainee.show'));
                return $this->actionButtonsRender($this->traineeRepository->modelPath(), $data->user_id);
            })->escapeColumns([])
            ->make(true);
    }

    public function create(): array
    {
        $this->pageTitle('Create Trainee');
        $this->breadcrumb('Trainee', 'system.trainee.index');
        $this->otherData([
            'telephone_code' => '+20',
            'code' => 'eg'
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
                'permission_group_id' => 125,
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

    public function edit($id): array
    {
        $user = $this->userRepository->find($id);
        $trainee = $this->traineeRepository->getTraineeFirst($user->id);
        $this->pageTitle('Update Trainee');
        $this->breadcrumb('Trainee', 'system.trainee.index');

        $this->otherData([
            'result' => $user,
            'trainee' => $trainee,
            'telephone' => strlen($user->mobile) < 11 ? $user->mobile : substr($user->mobile, 3),
            'telephone_code' => strlen($user->mobile) < 11 ? '966' : substr($user->mobile, 0, 3),
            'code' => $this->getCode($user->mobile)

        ]);

        return $this->retunData;
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $userData = $request->only(['name', 'email']);

            if ($request->password) {
                $userData['password'] = $this->userPassword($request->password);
            }

            if ($request->telephone) {
                $userData['mobile'] = fixMobileNumber($request->telephone);
            }

            $this->userRepository->update($userData, $id);

            $trainee = $this->traineeRepository->getTraineeFirst($id);

            if ($trainee) {
                $this->traineeRepository->update([
                    'training_level' => $request->training_level,
                    'membership_start' => $request->membership_start,
                    'membership_end' => $request->membership_end,
                    'age' => $request->age,
                    'weight' => $request->weight,
                    'height' => $request->height,
                    'status' => $request->status,
                ], $trainee->id);
            }

            DB::commit();
            return true;
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

    public function loadWorkoutDetails($id)
    {
        return $this->workoutService->loadDataTableDataForTrainee($id);
    }

    public function getTraineeFirst($userId)
    {
        return $this->traineeRepository->getTraineeFirst($userId);
    }
}
