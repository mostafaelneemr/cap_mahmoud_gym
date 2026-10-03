<?php

namespace App\Services;

use App\Repositories\Exercise\ExerciseRepository;
use App\Repositories\Trainee\TraineeRepository;
use App\Repositories\Workout\WorkoutRepository;
use Illuminate\Support\Facades\Crypt;


class WorkoutService extends BaseService
{
    protected $workoutRepository, $traineeRepository, $exerciseRepository;

    public function __construct(
        WorkoutRepository $workoutRepository,
        TraineeRepository $traineeRepository,
        ExerciseRepository $exerciseRepository
    ) {
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
            ->addColumn('day_name', function ($data) {
                return $data->display_day_name;
            })
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
            ->addColumn('day_name', function ($data) {
                return $data->display_day_name;
            })
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
                $dayName = !empty($dayData['day_name']) ? $dayData['day_name'] : (!empty($dayData['title']) ? $dayData['title'] : ($dayData['day_name_ar'] ?? $dayKey));
                $dayNameAr = !empty($dayData['day_name_ar']) ? $dayData['day_name_ar'] : null;
                $warmup = $dayData['warmup'] ?? null;
                $warmupAr = $dayData['warmup_ar'] ?? null;
                $postWorkout = $dayData['post_workout'] ?? null;
                $postWorkoutAr = $dayData['post_workout_ar'] ?? null;

                $workoutPlan = $this->workoutRepository->store([
                    'trainee_id' => $trainee_id,
                    'day_name' => $dayName,
                    'day_name_ar' => $dayNameAr,
                    'warmup' => $warmup,
                    'warmup_ar' => $warmupAr,
                    'post_workout' => $postWorkout,
                    'post_workout_ar' => $postWorkoutAr,
                ]);

                if (isset($dayData['exercises']) && is_array($dayData['exercises'])) {
                    foreach ($dayData['exercises'] as $exData) {
                        $exName = !empty($exData['name']) ? $exData['name'] : ($exData['name_ar'] ?? null);
                        $exNameAr = !empty($exData['name_ar']) ? $exData['name_ar'] : null;

                        if (!empty($exName) || !empty($exNameAr)) {
                            $this->exerciseRepository->store([
                                'workout_plan_id' => $workoutPlan->id,
                                'name' => $exName ?: $exNameAr,
                                'name_ar' => $exNameAr,
                                'sets' => $exData['sets'] ?? null,
                                'reps' => $exData['reps'] ?? null,
                                'rest' => $exData['rest'] ?? null,
                                'internal_weight' => $exData['weight'] ?? null,
                                'tempo' => $exData['tempo'] ?? null,
                                'link' => $exData['link'] ?? null,
                                'notes_en' => $exData['notes_en'] ?? null,
                                'notes_ar' => $exData['notes_ar'] ?? null,
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

    public function restoreLatestArchivedWorkout(int $traineeId, ?string $archivedAt = null): bool
    {
        return $this->workoutRepository->restoreLatestArchivedWorkout($traineeId, $archivedAt);
    }
}
