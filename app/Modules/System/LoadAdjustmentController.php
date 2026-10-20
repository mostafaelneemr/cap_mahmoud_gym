<?php

namespace App\Modules\System;

use App\Http\Requests\LoadAdjustmentReportRequest;
use App\Http\Requests\LoadAdjustmentReviewRequest;
use App\Models\Trainee;
use App\Services\LoadAdjustmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoadAdjustmentController extends SystemController
{
    protected LoadAdjustmentService $loadAdjustmentService;

    public function __construct(LoadAdjustmentService $loadAdjustmentService)
    {
        parent::__construct();
        $this->loadAdjustmentService = $loadAdjustmentService;
    }

    /**
     * تقديم تقرير تعديل الحمل التدريبي بواسطة المتدرب.
     *
     * @param LoadAdjustmentReportRequest $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function store(LoadAdjustmentReportRequest $request)
    {
        try {
            $authUser = Auth::guard('user')->user();
            $report = $this->loadAdjustmentService->storeReport($request->validated(), $authUser);

            $message = __('Load adjustment report submitted successfully to your coach!');

            if ($request->ajax() || $request->wantsJson()) {
                return $this->success($message, [
                    'report' => $report,
                ]);
            }

            flash_msg('success', $message);
            return redirect()->back();
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return $this->fail($e->getMessage());
            }

            flash_msg('error', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * استرجاع السجل التاريخي لتقارير تعديل الأحمال للمتدرب.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function traineeHistory(Request $request)
    {
        $authUser = Auth::guard('user')->user();
        $trainee = null;

        if (in_array((int) $authUser->user_type, [2, 3], true)) {
            $trainee = Trainee::where('user_id', $authUser->id)->first();
        } elseif ($request->filled('trainee_id')) {
            $trainee = Trainee::find($request->trainee_id);
        }

        if (!$trainee) {
            return $this->fail(__('Trainee profile not found.'), 404);
        }

        $exerciseId = $request->input('exercise_id');
        if ($exerciseId) {
            $reports = $this->loadAdjustmentService->getReportsForExercise((int) $exerciseId, $trainee->id);
        } else {
            $reports = $this->loadAdjustmentService->getReportsForTrainee($trainee->id);
        }

        return $this->success(__('History fetched successfully'), [
            'trainee_id' => $trainee->id,
            'reports'    => $reports,
        ]);
    }

    /**
     * مراجعة التقرير وتحديث حالته وإضافة ملاحظات المدرب.
     *
     * @param LoadAdjustmentReviewRequest $request
     * @param int|string $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function review(LoadAdjustmentReviewRequest $request, $id)
    {
        try {
            $authUser = Auth::guard('user')->user();
            $report = $this->loadAdjustmentService->reviewReport((int) $id, $request->validated(), $authUser);

            $message = __('Load adjustment report reviewed successfully!');

            if ($request->ajax() || $request->wantsJson()) {
                return $this->success($message, [
                    'report' => $report,
                ]);
            }

            flash_msg('success', $message);
            return redirect()->back();
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return $this->fail($e->getMessage());
            }

            flash_msg('error', $e->getMessage());
            return redirect()->back();
        }
    }
}
