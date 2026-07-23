<?php

namespace App\Modules\System;

use App\Http\Requests\TraineeFormRequest;
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

    public function resetPlan($id)
    {
        $this->workoutService->updateTraineeWorkout($id);
        flash_msg('success', __('Workout plan has been reset/archived successfully.'));
        return redirect()->back();
    }
}
