<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LoadAdjustmentReviewRequest extends FormRequest
{
    /**
     * التحقق من صلاحية المستخدم لمراجعة تقرير تعديل الحمل.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $authUser = Auth::guard('user')->user();
        if (!$authUser) {
            return false;
        }

        // منع المتدربين من مراجعة أو تعديل حالة التقارير
        if (in_array((int) $authUser->user_type, [2, 3], true)) {
            return false;
        }

        return true;
    }

    /**
     * قواعد التحقق من مدخلات مراجعة المدرب.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status'      => 'required|in:pending,reviewed,dismissed',
            'coach_notes' => 'nullable|string|max:1000',
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
            'status.required' => __('Review status is required.'),
            'status.in'       => __('Invalid review status selected.'),
            'coach_notes.max' => __('Coach notes must not exceed 1000 characters.'),
        ];
    }
}
