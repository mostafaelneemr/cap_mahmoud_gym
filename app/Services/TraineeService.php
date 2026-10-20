<?php

namespace App\Services;

use App\Enums\StatusEnum;
use App\Enums\TraineesStatusEnum;
use App\Repositories\Trainee\TraineeRepository;
use App\Repositories\User\UserRepository;
use Datatables;
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

    /**
     * التحقق من المعرف وفك تشفيره إن لزم الأمر واسترجاع سجل المتدرب والمستخدم المرتبط به.
     *
     * @param mixed $id
     * @return array{0: \App\Models\Trainee|null, 1: \App\Models\User|null}
     */
    public function resolveTraineeAndUser($id): array
    {
        if (empty($id)) {
            return [null, null];
        }

        // محاولة فك التشفير إذا كان المعرف مشفراً
        if (is_string($id) && !ctype_digit($id)) {
            try {
                $id = Crypt::decrypt($id);
            } catch (\Exception $e) {
                // إذا فشل فك التشفير، يبقى المعرف كما هو
            }
        }

        // 1. البحث كـ Trainee ID أولاً
        $trainee = null;
        try {
            $trainee = \App\Models\Trainee::find($id);
        } catch (\Exception $e) {
            $trainee = null;
        }

        if ($trainee) {
            $user = $trainee->user ?? \App\Models\User::find($trainee->user_id);
            return [$trainee, $user];
        }

        // 2. البحث كـ User ID إذا لم يكن Trainee ID
        $user = null;
        try {
            $user = \App\Models\User::find($id);
        } catch (\Exception $e) {
            $user = null;
        }

        if ($user) {
            $trainee = \App\Models\Trainee::where('user_id', $user->id)->first();
            return [$trainee, $user];
        }

        return [null, null];
    }

    public function findById($id)
    {
        [$trainee, $user] = $this->resolveTraineeAndUser($id);

        if (!$trainee) {
            abort(404, __('Trainee not found.'));
        }

        $trainee->load([
            'user',
            'currentWorkoutPlans.exercises',
            'currentWorkoutPlans.warmupExercises',
            'currentWorkoutPlans.mainExercises',
            'currentWorkoutPlans.postWorkoutExercises',
            'activeWorkoutPlans.exercises',
            'archivedWorkoutPlans.exercises',
            'archivedWorkoutPlans.warmupExercises',
            'archivedWorkoutPlans.mainExercises',
            'archivedWorkoutPlans.postWorkoutExercises',
            'activeNutritionPlans',
        ]);
        $user_id = $trainee->user_id ?? 0;

        $activityLogData = $this->activity_log_service->loadViewData();
        $activityLogData['datatableURL'] = route('system.trainee.get-activity-log', $user_id);
        $activityLogData['datatableID'] = 'activity-log';

        $authSessionData = $this->auth_session_service->loadViewData();
        $authSessionData['datatableURL'] = route('system.trainee.get-auth-session', $user_id);
        $authSessionData['datatableID'] = 'auth-session';

        $workoutData = $this->workoutService->loadViewData();
        $workoutData['datatableURL'] = route('system.trainee.get-workout', $trainee->id);
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
                if ($data->user_id) {
                    return datatable_links('system.trainee.show', route('system.trainee.show', $data->id), $data->user?->name);
                }
                return '';
            })
            ->editColumn('weight', '{{$weight}}')
            ->editColumn('height', '{{$height}}')
            ->editColumn('status', function ($data) {
                return $data->status ;
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
            $mobile = null;
            if ($request->filled('telephone')) {
                $mobile = \formatMobileNumber($request->telephone, $request->telephone_code);
            }

            $userStatus = ($request->status == TraineesStatusEnum::Active->value) ? StatusEnum::Enable->value : StatusEnum::Disable->value;

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'status' => $userStatus,
                'permission_group_id' => 125,
                'user_type' => 2,
                'password' => $this->userPassword('123456'),
                'mobile' => $mobile ?? '',
                'default_language' => 'en-gb',
            ];

            $user = $this->userRepository->store($userData);

            $traineeData = [
                'user_id' => $user->id,
                'name' => $request->name,
                'email' => $request->email,
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
        [$trainee, $user] = $this->resolveTraineeAndUser($id);

        if (!$user) {
            abort(404, __('Trainee user not found.'));
        }

        $this->clearRetunData();
        $this->pageTitle('Update Trainee');
        $this->breadcrumb('Trainee', 'system.trainee.index');

        $parsedMobile = parseMobileNumber($user->mobile ?? '');

        $this->otherData([
            'result' => $user,
            'trainee' => $trainee,
            'telephone' => $parsedMobile['telephone'],
            'telephone_code' => $parsedMobile['telephone_code'],
            'code' => $parsedMobile['code'],
        ]);

        return $this->retunData;
    }

    public function update($request, $id)
    {
        [$trainee, $user] = $this->resolveTraineeAndUser($id);

        if (!$user) {
            return false;
        }

        DB::beginTransaction();
        try {
            $userData = $request->only(['name', 'email']);

            // تحديث كلمة المرور بشكل آمن ومشفر عند تقديمها فقط
            if ($request->filled('password')) {
                $userData['password'] = $this->userPassword($request->password);
            }

            if ($request->filled('telephone')) {
                $userData['mobile'] = \formatMobileNumber($request->telephone, $request->telephone_code);
            }

            $this->userRepository->update($userData, $user->id);

            if ($trainee) {
                $traineeData = [
                    'training_level' => $request->training_level,
                    'membership_start' => $request->membership_start,
                    'membership_end' => $request->membership_end,
                    'age' => $request->age,
                    'weight' => $request->weight,
                    'height' => $request->height,
                    'status' => $request->status,
                ];

                if ($request->filled('name')) {
                    $traineeData['name'] = $request->name;
                }

                if ($request->filled('email')) {
                    $traineeData['email'] = $request->email;
                }

                $this->traineeRepository->update($traineeData, $trainee->id);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            errorLog($e->getMessage());
            return false;
        }
    }

    /**
     * الحذف الآمن (Soft Delete) للمتدرب والمستخدم المرتبط به مع الحفاظ على سلامة البيانات.
     *
     * @param mixed $id
     * @return bool
     */
    public function destroy($id): bool
    {
        [$trainee, $user] = $this->resolveTraineeAndUser($id);

        if (!$user && !$trainee) {
            abort(404, __('Trainee not found.'));
        }

        DB::beginTransaction();
        try {
            // حذف سجل المتدرب إن وجد (يقوم نموذج Trainee بـ SoftDelete متسلسل للخطط الغذائية والتدريبية)
            if ($trainee) {
                $trainee->delete();
            }

            // حذف سجل المستخدم إن وجد
            if ($user) {
                $user->delete();
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            errorLog($e->getMessage());
            return false;
        }
    }

    protected function userPassword($password): string
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

    public function restoreLatestArchivedWorkout(int $traineeId, ?string $archivedAt = null): bool
    {
        return $this->workoutService->restoreLatestArchivedWorkout($traineeId, $archivedAt);
    }

    public function getTraineeId($id)
    {
        return $this->traineeRepository->find($id);
    }
}
