<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreExercisFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
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
                    'day_name_ar' => 'nullable|string|max:100',
                    'warmup' => 'nullable|string',
                    'warmup_ar' => 'nullable|string',
                    'post_workout' => 'nullable|string',
                    'post_workout_ar' => 'nullable|string',
                    'exercises' => 'nullable|array',
                    'exercises.*.exercise_id' => 'nullable|integer',
                    'exercises.*.name' => 'required_without:exercises.*.name_ar|nullable|string|max:150',
                    'exercises.*.name_ar' => 'nullable|string|max:150',
                    'exercises.*.sets' => 'nullable|string|max:50',
                    'exercises.*.reps' => 'nullable|string|max:50',
                    'exercises.*.rest' => 'nullable|string|max:50',
                    'exercises.*.weight' => 'nullable|string|max:100',
                    'exercises.*.tempo' => 'nullable|string|max:50',
                    'exercises.*.link' => 'nullable|url|max:255',
                    'exercises.*.notes_en' => 'nullable|string',
                    'exercises.*.notes_ar' => 'nullable|string',
                ];

            }
            default:break;
        }

    }
}
