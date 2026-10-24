<?php

namespace App\Http\Requests;

use App\Models\Exercise;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LoadAdjustmentReportRequest extends FormRequest
{
    /**
     * التحقق من الصلاحيات وأهلية المتدرب لتقديم تقرير تعديل الحمل
     */
    public function authorize(): bool
    {
        $user = Auth::guard('user')->user();
        if (!$user) {
            return false;
        }

        // استنتاج سجل المتدرب في الخادم لمنع التلاعب
        $trainee = $user->trainee;
        if (!$trainee) {
            return false;
        }

        // منع محاولات التلاعب بالمعرف IDOR
        if ($this->has('trainee_id') && (int) $this->input('trainee_id') !== (int) $trainee->id) {
            return false;
        }

        $exerciseId = $this->input('exercise_id');
        if (!$exerciseId) {
            return false;
        }

        $exercise = Exercise::with('workoutPlan')->find($exerciseId);
        if (!$exercise || !$exercise->workoutPlan) {
            return false;
        }

        // التحقق من أن التمرين ينتمي لخطة المتدرب نفسه وأن الخطة نشطة وليست مؤرشفة
        if ((int) $exercise->workoutPlan->trainee_id !== (int) $trainee->id) {
            return false;
        }

        if ($exercise->workoutPlan->status !== 'active') {
            return false;
        }

        return true;
    }

    /**
     * تهيئة وتنظيف المدخلات قبل التحقق
     */
    protected function prepareForValidation(): void
    {
        $reportedLoad = $this->input('reported_load');
        if (is_string($reportedLoad)) {
            $reportedLoad = trim($reportedLoad);
            $reportedLoad = $reportedLoad === '' ? null : $reportedLoad;
        }

        $notes = $this->input('notes');
        if (is_string($notes)) {
            $notes = trim($notes);
            $notes = $notes === '' ? null : $notes;
        }

        $this->merge([
            'reported_load' => $reportedLoad,
            'notes'         => $notes,
        ]);
    }

    /**
     * قواعد التحقق: يجب تقديم الوزن الجديد أو الملاحظة على الأقل
     */
    public function rules(): array
    {
        return [
            'exercise_id'   => 'required|integer|exists:exercises,id',
            'reported_load' => 'nullable|string|max:191|required_without:notes',
            'notes'         => 'nullable|string|max:1000|required_without:reported_load',
        ];
    }

    public function messages(): array
    {
        return [
            'exercise_id.required'           => __('التمرين محدد غير صالح.'),
            'exercise_id.exists'             => __('التمرين المحدد غير موجود.'),
            'reported_load.required_without' => __('يجب إدخال الوزن الجديد أو كتابة ملاحظة على الأقل.'),
            'reported_load.max'              => __('يجب ألا يتجاوز الوزن المدخل 191 حرفاً.'),
            'notes.required_without'         => __('يجب إدخال الوزن الجديد أو كتابة ملاحظة على الأقل.'),
            'notes.max'                      => __('يجب ألا تتجاوز الملاحظات 1000 حرف.'),
        ];
    }
}
