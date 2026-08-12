<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NutritionFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        switch ($this->method()) {
            case 'GET':
            case 'DELETE': {
                return [];
            }
            case 'POST':
            case 'PUT':
            case 'PATCH': {
                return [
                    'trainee_id' => 'required',
                    'name' => 'nullable|string|max:255',
                    'description' => 'nullable|string',
                ];
            }
            default:
                break;
        }
    }
}
