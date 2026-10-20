<?php

namespace App\Modules\System;

use App\Http\Requests\NutritionFormRequest;
use App\Models\Trainee;
use App\Services\NutritionService;
use App\Services\TraineeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

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
     * Trainee / Admin View: Active Nutrition Plan
     */
    public function myPlan(Request $request)
    {
        $authUser = Auth::guard('user')->user();
        $isTrainee = in_array((int) $authUser->user_type, [2, 3], true);
        $trainee = null;

        if ($isTrainee) {
            $ownTrainee = Trainee::where('user_id', $authUser->id)->first();
            if (!$ownTrainee) {
                flash_msg('error', __('Trainee profile not found.'));
                return redirect()->back();
            }

            // منع محاولات الوصول غير المصرح لخطط المتدربين الآخرين (IDOR)
            if ($request->filled('trainee')) {
                try {
                    $decryptedId = Crypt::decrypt($request->trainee);
                    $targetTrainee = Trainee::find($decryptedId) ?? Trainee::where('user_id', $decryptedId)->first();
                    if (!$targetTrainee || (int) $targetTrainee->user_id !== (int) $authUser->id) {
                        abort(403, __('Unauthorized access to nutrition plan.'));
                    }
                } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
                    throw $e;
                } catch (\Exception $e) {
                    abort(403, __('Invalid trainee identifier.'));
                }
            } elseif ($request->filled('trainee_id')) {
                if ((int) $request->trainee_id !== (int) $ownTrainee->id && (int) $request->trainee_id !== (int) $ownTrainee->user_id) {
                    abort(403, __('Unauthorized access to nutrition plan.'));
                }
            }

            $trainee = $ownTrainee;
        } else {
            if ($request->filled('trainee')) {
                try {
                    $decryptedId = Crypt::decrypt($request->trainee);
                    $trainee = Trainee::find($decryptedId) ?? Trainee::where('user_id', $decryptedId)->first();
                } catch (\Exception $e) {
                    Log::error('Failed to decrypt trainee ID: ' . $e->getMessage());
                    $trainee = null;
                }
            } elseif ($request->filled('trainee_id')) {
                $trainee = Trainee::find($request->trainee_id) ?? Trainee::where('user_id', $request->trainee_id)->first();
            }

            if (!$trainee) {
                $trainee = $this->traineeService->getTraineeFirst($authUser->id);
                if (!$trainee && ($authUser->user_type == 1 || $authUser->user_type === null)) {
                    $trainee = Trainee::first();
                }
            }
        }

        $targetUser = $trainee ? ($trainee->user ?? $authUser) : $authUser;
        $plan = $trainee ? $this->nutritionService->getActivePlanForTraineeUser($targetUser) : null;

        $this->viewData['breadcrumb'] = [
            [
                'text' => __('My Nutrition Plan'),
            ]
        ];
        $this->viewData['pageTitle'] = __('Nutrition Plan');
        $this->viewData['user'] = $targetUser;
        $this->viewData['trainee'] = $trainee;
        $this->viewData['plan'] = $plan;

        return $this->view('nutrition.my-plan', $this->viewData);
    }
}
