<?php

namespace App\Services;

use App\Models\Exercise;
use App\Models\TraineeLoadAdjustment;
use App\Models\User;

/**
 * خدمة إدارة تقارير تحديث أوزان التمارين للمتدربين ومراجعتها من قبل المدربين.
 * تضمن هذه الخدمة عدم تعديل الأوزان المقررة في الخطة التدريبية المعتمدة إطلاقاً.
 */
class LoadAdjustmentService
{
    /**
     * حفظ تقرير تعديل حمل جديد للمتدرب كلقطة تاريخية منفصلة
     *
     * @param array $data
     * @param User $user
     * @return TraineeLoadAdjustment
     */
    public function storeReport(array $data, User $user): TraineeLoadAdjustment
    {
        $trainee = $user->trainee;
        $exercise = Exercise::with('workoutPlan')->findOrFail($data['exercise_id']);
        $workoutPlan = $exercise->workoutPlan;

        // التقاط لقطات ثابتة لبيانات التمرين واليوم التدريبي في لحظة التقديم
        $workoutDayName = $workoutPlan ? $workoutPlan->day_name : null;
        $exerciseName = $exercise->name;
        $prescribedLoad = $exercise->internal_weight_display ?? '';

        return TraineeLoadAdjustment::create([
            'trainee_id'       => $trainee->id,
            'workout_plan_id'  => $exercise->workout_plan_id,
            'exercise_id'      => $exercise->id,
            'workout_day_name' => $workoutDayName,
            'exercise_name'    => $exerciseName,
            'prescribed_load'  => $prescribedLoad,
            'reported_load'    => $data['reported_load'] ?? null,
            'notes'            => $data['notes'] ?? null,
            'status'           => 'pending',
            'coach_notes'      => null,
            'reviewed_by'      => null,
            'reviewed_at'      => null,
        ]);
    }

    /**
     * مراجعة التقرير من قبل المدرب وتسجيل الملاحظات وحالة المراجعة
     *
     * @param int $reportId
     * @param array $data
     * @param User $reviewer
     * @return TraineeLoadAdjustment
     */
    public function reviewReport(int $reportId, array $data, User $reviewer): TraineeLoadAdjustment
    {
        $report = TraineeLoadAdjustment::findOrFail($reportId);

        $report->status = $data['status'];
        if (array_key_exists('coach_notes', $data)) {
            $report->coach_notes = $data['coach_notes'];
        }
        $report->reviewed_by = $reviewer->id;
        $report->reviewed_at = now();
        $report->save();

        return $report;
    }
}
