<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TraineeFormRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        switch ($this->method()) {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST':
            {
                return [
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|unique:trainees,email',
                    'status' => 'required|in:active,inactive,expired',
                    'password' => 'nullable|confirmed',
                    'password_confirmation' => 'required_with:password',
                    'membership_start' => 'required|date',
                    'membership_end' => 'required|date|after:membership_start',
                    'weight' => 'nullable|numeric|decimal:0,2|min:0|max:999.99',
                    'height' => 'nullable|numeric|decimal:0,2|min:0|max:999.99',
                    'age' => 'nullable|integer|min:0|max:150',
                    'training_level' => 'required|in:beginner,intermediate,advanced',
                ];

            }
            case 'PUT':
            case 'PATCH':
            {
                $trainee = $this->route('trainee');
                $traineeId = is_object($trainee) ? $trainee->id : $this->segment(3);
                return [
                    'name' => 'required|string|max:255',
                    'email' => ['required', 'email', Rule::unique('trainees', 'email')->ignore($traineeId)],
                    'status' => 'required|in:active,inactive,expired',
                    'password' => 'nullable|confirmed',
                    'password_confirmation' => 'required_with:password',
                    'membership_start' => 'required|date',
                    'membership_end' => 'required|date|after:membership_start',
                    'weight' => 'nullable|numeric|decimal:0,2|min:0|max:999.99',
                    'height' => 'nullable|numeric|decimal:0,2|min:0|max:999.99',
                    'age' => 'nullable|integer|min:0',
                    'training_level' => 'required|in:beginner,intermediate,advanced',
                ];
            }
            default:
                break;
        }

    }

    public function messages()
    {
        return [
            'telephone.min' => __('The phone number must be at least 9 digits long'),
            'telephone.max' => __('The phone number must be at most 12 digits long'),
        ];
    }
}
