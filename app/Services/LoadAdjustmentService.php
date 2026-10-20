<?php

namespace App\Services;

use App\Models\Exercise;
use App\Models\Trainee;
use App\Models\TraineeLoadAdjustment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LoadAdjustmentService extends BaseService
{
    /**
     * استخراج وتحديد نموذج المتدرب بناءً على المستخدم المصادق أو المعرف الممرر.
     *
     * @param User $authUser
     * @param int|null $traineeId
     * @return Trainee|null
     */
    public function resolveTrainee(User $authUser, $traineeId = null): ?Trainee
    {
        if (in_array((int) $authUser->user_type, [2, 3], true)) {
            // المتدرب المصادق
            return Trainee::where('user_id', $authUser->id)->first();
        }

        if ($traineeId) {
            return Trainee::find($traineeId);
        }

        return null;
    }

    /**
     * حفظ تقرير تعديل الحمل التدريبي دون المساس بالخطة المعتمدة إطلاقاً.
     *
     * @param array $data
     * @param User $authUser
     * @return TraineeLoadAdjustment
     * @throws \Exception
     */
    public function storeReport(array $data, User $authUser): TraineeLoadAdjustment
    {
        $trainee = $this->resolveTrainee($authUser, $data['trainee_id'] ?? null);
        if (!$trainee) {
            abort(404, __('Trainee profile not found.'));
        }

        // جلب التمرين واليوم التدريبي المرتبط
        $exercise = Exercise::with('workoutPlan')->findOrFail((int) $data['exercise_id']);

        // التحقق الأمني من الملكية
        if (in_array((int) $authUser->user_type, [2, 3], true)) {
            if (!$exercise->workoutPlan || (int) $exercise->workoutPlan->trainee_id !== (int) $trainee->id) {
                abort(403, __('Unauthorized to submit load adjustment for this exercise.'));
            }

            if ($exercise->workoutPlan->status === 'archived') {
                abort(422, __('Cannot submit load adjustment for an archived workout program.'));
            }
        }

        // أخذ لقطات السياق التاريخي الثابتة لحماية التقرير عند حذف أو تعديل التمرين مستقبلاً
        $workoutDayName = $exercise->workoutPlan ? ($exercise->workoutPlan->day_name ?: __('Workout Day')) : __('Workout Day');
        $exerciseName = $exercise->name;
        $prescribedLoad = $exercise->internal_weight_display ?: __('No prescribed load');
        $traineeNameSnapshot = $trainee->name ?: ($authUser->name ?: __('Trainee'));

        $reportedLoad = strip_tags(trim((string) $data['reported_load']));
        $notes = !empty($data['notes']) ? strip_tags(trim((string) $data['notes'])) : null;

        return DB::transaction(function () use (
            $trainee,
            $exercise,
            $workoutDayName,
            $exerciseName,
            $prescribedLoad,
            $traineeNameSnapshot,
            $reportedLoad,
            $notes
        ) {
            return TraineeLoadAdjustment::create([
                'trainee_id'            => $trainee->id,
                'trainee_name_snapshot' => $traineeNameSnapshot,
                'workout_plan_id'       => $exercise->workout_plan_id,
                'exercise_id'           => $exercise->id,
                'workout_day_name'      => $workoutDayName,
                'exercise_name'         => $exerciseName,
                'prescribed_load'       => $prescribedLoad,
                'reported_load'         => $reportedLoad,
                'notes'                 => $notes,
                'status'                => TraineeLoadAdjustment::STATUS_PENDING,
                'coach_notes'           => null,
                'reviewed_by'           => null,
                'reviewed_at'           => null,
            ]);
        });
    }

    /**
     * مراجعة التقرير من قبل المدرب وتحديث حالته وملاحظاته.
     *
     * @param int $reportId
     * @param array $data
     * @param User $reviewer
     * @return TraineeLoadAdjustment
     */
    public function reviewReport(int $reportId, array $data, User $reviewer): TraineeLoadAdjustment
    {
        if (in_array((int) $reviewer->user_type, [2, 3], true)) {
            abort(403, __('Unauthorized to review load adjustment reports.'));
        }

        $report = TraineeLoadAdjustment::findOrFail($reportId);

        $report->status = $data['status'];
        if (array_key_exists('coach_notes', $data)) {
            $report->coach_notes = !empty($data['coach_notes']) ? strip_tags(trim((string) $data['coach_notes'])) : null;
        }

        $report->reviewed_by = $reviewer->id;
        $report->reviewed_at = now();
        $report->save();

        return $report;
    }

    /**
     * استرجاع السجل التاريخي لتقارير متدرب معين.
     *
     * @param int $traineeId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getReportsForTrainee(int $traineeId)
    {
        return TraineeLoadAdjustment::with(['exercise', 'workoutPlan', 'reviewer'])
            ->where('trainee_id', $traineeId)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * استرجاع التقارير الخاصة بتمرين محدد للمتدرب.
     *
     * @param int $exerciseId
     * @param int $traineeId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getReportsForExercise(int $exerciseId, int $traineeId)
    {
        return TraineeLoadAdjustment::with(['reviewer'])
            ->where('exercise_id', $exerciseId)
            ->where('trainee_id', $traineeId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * حساب عدد التقارير المعلقة لمتدرب معين.
     *
     * @param int $traineeId
     * @return int
     */
    public function getPendingCountForTrainee(int $traineeId): int
    {
        return TraineeLoadAdjustment::where('trainee_id', $traineeId)
            ->where('status', TraineeLoadAdjustment::STATUS_PENDING)
            ->count();
    }
}
