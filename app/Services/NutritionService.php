<?php

namespace App\Services;

use App\Models\NutritionPlan;
use App\Models\Trainee;
use App\Models\User;
use App\Repositories\Nutrition\NutritionRepository;
use App\Repositories\Trainee\TraineeRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class NutritionService extends BaseService
{
    protected $nutritionRepository, $traineeRepository, $userRepository;
    public function __construct(NutritionRepository $nutritionRepository, TraineeRepository $traineeRepository,
                                UserRepository      $userRepository)
    {
        parent::__construct();
        $this->nutritionRepository = $nutritionRepository;
        $this->traineeRepository = $traineeRepository;
        $this->userRepository = $userRepository;
    }

    public function loadViewData(): array
    {
        $this->pageTitle('Nutrition Plans');
        $this->tableColumns([
            __('ID'),
            __('Trainee Name'),
            __('Plan Name'),
            __('Status'),
            __('Created At'),
            __('Action')
        ]);

        $this->jsColumns([
            'id' => '',
            'trainee_name' => '',
            'name' => '',
            'status' => '',
            'created_at' => '',
            'action' => ''
        ]);

        $this->addButton('system.nutrition.create');
        $this->filterIgnoreColumns(['action']);

        return $this->retunData;
    }

    public function loadDataTableData()
    {
        $query = $this->nutritionRepository->getDataTableQuery();

        return \Datatables::eloquent($query)
            ->addColumn('id', '{{$id}}')
            ->addColumn('trainee_name', function ($data) {
                if ($data->trainee && $data->trainee->user) {
                    return $data->trainee->user->name;
                }
                return $data->user ? $data->user->name : '';
            })
            ->addColumn('name', function ($data) {
                return $data->name ? $data->name : __('Nutrition Plan');
            })
            ->editColumn('status', function ($data) {
                return datatable_badge(ucfirst($data->status), $data->status == 'active' ? 'badge-light-success' : 'badge-light-secondary');
            })
            ->editColumn('created_at', function ($data) {
                return $data->created_at ? $data->created_at->format('Y-m-d H:i') : '';
            })
            ->editColumn('action', function ($data) {
                return $this->actionButtonsRender($this->nutritionRepository->modelPath(), $data->id);
            })
            ->escapeColumns([])
            ->make(true);
    }

    private function resolveTraineeId($id)
    {
        if (!$id) {
            return null;
        }

        $traineeById = Trainee::find($id);
        if ($traineeById) {
            return (int) $traineeById->id;
        }

        $traineeByUser = Trainee::where('user_id', $id)->first();
        if ($traineeByUser) {
            return (int) $traineeByUser->id;
        }

        return (int) $id;
    }

    public function create($request): array
    {
        $selectedTraineeId = null;
        if ($request->filled('trainee')) {
            try {
                $decryptedId = Crypt::decrypt($request->trainee);
                $selectedTraineeId = $this->resolveTraineeId($decryptedId);
            } catch (\Exception $e) {
                $selectedTraineeId = null;
            }
        }

        $trainees = $this->traineeRepository->getTraineeWithUser();
        if ($trainees->isEmpty()) {
            $trainees = $this->userRepository->getUserTrainee();
        }

        $this->pageTitle('Create Nutrition Plan');
        $this->breadcrumb('Trainee', 'system.trainee.index');
        $this->retunData['selectedTraineeId'] = $selectedTraineeId;
        $this->retunData['trainees'] = $trainees;

        return $this->retunData;
    }

    public function store($request)
    {
        $data = $request->all();
        $traineeId = $data['trainee_id'] ?? null;

        if (!$traineeId) {
            return false;
        }

        $traineeId = $this->resolveTraineeId($traineeId);

        DB::beginTransaction();
        try {
            $this->nutritionRepository->updateTraineeNutrition($traineeId);

            $nutritionPlan = $this->nutritionRepository->store([
                'trainee_id' => $traineeId,
                'name' => $data['name'] ?? null,
                'description' => $data['description'] ?? null,
                'status' => 'active',
            ]);

            DB::commit();
            return $nutritionPlan;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function edit($id): array
    {
        $plan = $this->nutritionRepository->find($id);
        $this->pageTitle('Edit Nutrition Plan');
        $this->breadcrumb('Nutrition Plans', 'system.nutrition.index');
        $this->retunData['plan'] = $plan;
        $this->retunData['trainees'] = $this->traineeRepository->getTraineeWithUser();

        return $this->retunData;
    }

    public function update($request, $id)
    {
        $plan = $this->nutritionRepository->find($id);
        if (!$plan) {
            return false;
        }

        $data = $request->all();
        $traineeId = $data['trainee_id'] ?? $plan->trainee_id;
        if ($traineeId) {
            $traineeId = $this->resolveTraineeId($traineeId);
        }

        return $plan->update([
            'trainee_id' => $traineeId,
            'name' => $data['name'] ?? $plan->name,
            'description' => $data['description'] ?? $plan->description,
            'status' => $data['status'] ?? $plan->status,
        ]);
    }

    public function findById($id)
    {
        return $this->nutritionRepository->find($id);
    }

    public function destroy($id)
    {
        $plan = $this->nutritionRepository->find($id);
        if ($plan) {
            $plan->delete();
            return true;
        }
        return false;
    }

    public function getActivePlanForTraineeUser($user)
    {
        $trainee = Trainee::where('user_id', $user->id)->first();
        $traineeId = $trainee ? $trainee->id : $user->id;
        return $this->nutritionRepository->getActiveNutritionByTrainee($traineeId);
    }
}
