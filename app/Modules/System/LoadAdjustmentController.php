<?php

namespace App\Modules\System;

use App\Http\Requests\LoadAdjustmentItemReviewRequest;
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
     * مراجعة عنصر مجموعة فردية محددة واتخاذ قرار مستقل
     */
    public function reviewItem(LoadAdjustmentItemReviewRequest $request, $id)
    {
        try {
            $user = Auth::guard('user')->user();
            $item = $this->adjustmentService->reviewItem((int) $id, $request->validated(), $user);

            if ($item->hasConflict()) {
                $message = __('تم رصد تعارض: تم تعديل وزن الخطة التدريبية بعد تقديم الطلب.');
            } elseif ($item->isApproved()) {
                $message = __('تم اعتماد وتطبيق الوزن بنجاح على الخطة التدريبية!');
            } else {
                $message = __('تم رفض طلب تعديل المجموعة.');
            }

            if ($request->ajax() || $request->wantsJson()) {
                return $this->success($message, [
                    'item'     => $item,
                    'conflict' => $item->hasConflict(),
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
