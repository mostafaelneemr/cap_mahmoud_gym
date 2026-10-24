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

        $sets = $this->input('sets');
        if (is_array($sets)) {
            $cleanedSets = [];
            foreach ($sets as $s) {
                if (is_array($s) && isset($s['set_number'])) {
                    $cleanedSets[] = [
                        'set_number'       => (int) $s['set_number'],
                        'suggested_weight' => isset($s['suggested_weight']) ? trim((string) $s['suggested_weight']) : '',
                    ];
                }
            }
            $sets = !empty($cleanedSets) ? $cleanedSets : null;
        } else {
            $sets = null;
        }

        $this->merge([
            'reported_load' => $reportedLoad,
            'notes'         => $notes,
            'sets'          => $sets,
        ]);
    }

    /**
     * قواعد التحقق: يجب تقديم المجموعات المختارة أو الوزن أو الملاحظة
     */
    public function rules(): array
    {
        return [
            'exercise_id'             => 'required|integer|exists:exercises,id',
            'sets'                    => 'nullable|array|min:1|max:4',
            'sets.*.set_number'       => 'required_with:sets|integer|min:1|max:4',
            'sets.*.suggested_weight' => 'required_with:sets|string|max:50',
            'reported_load'           => 'nullable|string|max:191',
            'notes'                   => 'nullable|string|max:1000',
        ];
    }

    /**
     * التحقق المخصص من عدم تجاوز عدد مجموعات التمرين الفعلي
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $hasSets = !empty($this->input('sets'));
            $hasReportedLoad = !empty($this->input('reported_load'));
            $hasNotes = !empty($this->input('notes'));

            if (!$hasSets && !$hasReportedLoad && !$hasNotes) {
                $validator->errors()->add('reported_load', __('يجب إدخال الوزن الجديد أو اختيار مجموعة أو كتابة ملاحظة على الأقل.'));
                $validator->errors()->add('notes', __('يجب إدخال الوزن الجديد أو اختيار مجموعة أو كتابة ملاحظة على الأقل.'));
            }

            if ($hasSets && $this->input('exercise_id')) {
                $exercise = Exercise::find($this->input('exercise_id'));
                if ($exercise) {
                    $maxSets = $exercise->getTotalSetsCount();
                    $submittedSets = $this->input('sets', []);

                    $seen = [];
                    foreach ($submittedSets as $idx => $s) {
                        $setNum = (int) ($s['set_number'] ?? 0);
                        if ($setNum > $maxSets) {
                            $validator->errors()->add(
                                "sets.{$idx}.set_number",
                                __("رقم المجموعة المحدد (:num) يتجاوز عدد مجموعات التمرين الفعلي (:max).", [
                                    'num' => $setNum,
                                    'max' => $maxSets,
                                ])
                            );
                        }
                        if (in_array($setNum, $seen, true)) {
                            $validator->errors()->add("sets.{$idx}.set_number", __('لا يمكن تكرار نفس المجموعة في نفس الطلب.'));
                        }
                        $seen[] = $setNum;
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'exercise_id.required'           => __('التمرين محدد غير صالح.'),
            'exercise_id.exists'             => __('التمرين المحدد غير موجود.'),
            'sets.max'                       => __('الحد الأقصى للمجموعات في الطلب هو 4 مجموعات.'),
            'sets.*.set_number.required'     => __('رقم المجموعة مطلوب.'),
            'sets.*.set_number.min'          => __('رقم المجموعة يجب أن يبدأ من 1.'),
            'sets.*.set_number.max'          => __('رقم المجموعة لا يمكن أن يتجاوز 4.'),
            'sets.*.suggested_weight.required'=> __('الوزن المقترح للمجموعة مطلوب.'),
            'reported_load.max'              => __('يجب ألا يتجاوز الوزن المدخل 191 حرفاً.'),
            'notes.max'                      => __('يجب ألا تتجاوز الملاحظات 1000 حرف.'),
        ];
    }
}
