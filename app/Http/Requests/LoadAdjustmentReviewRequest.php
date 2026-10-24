<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LoadAdjustmentReviewRequest extends FormRequest
{
    /**
     * التحقق من أن المستخدم ليس متدرباً ويمتلك صلاحية مراجعة التقارير
     */
    public function authorize(): bool
    {
        $user = Auth::guard('user')->user();
        if (!$user) {
            return false;
        }

        // يمنع المتدربون من اعتماد أو مراجعة التقارير
        if ($user->user_type == 2) {
            return false;
        }

        return true;
    }

    /**
     * تنظيف الملاحظات قبل التحقق
     */
    protected function prepareForValidation(): void
    {
        $coachNotes = $this->input('coach_notes');
        if (is_string($coachNotes)) {
            $coachNotes = trim($coachNotes);
            $coachNotes = $coachNotes === '' ? null : $coachNotes;
        }

        $this->merge([
            'coach_notes' => $coachNotes,
        ]);
    }

    /**
     * قواعد التحقق لمراجعة المدرب
     */
    public function rules(): array
    {
        return [
            'status'      => 'required|in:reviewed,dismissed',
            'coach_notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => __('حالة المراجعة مطلوبة.'),
            'status.in'       => __('حالة المراجعة يجب أن تكون تمت المراجعة أو تم الإلغاء.'),
            'coach_notes.max' => __('يجب ألا تتجاوز ملاحظات المدرب 1000 حرف.'),
        ];
    }
}
