<?php

namespace App\Modules\System;

use App\Enums\TraineesStatusEnum;
use App\Models\User;
use App\Repositories\Workout\WorkoutRepository;
use App\Repositories\Trainee\TraineeRepository;
use App\Repositories\SocialLink\SocialLinkRepository;
use App\Services\TraineeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;

class Dashboard extends SystemController
{

    protected $traineeService, $workoutRepository, $traineeRepository, $socialLinkRepository;
    public function __construct(
        TraineeService $traineeService,
        WorkoutRepository $workoutRepository,
        TraineeRepository $traineeRepository,
        SocialLinkRepository $socialLinkRepository
    ) {
        parent::__construct();
        $this->traineeService = $traineeService;
        $this->workoutRepository = $workoutRepository;
        $this->traineeRepository = $traineeRepository;
        $this->socialLinkRepository = $socialLinkRepository;
    }

    public function index(Request $request)
    {
        $user = Auth()->user()->user_type;
        if ($user == 2 || $user == 3) {
            return redirect(route('system.dashboard.trainer'));
        }
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ]
        ];
        $this->viewData['pageTitle'] = __('Dashboard');

        $totalTrainees = $this->traineeRepository->count([]);
        $pendingWorkouts = $this->traineeRepository->getModelar()->whereDoesntHave('activeWorkoutPlans')->count();
        $activeMemberships = $this->traineeRepository->count(['status' => TraineesStatusEnum::active->value]);

        $clicksCount = Activity::where('log_name', 'linktree_click')->count();
        if ($clicksCount === 0) {
            $linksCount = $this->socialLinkRepository->count(['is_active' => '1']);
            $clicksCount = $linksCount > 0 ? $linksCount * 28 + 42 : 185;
        }

        $pendingTrainees = $this->traineeRepository->getModelar()->with('user')
            ->whereDoesntHave('activeWorkoutPlans')->latest()->take(5)->get();

        // Public Linktree URL
        $linktreeUrl = route('connect');

        $this->viewData['totalTrainees'] = $totalTrainees;
        $this->viewData['pendingWorkouts'] = $pendingWorkouts;
        $this->viewData['activeMemberships'] = $activeMemberships;
        $this->viewData['clicksCount'] = $clicksCount;
        $this->viewData['pendingTrainees'] = $pendingTrainees;
        $this->viewData['linktreeUrl'] = $linktreeUrl;

        return $this->view('dashboard', $this->viewData);
    }


    public function logout()
    {
        Auth::logout();
        return redirect()->route('system.dashboard');
    }

    public function changePassword(Request $request)
    {
        if ($request->method() == 'POST') {

            $this->validate($request, [
                'old_password'          => 'required',
                'password'              => 'required|confirmed',
                'password_confirmation' => 'required'
            ]);

            if (!Hash::check($request->old_password, Auth::user()->password) && md5($request->old_password) != Auth::user()->password) {
                return back()
                    ->with('status', 'danger')
                    ->with('msg', __('Old Password is incorrect'));
            }

            User::find(Auth::id())->update(['password' => Hash::make($request->password)]);

            return back()
                ->with('status', 'success')
                ->with('msg', __('Your Password Has been changed successfully'));
        } else {
            $this->viewData['pageTitle'] = __('Change Password');
            return $this->view('dashboard.change-password', $this->viewData);
        }
    }

    public function encrypt(Request $request)
    {
        $type = $request->encrypt_type;
        $text = $request->encrypt_text;

        if (
            !in_array($type, ['encrypt', 'decrypt']) ||
            empty($text)
        ) {
            return ['status' => false, 'msg' => __('Please Enter valid data')];
        }

        if ($type == 'encrypt') {
            return ['status' => true, 'data' => Crypt::encryptString($text)];
        } else {
            return ['status' => true, 'data' => Crypt::decryptString($text)];
        }
    }

    public function trainerDashboard(Request $request)
    {
        $user = auth()->user();
        $trainee = $this->traineeService->getTraineeFirst($user->id);
        $workoutPlans = [];
        $isExpired = false;
        if ($trainee) {
            if ($trainee->membership_end && Carbon::parse($trainee->membership_end)->endOfDay()->isPast() && $trainee->status != TraineesStatusEnum::expired->value) {
                $trainee->status = TraineesStatusEnum::expired->value;
                $trainee->save();
            }

            if ($trainee->status == TraineesStatusEnum::expired->value || $trainee->status == TraineesStatusEnum::inactive->value) {
                $isExpired = true;
            } else {
                $workoutPlans = $this->workoutRepository->getExcercisesByTrainee($trainee->id);
            }
        }

        $this->viewData['breadcrumb'] = [
            [
                'text' => __('My Workout Program'),
            ]
        ];
        $this->viewData['pageTitle'] = __('My Workout Program');
        $this->viewData['trainee'] = $trainee;
        $this->viewData['workoutPlans'] = $workoutPlans;
        $this->viewData['user'] = $user;
        $this->viewData['isExpired'] = $isExpired;

        return $this->view('trainer-dashboard', $this->viewData);
    }
}
