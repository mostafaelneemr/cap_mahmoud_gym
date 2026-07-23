<?php

namespace App\Services;

use App\Repositories\Exercise\ExerciseRepository;
use App\Repositories\Trainee\TraineeRepository;
use App\Repositories\Workout\WorkoutRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;


class WorkoutService extends BaseService
{
    protected $workoutRepository,$traineeRepository,$exerciseRepository;

    public function __construct(WorkoutRepository $workoutRepository, TraineeRepository $traineeRepository,
                                ExerciseRepository $exerciseRepository)
    {
        parent::__construct();
        $this->workoutRepository = $workoutRepository;
        $this->traineeRepository = $traineeRepository;
        $this->exerciseRepository = $exerciseRepository;
    }

    public function loadViewData(): array
    {
        $this->pageTitle('Workout Plans');
        $this->tableColumns([
            __('ID'),
            __('Trainee Name'),
            __('Day Name'),
            __('Action')
        ]);

        $this->jsColumns([
            'id' => '',
            'trainee_name' => '',
            'day_name' => '',
            'action' => ''
        ]);

        $this->addButton('system.workout.create');
        $this->filterIgnoreColumns(['action']);

        return $this->retunData;
    }

    public function loadDataTableData()
    {
        $query = $this->workoutRepository->getDataTableQuery();

        return \Datatables::eloquent($query)
            ->addColumn('id', '{{$id}}')
            ->addColumn('trainee_name', function ($data) {
                return $data->trainee ? ($data->trainee->user ? $data->trainee->user->name : '') : '';
            })
            ->addColumn('day_name', '{{$day_name}}')
            ->editColumn('action', function ($data) {
                return $this->actionButtonsRender($this->workoutRepository->modelPath(), $data->id);
            })->escapeColumns([])
            ->make(true);
    }

    public function loadDataTableDataForTrainee($trainee_id)
    {
        $query = $this->workoutRepository->getDataTableQuery()->where('trainee_id', $trainee_id);

        return \Datatables::eloquent($query)
            ->addColumn('id', '{{$id}}')
            ->addColumn('trainee_name', function ($data) {
                return $data->trainee ? ($data->trainee->user ? $data->trainee->user->name : '') : '';
            })
            ->addColumn('day_name', '{{$day_name}}')
            ->editColumn('action', function ($data) {
                return $this->actionButtonsRender($this->workoutRepository->modelPath(), $data->id);
            })->escapeColumns([])
            ->make(true);
    }

    public function create($request): array
    {
        $selectedTraineeId = null;

        if ($request->filled('trainee')) {
            try {
                $selectedTraineeId = Crypt::decrypt($request->trainee);
            } catch (\Exception $e) {
                $selectedTraineeId = null;
            }
        }
        $this->pageTitle('Create Workout Plan');
        $this->breadcrumb('Trainee', 'system.trainee.index');
        $this->retunData['selectedTraineeId'] = $selectedTraineeId;
        $this->retunData['trainees'] = $this->traineeRepository->getWithUser();

        return $this->retunData;
    }

    public function store($request)
    {
        $data = $request->all();
        $trainee_id = $data['trainee_id'] ?? null;
        $programs = $data['program'] ?? [];

        if (!$trainee_id) {
            return false;
        }

        \DB::beginTransaction();
        try {
            foreach ($programs as $dayKey => $dayData) {
                $workoutPlan = $this->workoutRepository->store([
                    'trainee_id' => $trainee_id,
                    'day_name' => $dayData['title'] ?? $dayKey,
                    'warmup' => $dayData['warmup'] ?? null,
                    'post_workout' => $dayData['post_workout'] ?? null,
                ]);

                if (isset($dayData['exercises']) && is_array($dayData['exercises'])) {
                    foreach ($dayData['exercises'] as $exData) {
                        if (!empty($exData['name'])) {
                            $this->exerciseRepository->store([
                                'workout_plan_id' => $workoutPlan->id,
                                'name' => $exData['name'],
                                'sets' => $exData['sets'] ?? null,
                                'reps' => $exData['reps'] ?? null,
                                'rest' => $exData['rest'] ?? null,
                                'internal_weight' => $exData['weight'] ?? null,
                                'tempo' => $exData['tempo'] ?? null,
                                'link' => $exData['link'] ?? null,
                            ]);
                        }
                    }
                }
            }
            \DB::commit();
            return true;
        } catch (\Exception $e) {
            \DB::rollBack();
            return false;
        }
    }

    public function destroy($id)
    {
        $workout = $this->workoutRepository->find($id);
        if ($workout) {
            $workout->delete();
            return true;
        }
        return false;
    }

    public function updateTraineeWorkout($id)
    {
        return $this->workoutRepository->updateTraineeWorkout($id);
    }
}
