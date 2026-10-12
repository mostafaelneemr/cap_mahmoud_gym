<?php

namespace App\Http\Requests;
use App\Models\Exercise;
use Illuminate\Foundation\Http\FormRequest;

class StoreExercisFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    /**
     * Clean the multi-weight input: drop empty/whitespace values and keep max 4.
     */
    protected function prepareForValidation()
    {
        $exercises = $this->input('exercises');
        if (!is_array($exercises)) {
            return;
        }

        foreach ($exercises as $key => $exData) {
            if (!is_array($exData)) {
                continue;
            }
            $raw = $exData['internal_weight'] ?? ($exData['weight'] ?? null);
            $exercises[$key]['internal_weight'] = Exercise::normalizeWeights($raw);
            unset($exercises[$key]['weight']);
        }

        $this->merge(['exercises' => $exercises]);
    }

    public function rules()
    {
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST':
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'day_name' => 'nullable|string|max:100',
                    'warmup' => 'nullable|string',
                    'post_workout' => 'nullable|string',
                    'exercises' => 'nullable|array',
                    'exercises.*.exercise_id' => 'nullable|integer',
                    'exercises.*.name' => 'nullable|string|max:150',
                    'exercises.*.sets' => 'nullable|string|max:50',
                    'exercises.*.reps' => 'nullable|string|max:50',
                    'exercises.*.rest' => 'nullable|string|max:50',
                    'exercises.*.internal_weight' => 'nullable|array',
                    'exercises.*.internal_weight.*' => 'nullable|string|max:20',
                    'exercises.*.tempo' => 'nullable|string|max:50',
                    'exercises.*.link' => 'nullable|url|max:255',
                    'exercises.*.notes_en' => 'nullable|string',

                ];

            }
            default:break;
        }

    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $data = $validator->getData();
            $exercises = $data['exercises'] ?? $this->input('exercises', []);
            $validCount = 0;

            if (is_array($exercises) && !empty($exercises)) {
                foreach ($exercises as $exData) {
                    if (is_array($exData) && !empty($exData['name']) && trim((string) $exData['name']) !== '') {
                        $validCount++;
                    }
                }
            }

            if ($validCount === 0) {
                $validator->errors()->add(
                    'exercises',
                    __('Cannot save workout day without exercises. Please provide at least one exercise.')
                );
            }
        });
    }
}
