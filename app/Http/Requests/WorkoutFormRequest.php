<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkoutFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $programs = $this->all()['program'] ?? $this->input('program');
        if (is_array($programs)) {
            foreach ($programs as $dayKey => $dayData) {
                if (array_key_exists('warmup', $dayData)) {
                    $programs[$dayKey]['warmup'] = \App\Models\WorkoutPlan::sanitizeRichText($dayData['warmup']);
                }
                if (array_key_exists('post_workout', $dayData)) {
                    $programs[$dayKey]['post_workout'] = \App\Models\WorkoutPlan::sanitizeRichText($dayData['post_workout']);
                }
                if (!isset($dayData['exercises']) || !is_array($dayData['exercises'])) {
                    continue;
                }
                foreach ($dayData['exercises'] as $exKey => $exData) {
                    if (!is_array($exData)) {
                        continue;
                    }
                    if (empty($exData['type'])) {
                        $programs[$dayKey]['exercises'][$exKey]['type'] = 'main';
                    }
                }
            }
            $this->merge(['program' => $programs]);
        }
    }

    public function rules(): array
    {
        return [
            'trainee_id' => 'required|integer|exists:trainees,id',
            'program' => 'required|array|min:1',
            'program.*.warmup' => 'nullable|string',
            'program.*.post_workout' => 'nullable|string',
            'program.*.exercises.*.type' => 'required|in:warmup,main,post_workout',
            'program.*.exercises.*.exercise_library_id' => 'nullable|integer|exists:exercise_libraries,id',
            'program.*.exercises.*.machine_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $data = $validator->getData();
            $programs = $data['program'] ?? $this->input('program', []);

            if (!is_array($programs) || empty($programs)) {
                $validator->errors()->add('program', __('Please configure at least one workout day.'));
                return;
            }

            $totalExercisesWithNames = 0;
            $dayIndex = 0;

            foreach ($programs as $dayKey => $dayData) {
                $dayIndex++;
                $dayName = !empty($dayData['day_name'])
                    ? $dayData['day_name']
                    : __('Day :num', ['num' => $dayIndex]);

                $exercises = $dayData['exercises'] ?? [];
                $dayValidCount = 0;

                if (is_array($exercises) && !empty($exercises)) {
                    foreach ($exercises as $exKey => $exData) {
                        $exName = isset($exData['name']) ? trim((string) $exData['name']) : '';
                        if ($exName !== '') {
                            $dayValidCount++;
                            $totalExercisesWithNames++;
                        }
                    }
                }

                if ($dayValidCount === 0) {
                    $validator->errors()->add(
                        "program.{$dayKey}.exercises",
                        __(':day must have at least one exercise. A workout plan cannot be approved without exercises.', [
                            'day' => $dayName,
                        ])
                    );
                }
            }

            if ($totalExercisesWithNames === 0) {
                $validator->errors()->add(
                    'exercises',
                    __('Cannot approve workout plan without exercises. Please add exercises to the program.')
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'trainee_id.required' => __('Please select a trainee.'),
            'trainee_id.exists' => __('The selected trainee is invalid.'),
            'program.required' => __('Please configure at least one workout day.'),
            'program.min' => __('Please configure at least one workout day.'),
        ];
    }

    public function attributes(): array
    {
        return [
            'trainee_id' => __('Trainee'),
            'program' => __('Program'),
            'exercises' => __('Exercises'),
        ];
    }
}
