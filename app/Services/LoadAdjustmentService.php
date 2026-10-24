<?php

namespace App\Services;

use App\Models\Exercise;
use App\Models\TraineeLoadAdjustment;
use App\Models\TraineeLoadAdjustmentItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * خدمة إدارة تقارير تحديث أوزان التمارين للمتدربين ومراجعتها من قبل المدربين.
 * تدعم مراجعة واعتماد كل مجموعة تدريبية على حدة، كشف تعارض التعديلات المتزامنة،
 * وتطبيق الأوزان المعتمدة على الخطة التدريبية بشكل دقيق وآمن.
 */
class LoadAdjustmentService
{
    /**
     * حفظ تقرير تعديل حمل جديد للمتدرب مع تفاصيل المجموعات الفردية إن وجدت
     *
     * @param array $data
     * @param User $user
     * @return TraineeLoadAdjustment
     */
    public function storeReport(array $data, User $user): TraineeLoadAdjustment
    {
        return DB::transaction(function () use ($data, $user) {
            $trainee = $user->trainee;
            $exercise = Exercise::with('workoutPlan')->findOrFail($data['exercise_id']);
            $workoutPlan = $exercise->workoutPlan;

            // التقاط لقطات ثابتة لبيانات التمرين واليوم التدريبي في لحظة التقديم
            $workoutDayName = $workoutPlan ? $workoutPlan->day_name : null;
            $exerciseName = $exercise->name;
            $prescribedLoad = $exercise->internal_weight_display ?? '';

            // إعداد ملخص الوزن المبلغ عنه
            $reportedLoadSummary = null;
            $setsData = $data['sets'] ?? [];

            if (!empty($setsData) && is_array($setsData)) {
                $parts = [];
                foreach ($setsData as $s) {
                    $setNum = (int) ($s['set_number'] ?? 0);
                    $sWeight = trim((string) ($s['suggested_weight'] ?? ''));
                    if ($setNum > 0 && $sWeight !== '') {
                        $parts[] = "Set {$setNum}: {$sWeight}";
                    }
                }
                $reportedLoadSummary = implode(', ', $parts);
            } elseif (!empty($data['reported_load'])) {
                $reportedLoadSummary = trim($data['reported_load']);
            }

            $report = TraineeLoadAdjustment::create([
                'trainee_id'       => $trainee->id,
                'workout_plan_id'  => $exercise->workout_plan_id,
                'exercise_id'      => $exercise->id,
                'workout_day_name' => $workoutDayName,
                'exercise_name'    => $exerciseName,
                'prescribed_load'  => $prescribedLoad,
                'reported_load'    => $reportedLoadSummary,
                'notes'            => $data['notes'] ?? null,
                'status'           => 'pending',
                'coach_notes'      => null,
                'reviewed_by'      => null,
                'reviewed_at'      => null,
            ]);

            // إنشاء سجلات المجموعات الفردية إذا تم تمريرها
            if (!empty($setsData) && is_array($setsData)) {
                foreach ($setsData as $s) {
                    $setNum = (int) ($s['set_number'] ?? 0);
                    $sWeight = trim((string) ($s['suggested_weight'] ?? ''));

                    if ($setNum < 1 || $setNum > Exercise::MAX_WEIGHTS || $sWeight === '') {
                        continue;
                    }

                    // التقاط الوزن الأساسي الحالي لهذه المجموعة بالذات في لحظة التقديم
                    $baseWeight = $exercise->getWeightForSet($setNum);

                    TraineeLoadAdjustmentItem::create([
                        'load_adjustment_id'      => $report->id,
                        'set_number'              => $setNum,
                        'base_weight'             => $baseWeight,
                        'suggested_weight'        => $sWeight,
                        'approved_weight'         => null,
                        'status'                  => TraineeLoadAdjustmentItem::STATUS_PENDING,
                        'coach_notes'             => null,
                        'reviewed_by'             => null,
                        'reviewed_at'             => null,
                        'applied_at'              => null,
                        'conflict_detected_at'    => null,
                        'conflict_current_weight' => null,
                    ]);
                }
            }

            return $report->load('items');
        });
    }

    /**
     * مراجعة عنصر مجموعة فردية محددة واتخاذ قرار مستقل بشأنها
     *
     * @param int $itemId
     * @param array $data
     * @param User $reviewer
     * @return TraineeLoadAdjustmentItem
     */
    public function reviewItem(int $itemId, array $data, User $reviewer): TraineeLoadAdjustmentItem
    {
        return DB::transaction(function () use ($itemId, $data, $reviewer) {
            $item = TraineeLoadAdjustmentItem::with(['loadAdjustment.exercise'])->lockForUpdate()->findOrFail($itemId);
            $parentReport = $item->loadAdjustment;

            $action = $data['action'] ?? 'approve';
            $coachNotes = array_key_exists('coach_notes', $data) ? $data['coach_notes'] : null;
            $force = !empty($data['force']);

            // إذا كان العنصر تمت مراجعته مسبقاً بنجاح وليس في حالة تعارض، نمنع التكرار
            if (!$force && in_array($item->status, [TraineeLoadAdjustmentItem::STATUS_APPROVED, TraineeLoadAdjustmentItem::STATUS_REJECTED], true)) {
                return $item;
            }

            $exercise = $parentReport->exercise_id ? Exercise::lockForUpdate()->find($parentReport->exercise_id) : null;

            if ($action === 'approve' || $action === 'approve_alternative' || $action === 'resolve_approve') {
                // تحديد الوزن المعتمد
                if ($action === 'approve_alternative' || !empty($data['approved_weight'])) {
                    $weightToApply = trim((string) $data['approved_weight']);
                } else {
                    $weightToApply = $item->suggested_weight;
                }

                // فحص التعارض: مقارنة الوزن الأساسي وقت التقديم مع الوزن الحالي المقيد في الخطة
                if ($exercise && !$force) {
                    $currentPersistedWeight = $exercise->getWeightForSet($item->set_number);

                    // إذا اختلف الوزن الحالي في الخطة عن الوزن الذي استند عليه المتدرب
                    if ($currentPersistedWeight !== $item->base_weight) {
                        $item->status = TraineeLoadAdjustmentItem::STATUS_CONFLICT;
                        $item->conflict_detected_at = now();
                        $item->conflict_current_weight = $currentPersistedWeight;
                        if ($coachNotes !== null) {
                            $item->coach_notes = $coachNotes;
                        }
                        $item->save();

                        $parentReport->recalculateAggregateStatus();
                        return $item;
                    }
                }

                // تطبيق الوزن المعتمد على الخطة التدريبية
                if ($exercise) {
                    $exercise->setWeightForSet($item->set_number, $weightToApply);
                    $exercise->save();
                }

                $item->status = TraineeLoadAdjustmentItem::STATUS_APPROVED;
                $item->approved_weight = $weightToApply;
                $item->applied_at = now();
                $item->reviewed_by = $reviewer->id;
                $item->reviewed_at = now();
                $item->conflict_detected_at = null;
                $item->conflict_current_weight = null;
                if ($coachNotes !== null) {
                    $item->coach_notes = $coachNotes;
                }
                $item->save();

            } elseif ($action === 'reject' || $action === 'resolve_reject') {
                // الرفض: لا يغير الوزن في الخطة التدريبية نهائياً
                $item->status = TraineeLoadAdjustmentItem::STATUS_REJECTED;
                $item->reviewed_by = $reviewer->id;
                $item->reviewed_at = now();
                $item->conflict_detected_at = null;
                $item->conflict_current_weight = null;
                if ($coachNotes !== null) {
                    $item->coach_notes = $coachNotes;
                }
                $item->save();
            }

            // إعادة احتساب الحالة الإجمالية للتقرير الرئيسي
            $parentReport->recalculateAggregateStatus();

            return $item->fresh(['loadAdjustment', 'reviewer']);
        });
    }

    /**
     * مراجعة التقرير من قبل المدرب وتسجيل الملاحظات وحالة المراجعة (للتوافق القديم أو المراجعة الجماعية)
     *
     * @param int $reportId
     * @param array $data
     * @param User $reviewer
     * @return TraineeLoadAdjustment
     */
    public function reviewReport(int $reportId, array $data, User $reviewer): TraineeLoadAdjustment
    {
        return DB::transaction(function () use ($reportId, $data, $reviewer) {
            $report = TraineeLoadAdjustment::with(['items', 'exercise'])->lockForUpdate()->findOrFail($reportId);

            $status = $data['status'];
            $coachNotes = array_key_exists('coach_notes', $data) ? $data['coach_notes'] : null;

            $report->status = $status;
            if ($coachNotes !== null) {
                $report->coach_notes = $coachNotes;
            }
            $report->reviewed_by = $reviewer->id;
            $report->reviewed_at = now();
            $report->save();

            // إذا كان التقرير يضم عناصر مجموعات، يتم تحديث العناصر المعلقة وفق قرار المدرب
            if ($report->items->isNotEmpty()) {
                foreach ($report->items as $item) {
                    if ($item->isPending() || $item->hasConflict()) {
                        $action = ($status === 'reviewed') ? 'approve' : 'reject';
                        $this->reviewItem($item->id, [
                            'action'      => $action,
                            'coach_notes' => $coachNotes,
                            'force'       => true,
                        ], $reviewer);
                    }
                }
            }

            return $report->fresh(['items', 'reviewer']);
        });
    }
}
