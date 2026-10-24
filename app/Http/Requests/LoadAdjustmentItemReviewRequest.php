<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LoadAdjustmentItemReviewRequest extends FormRequest
{
    /**
     * التحقق من الصلاحيات: المدربون والمشرفون فقط
     */
    public function authorize(): bool
    {
        $user = Auth::guard('user')->user();
        if (!$user) {
            return false;
        }

        // يمنع المتدربون من مراجعة أو اعتماد التقارير
        if ($user->user_type == 2) {
            return false;
        }

        return true;
    }

    /**
     * تنظيف المدخلات قبل التحقق
     */
    protected function prepareForValidation(): void
    {
        $coachNotes = $this->input('coach_notes');
        if (is_string($coachNotes)) {
            $coachNotes = trim($coachNotes);
            $coachNotes = $coachNotes === '' ? null : $coachNotes;
        }

        $approvedWeight = $this->input('approved_weight');
        if (is_string($approvedWeight)) {
            $approvedWeight = trim($approvedWeight);
            $approvedWeight = $approvedWeight === '' ? null : $approvedWeight;
        }

        $this->merge([
            'coach_notes'     => $coachNotes,
            'approved_weight' => $approvedWeight,
            'force'           => filter_var($this->input('force'), FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function rules(): array
    {
        return [
            'action'          => 'required|string|in:approve,reject,approve_alternative,resolve_approve,resolve_reject',
            'approved_weight' => 'nullable|string|max:50|required_if:action,approve_alternative',
            'coach_notes'     => 'nullable|string|max:1000',
            'force'           => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'action.required'                   => __('إجراء المراجعة مطلوب.'),
            'action.in'                         => __('إجراء المراجعة المحدد غير صالح.'),
            'approved_weight.required_if'       => __('يرجى تحديد الوزن البديل المعتمد.'),
            'approved_weight.max'               => __('يجب ألا يتجاوز الوزن المعتمد 50 حرفاً.'),
            'coach_notes.max'                   => __('يجب ألا تتجاوز ملاحظات المدرب 1000 حرف.'),
        ];
    }
}
