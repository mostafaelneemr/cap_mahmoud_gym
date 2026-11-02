<?php

namespace App\Modules\System;

use App\Http\Requests\LoadAdjustmentItemReviewRequest;
use App\Http\Requests\LoadAdjustmentReportRequest;
use App\Http\Requests\LoadAdjustmentReviewRequest;
use App\Models\TraineeLoadAdjustmentItem;
use App\Services\InAppNotificationService;
use App\Services\LoadAdjustmentService;
use Illuminate\Http\Request;
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
     * صفحة مراجعة طلبات تعديل أوزان التمارين المركزية للمدرب
     */
    public function index(Request $request)
    {
        $authUser = Auth::guard('user')->user();
        if ($authUser && $authUser->user_type == 2) {
            abort(403, __('Unauthorized access.'));
        }

        $filters = [
            'trainee'    => $request->get('trainee'),
            'status'     => $request->get('status', 'pending'),
            'date'       => $request->get('date'),
            'request_id' => $request->get('request_id'),
        ];

        $stats = $this->adjustmentService->getSummaryStats();
        $adjustments = $this->adjustmentService->getPaginatedRequests($filters, 15);

        $this->viewData['breadcrumb'][] = [
            'text' => __('Load Adjustment Center')
        ];
        $this->viewData['pageTitle'] = __('Load Adjustment Center');
        $this->viewData['stats'] = $stats;
        $this->viewData['adjustments'] = $adjustments;
        $this->viewData['filters'] = $filters;
        $this->viewData['highlightedRequestId'] = $request->get('request_id');

        return $this->view('load-adjustment.index', $this->viewData);
    }

    /**
     * استلام وحفظ تقرير تعديل الوزن من المتدرب
     */
    public function store(LoadAdjustmentReportRequest $request)
    {
        try {
            $user = Auth::guard('user')->user();
            $report = $this->adjustmentService->storeReport($request->validated(), $user);
            app(InAppNotificationService::class)->notifyCoachLoadAdjustmentSubmitted($report);

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

            // Notify trainee when item decision is finalized (approved or rejected) and not in conflict
            if (in_array($item->status, [TraineeLoadAdjustmentItem::STATUS_APPROVED, TraineeLoadAdjustmentItem::STATUS_REJECTED], true)) {
                app(InAppNotificationService::class)->notifyTraineeLoadAdjustmentItemReviewed($item);
            }

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
            app(InAppNotificationService::class)->notifyTraineeLoadAdjustmentReviewed($report);

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
