<?php

namespace App\Http\Requests;

use App\Models\Exercise;
use App\Models\Trainee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LoadAdjustmentReportRequest extends FormRequest
{
    /**
     * التحقق من صلاحية المستخدم لتقديم تقرير تعديل الحمل.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $authUser = Auth::guard('user')->user();
        if (!$authUser) {
            return false;
        }

        // إذا كان المستخدم متدرباً (user_type = 2 أو 3)
        if (in_array((int) $authUser->user_type, [2, 3], true)) {
            $ownTrainee = Trainee::where('user_id', $authUser->id)->first();
            if (!$ownTrainee) {
                return false;
            }

            // منع التزوير المباشر لمعرف المتدرب (IDOR Prevention)
            if ($this->filled('trainee_id') && (int) $this->input('trainee_id') !== (int) $ownTrainee->id) {
                return false;
            }

            // التحقق من ملكية التمرين والخطة التدريبية
            $exerciseId = $this->input('exercise_id');
            if ($exerciseId) {
                $exercise = Exercise::with('workoutPlan')->find($exerciseId);
                if (!$exercise || !$exercise->workoutPlan) {
                    return false;
                }

                // يجب أن يتبع التمرين خطة نفس المتدرب حصراً
                if ((int) $exercise->workoutPlan->trainee_id !== (int) $ownTrainee->id) {
                    return false;
                }

                // رفض التقديم للخطط المؤرشفة
                if ($exercise->workoutPlan->status === 'archived') {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * قواعد التحقق من مدخلات تقرير تعديل الحمل.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'exercise_id'   => 'required|integer|exists:exercises,id',
            'reported_load' => 'required|string|min:1|max:100',
            'notes'         => 'nullable|string|max:1000',
        ];
    }

    /**
     * رسائل الخطأ المترجمة.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'exercise_id.required'   => __('Exercise identifier is required.'),
            'exercise_id.exists'     => __('The selected exercise does not exist.'),
            'reported_load.required' => __('The reported load is required.'),
            'reported_load.max'      => __('The reported load must not exceed 100 characters.'),
            'notes.max'              => __('Notes must not exceed 1000 characters.'),
        ];
    }
}
