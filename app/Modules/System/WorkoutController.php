<?php

namespace App\Modules\System;

use App\Http\Requests\StoreExercisFormRequest;
use App\Repositories\Workout\WorkoutRepository;
use App\Services\WorkoutService;
use Illuminate\Http\Request;

class WorkoutController extends SystemController
{
    protected $workoutService;
    public function __construct(WorkoutService $workoutService)
    {
        parent::__construct();
        $this->workoutService = $workoutService;
    }

    public function index(Request $request)
    {
        if ($request->isDataTable) {
            return $this->workoutService->loadDataTableData();
        }
        return $this->view('workout.index', $this->workoutService->loadViewData());
    }

    public function create(Request $request)
    {
        return $this->view('workout.create', $this->workoutService->create($request));
    }

    public function store(Request $request)
    {
        $workout = $this->workoutService->store($request);
        if ($workout) {
            flash_msg('success', __('Data Added successfully'));
            return $this->success(
                __('Data added successfully'),
                ['url' => route('system.workout.index')]
            );
        } else {
            return $this->fail(__('Sorry, we could not add the data'));
        }
    }

    public function destroy($id)
    {
        $workout = $this->workoutService->destroy($id);
        if ($workout) {
            flash_msg('success', __('Data Deleted successfully'));
            return $this->success(
                __('Data deleted successfully'),
                ['url' => route('system.workout.index')]
            );
        } else {
            return $this->fail(__('Sorry, we could not delete the data'));
        }
    }

    public function storeDayExercises(StoreExercisFormRequest $request, $dayId, WorkoutRepository $workoutRepository)
    {
        $workoutRepository->syncDayExercises($dayId, $request->input('exercises', []));

        flash_msg('success', __('Exercises updated successfully'));
        return redirect()->back();
    }
}
