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
                    'exercises' => 'nullable|array',
                    'exercises.*.exercise_id' => 'nullable|integer',
                    'exercises.*.name' => 'required|string',
                    'exercises.*.sets' => 'nullable|string',
                    'exercises.*.reps' => 'nullable|string',
                    'exercises.*.rest' => 'nullable|string',
                    'exercises.*.weight' => 'nullable|string',
                    'exercises.*.tempo' => 'nullable|string',
                    'exercises.*.link' => 'nullable|url',
                ];

            }
            default:break;
        }

    }
}
