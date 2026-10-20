<?php

namespace App\Modules\System;

use App\Http\Requests\TraineeFormRequest;
use App\Models\Trainee;
use App\Services\TraineeService;
use App\Services\WorkoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TraineeController extends SystemController
{
    protected $traineeService, $workoutService;

    public function __construct(TraineeService $traineeService, WorkoutService $workoutService)
    {
        parent::__construct();
        $this->traineeService = $traineeService;
        $this->workoutService = $workoutService;
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

    public function store(TraineeFormRequest $request)
    {
        $trainer = $this->traineeService->store($request);
        if ($trainer) {
            flash_msg('success', __('Data Added successfully'));
            return $this->success(
                __('Data added successfully'),
                ['url' => route('system.trainee.index')]
            );
        } else {
            return $this->fail(__('Sorry, we could not add the data'));
        }
    }

    public function show($id, Request $request)
    {
        return $this->view('trainee.show', $this->traineeService->findById($id));
    }

    public function getUserActivityLog($id)
    {
        return $this->traineeService->loadActivityLogDetails($id);
    }

    public function getAuthSession($id)
    {
        return $this->traineeService->loadAuthSessionDetails($id);
    }

    public function getWorkout($id)
    {
        return $this->traineeService->loadWorkoutDetails($id);
    }

    public function edit($id)
    {
        return $this->view('trainee.create', $this->traineeService->edit($id));
    }

    public function showProfile()
    {
        return $this->view('user.show-profile', $this->traineeService->findById(Auth::id()));
    }

    public function update(TraineeFormRequest $request, $id)
    {
        $authUser = Auth::guard('user')->user();

        // التحقق من الصلاحيات: إذا كان المستخدم متدرباً، يمنع من تعديل أي حساب آخر
        if ($authUser && in_array((int) $authUser->user_type, [2, 3], true)) {
            [$trainee, $user] = $this->traineeService->resolveTraineeAndUser($id);
            if (!$user || (int) $user->id !== (int) $authUser->id) {
                abort(403, __('Unauthorized to update this trainee.'));
            }
        }

        $update = $this->traineeService->update($request, $id);

        if ($update) {
            flash_msg('success', __('Data Updated successfully'));
            return $this->success(
                __('Data Updated successfully'),
                ['url' => route('system.trainee.index')]
            );
        } else {
            return $this->fail(__('Sorry, we could not Update the data'));
        }
    }

    /**
     * حذف المتدرب وسجلاته المرتبطة بشكل آمن بعد التحقق من الصلاحيات.
     *
     * @param int|string $id
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        $authUser = Auth::guard('user')->user();

        // منع المتدربين من حذف أي حسابات
        if ($authUser && in_array((int) $authUser->user_type, [2, 3], true)) {
            abort(403, __('Unauthorized to delete trainees.'));
        }

        [$trainee, $user] = $this->traineeService->resolveTraineeAndUser($id);
        if (!$trainee && !$user) {
            if ($request->ajax() || $request->wantsJson()) {
                return $this->fail(__('Trainee not found.'), 404);
            }
            flash_msg('error', __('Trainee not found.'));
            return redirect()->back();
        }

        $deleted = $this->traineeService->destroy($id);

        if ($deleted) {
            flash_msg('success', __('Trainee deleted successfully.'));
            if ($request->ajax() || $request->wantsJson()) {
                return $this->success(
                    __('Trainee deleted successfully.'),
                    ['url' => route('system.trainee.index')]
                );
            }
            return redirect()->route('system.trainee.index');
        } else {
            if ($request->ajax() || $request->wantsJson()) {
                return $this->fail(__('Sorry, could not delete trainee.'));
            }
            flash_msg('error', __('Sorry, could not delete trainee.'));
            return redirect()->back();
        }
    }

    public function resetPlan($id)
    {
        $this->workoutService->updateTraineeWorkout($id);
        flash_msg('success', __('Workout plan has been reset/archived successfully.'));
        return redirect()->back();
    }

    public function restorePlan($id, Request $request)
    {
        $trainee = $this->traineeService->getTraineeId($id);
        if (!$trainee) {
            if ($request->ajax() || $request->wantsJson()) {
                return $this->fail(__('Trainee not found.'));
            }
            flash_msg('error', __('Trainee not found.'));
            return redirect()->back();
        }

        $batchOrIds = $request->input('plan_ids')
            ?: ($request->input('ids') ?: ($request->input('archived_at') ?: $request->input('archived_date')));

        $restored = $this->workoutService->restoreLatestArchivedWorkout((int) $id, $batchOrIds);

        if ($restored) {
            if ($request->ajax() || $request->wantsJson()) {
                return $this->success(__('Archived workout program has been restored successfully.'));
            }
            flash_msg('success', __('Archived workout program has been restored successfully.'));
        } else {
            if ($request->ajax() || $request->wantsJson()) {
                return $this->fail(__('No archived workout program found to restore.'));
            }
            flash_msg('error', __('No archived workout program found to restore.'));
        }

        return redirect()->back();
    }

    /**
     * Start impersonating a trainee to view the portal from their perspective.
     */
    public function impersonate($id)
    {
        $currentAuthUser = Auth::guard('user')->user();

        if ($currentAuthUser->user_type != 1 && $currentAuthUser->user_type !== null) {
            flash_msg('danger', __('You do not have permission to view as trainee.'));
            return redirect()->back();
        }

        $trainee = Trainee::with('user')->find($id);
        if (!$trainee || !$trainee->user) {
            flash_msg('danger', __('Trainee user account not found.'));
            return redirect()->back();
        }

        session([
            'impersonator_coach_id'     => $currentAuthUser->id,
            'impersonator_return_url'   => route('system.trainee.show', $trainee->id),
            'impersonated_trainee_name' => $trainee->user->name ?? __('Trainee'),
        ]);

        Auth::guard('user')->loginUsingId($trainee->user_id);

        flash_msg('info', __('Now viewing as :name', ['name' => $trainee->user->name]));
        return redirect()->route('system.dashboard.trainer');
    }

    /**
     * Exit trainee impersonation and restore original Coach session.
     */
    public function leaveImpersonate()
    {
        $coachId = session()->pull('impersonator_coach_id');
        $returnUrl = session()->pull('impersonator_return_url', route('system.trainee.index'));
        session()->forget('impersonated_trainee_name');

        if ($coachId) {
            Auth::guard('user')->loginUsingId($coachId);
            flash_msg('success', __('Returned to coach account successfully.'));
            return redirect($returnUrl);
        }

        return redirect()->route('system.dashboard');
    }
}
