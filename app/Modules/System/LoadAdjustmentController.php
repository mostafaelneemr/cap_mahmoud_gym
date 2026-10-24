<?php

namespace App\Modules\System;

use App\Http\Requests\LoadAdjustmentReportRequest;
use App\Http\Requests\LoadAdjustmentReviewRequest;
use App\Services\LoadAdjustmentService;
use Illuminate\Support\Facades\Auth;

class LoadAdjustmentController extends SystemController
{
    protected LoadAdjustmentService $adjustmentService;

    public function __construct(LoadAdjustmentService $adjustmentService)
    {
        parent::__construct();
        $this->adjustmentService = $adjustmentService;
    }

    /**
     * استلام وحفظ تقرير تعديل الوزن من المتدرب
     */
    public function store(LoadAdjustmentReportRequest $request)
    {
        try {
            $user = Auth::guard('user')->user();
            $report = $this->adjustmentService->storeReport($request->validated(), $user);

            $message = __('تم إرسال تحديث الوزن بنجاح للمدرب!');

            if ($request->ajax() || $request->wantsJson()) {
                return $this->success($message, [
                    'report' => $report,
                ]);
            }

            flash_msg('success', $message);
            return redirect()->back();
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return $this->fail($e->getMessage());
            }

            flash_msg('error', $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * مراجعة التقرير من قبل المدرب وتسجيل التوجيهات
     */
    public function review(LoadAdjustmentReviewRequest $request, $id)
    {
        try {
            $user = Auth::guard('user')->user();
            $report = $this->adjustmentService->reviewReport((int) $id, $request->validated(), $user);

            $message = __('تمت مراجعة التقرير بنجاح!');

            if ($request->ajax() || $request->wantsJson()) {
                return $this->success($message, [
                    'report' => $report,
                ]);
            }

            flash_msg('success', $message);
            return redirect()->back();
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return $this->fail($e->getMessage());
            }

            flash_msg('error', $e->getMessage());
            return redirect()->back();
        }
    }
}
