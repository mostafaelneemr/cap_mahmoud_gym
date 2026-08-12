<?php

namespace App\Modules\System;

use App\Http\Requests\NutritionFormRequest;
use App\Services\NutritionService;
use App\Services\TraineeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NutritionController extends SystemController
{
    protected $nutritionService, $traineeService;

    public function __construct(NutritionService $nutritionService, TraineeService $traineeService)
    {
        parent::__construct();
        $this->nutritionService = $nutritionService;
        $this->traineeService = $traineeService;
    }

    public function index(Request $request)
    {
        if ($request->isDataTable) {
            return $this->nutritionService->loadDataTableData();
        }
        return $this->view('nutrition.index', $this->nutritionService->loadViewData());
    }

    public function create(Request $request)
    {
        return $this->view('nutrition.create', $this->nutritionService->create($request));
    }

    public function store(NutritionFormRequest $request)
    {
        $plan = $this->nutritionService->store($request);
         if ($plan) {
            flash_msg('success', __('Nutrition plan created successfully'));
            return $this->success(
                __('Nutrition plan created successfully'),
                ['url' => route('system.nutrition.index')]
            );
        } else {
            return $this->fail(__('Sorry, could not save the nutrition plan'));
        }
    }

    public function show($id)
    {
        $plan = $this->nutritionService->findById($id);
        $this->viewData['pageTitle'] = __('Nutrition Plan Details');
        $this->viewData['plan'] = $plan;
        return $this->view('nutrition.show', $this->viewData);
    }

    public function edit($id)
    {
        return $this->view('nutrition.create', $this->nutritionService->edit($id));
    }

    public function update(NutritionFormRequest $request, $id)
    {
        $updated = $this->nutritionService->update($request, $id);
        if ($updated) {
            flash_msg('success', __('Nutrition plan updated successfully'));
            return $this->success(
                __('Nutrition plan updated successfully'),
                ['url' => route('system.nutrition.index')]
            );
        } else {
            return $this->fail(__('Sorry, could not update the nutrition plan'));
        }
    }

    public function destroy($id)
    {
        $deleted = $this->nutritionService->destroy($id);
        if ($deleted) {
            flash_msg('success', __('Nutrition plan deleted successfully'));
            return $this->success(
                __('Nutrition plan deleted successfully'),
                ['url' => route('system.nutrition.index')]
            );
        } else {
            return $this->fail(__('Sorry, could not delete the nutrition plan'));
        }
    }

    /**
     * Trainee View: Active Nutrition Plan
     */
    public function myPlan(Request $request)
    {
        $user = Auth::guard('user')->user();
        $trainee = $this->traineeService->getTraineeFirst($user->id);
        $plan = $this->nutritionService->getActivePlanForTraineeUser($user);

        $this->viewData['breadcrumb'] = [
            [
                'text' => __('My Nutrition Plan'),
            ]
        ];
        $this->viewData['pageTitle'] = __('My Nutrition Plan');
        $this->viewData['user'] = $user;
        $this->viewData['trainee'] = $trainee;
        $this->viewData['plan'] = $plan;

        return $this->view('nutrition.my-plan', $this->viewData);
    }
}
