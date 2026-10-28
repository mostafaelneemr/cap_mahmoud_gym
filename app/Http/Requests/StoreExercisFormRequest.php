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
        $exercises = $this->all()['exercises'] ?? $this->input('exercises');
        if (is_array($exercises)) {
            foreach ($exercises as $key => $exData) {
                if (!is_array($exData)) {
                    continue;
                }
                $raw = $exData['internal_weight'] ?? ($exData['weight'] ?? null);
                $exercises[$key]['internal_weight'] = Exercise::normalizeWeights($raw);
                unset($exercises[$key]['weight']);

                if (empty($exData['type'])) {
                    $exercises[$key]['type'] = 'main';
                }

                if (isset($exData['link']) && is_string($exData['link'])) {
                    $link = trim($exData['link']);
                    if ($link !== '' && !preg_match('~^https?://~i', $link)) {
                        $exercises[$key]['link'] = 'https://' . $link;
                    } elseif ($link === '') {
                        $exercises[$key]['link'] = null;
                    }
                }
            }
            $this->merge(['exercises' => $exercises]);
        }

        if ($this->has('warmup')) {
            $this->merge(['warmup' => \App\Models\WorkoutPlan::sanitizeRichText($this->input('warmup'))]);
        }

        if ($this->has('post_workout')) {
            $this->merge(['post_workout' => \App\Models\WorkoutPlan::sanitizeRichText($this->input('post_workout'))]);
        }

        $rawWarmupLinks = $this->input('warmup_links');
        if (is_array($rawWarmupLinks)) {
            $cleaned = array_values(array_filter(
                array_map(function ($l) {
                    if (!is_scalar($l)) {
                        return '';
                    }
                    $trimmed = trim((string) $l);
                    if ($trimmed === '') {
                        return '';
                    }
                    if (!preg_match('~^https?://~i', $trimmed)) {
                        $trimmed = 'https://' . $trimmed;
                    }
                    return $trimmed;
                }, $rawWarmupLinks),
                fn($l) => $l !== ''
            ));
            $cleaned = array_slice($cleaned, 0, \App\Models\WorkoutPlan::MAX_WARMUP_LINKS);
            $this->merge(['warmup_links' => $cleaned]);
        }
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
                    'trainee_id' => 'nullable|integer',
                    'day_name' => 'nullable|string|max:100',
                    'warmup' => 'nullable|string',
                    'warmup_links' => ['nullable'],
                    'warmup_links.*' => ['nullable'],
                    'post_workout' => 'nullable|string',
                    'exercises' => 'nullable|array',
                    'exercises.*.exercise_id' => 'nullable|integer',
                    'exercises.*.exercise_library_id' => 'nullable|integer|exists:exercise_libraries,id',
                    'exercises.*.type' => 'required|in:warmup,main,post_workout',
                    'exercises.*.name' => 'nullable|string|max:150',
                    'exercises.*.sets' => 'nullable|string|max:50',
                    'exercises.*.reps' => 'nullable|string|max:50',
                    'exercises.*.rest' => 'nullable|string|max:50',
                    'exercises.*.internal_weight' => 'nullable|array',
                    'exercises.*.internal_weight.*' => 'nullable|string|max:20',
                    'exercises.*.tempo' => 'nullable|string|max:50',
                    'exercises.*.link' => 'nullable|url|max:255',
                    'exercises.*.machine_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
                    'exercises.*.remove_machine_image' => 'nullable',
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
