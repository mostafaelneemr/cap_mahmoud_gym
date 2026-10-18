<?php

namespace App\Modules\System;

use App\Http\Requests\StoreExercisFormRequest;
use App\Http\Requests\WorkoutFormRequest;
use App\Models\WorkoutPlan;
use App\Repositories\Workout\WorkoutRepository;
use App\Services\WorkoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function store(WorkoutFormRequest $request)
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
        $dayData = [
            'day_name' => $request->input('day_name'),
            'warmup' => $request->input('warmup'),
            'post_workout' => $request->input('post_workout'),
        ];

        if ($request->has('warmup_links')) {
            $dayData['warmup_links'] = WorkoutPlan::normalizeWarmupLinks($request->input('warmup_links', []));
        }

        $day = $workoutRepository->syncDayExercises((int) $dayId, $request->input('exercises', []), $dayData);

        if (!$day) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'success' => false,
                    'message' => __('Workout day not found'),
                ], 404);
            }
            flash_msg('error', __('Workout day not found'));
            return redirect()->back();
        }

        $message = __('Day updated successfully');
        flash_msg('success', $message);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => true,
                'success' => true,
                'message' => $message,
                'data' => [
                    'day_id' => $day->id,
                    'day_name' => $day->day_name,
                    'warmup_links' => $day->warmup_links_list,
                ],
            ]);
        }

        return redirect()->back();
    }

    public function toggleDayStatus($dayId, Request $request)
    {
        $day = WorkoutPlan::find($dayId);
        if (!$day) {
            return $this->fail(__('Workout day not found'));
        }

        $newStatus = ($day->status === 'active') ? 'inactive' : 'active';
        $day->status = $newStatus;
        $day->save();

        $message = ($newStatus === 'active')
            ? __('Workout day ":day" is now Active.', ['day' => $day->day_name ?: __('Day')])
            : __('Workout day ":day" is now Inactive (temporarily muted).', ['day' => $day->day_name ?: __('Day')]);

        if ($request->ajax() || $request->wantsJson()) {
            return $this->success($message, [
                'day_id' => $day->id,
                'status' => $newStatus,
                'status_label' => ($newStatus === 'active') ? __('Active') : __('Inactive'),
            ]);
        }

        flash_msg('success', $message);
        return redirect()->back();
    }

    public function storeInlineDay(StoreExercisFormRequest $request, WorkoutRepository $workoutRepository)
    {
        $traineeId = $request->input('trainee_id');
        if (!$traineeId) {
            return $this->fail(__('Trainee ID is required'));
        }

        $dayName = $request->input('day_name') ?: __('New Day');
        $warmup = $request->input('warmup');
        $warmupLinks = $request->has('warmup_links') ? WorkoutPlan::normalizeWarmupLinks($request->input('warmup_links', [])) : null;
        $postWorkout = $request->input('post_workout');

        DB::beginTransaction();
        try {
            $plan = WorkoutPlan::create([
                'trainee_id' => $traineeId,
                'day_name' => $dayName,
                'warmup' => $warmup,
                'warmup_links' => $warmupLinks,
                'post_workout' => $postWorkout,
                'status' => 'active',
            ]);

            $exercises = $request->input('exercises', []);
            if (!empty($exercises) && is_array($exercises)) {
                $workoutRepository->syncDayExercises($plan->id, $exercises, []);
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'success' => true,
                    'message' => __('New workout day added successfully'),
                    'data' => [
                        'plan_id' => $plan->id,
                        'day_name' => $plan->day_name,
                        'reload' => true,
                    ],
                ]);
            }

            flash_msg('success', __('New workout day added successfully'));
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            errorLog('WorkoutController@storeInlineDay: ' . $e->getMessage());
            return $this->fail(__('Failed to create workout day'));
        }
    }
}
