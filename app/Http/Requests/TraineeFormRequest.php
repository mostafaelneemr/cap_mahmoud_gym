<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Crypt;

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
                    'email' => 'required|email|unique:user,email',
                    'status' => 'required|in:active,inactive,expired',
//                    'password' => 'required|confirmed',
//                    'password_confirmation' => 'required',
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
                $traineeParam = $this->route('trainee') ?: $this->segment(3);
                $userId = null;

                if (is_object($traineeParam)) {
                    $userId = $traineeParam->user_id ?? $traineeParam->id;
                } elseif (!empty($traineeParam)) {
                    $resolvedId = $traineeParam;
                    if (is_string($resolvedId) && !ctype_digit($resolvedId)) {
                        try {
                            $resolvedId = Crypt::decrypt($resolvedId);
                        } catch (\Exception $e) {
                            // فشل فك التشفير، يبقى كما هو
                        }
                    }

                    $traineeRec = \App\Models\Trainee::find($resolvedId);
                    if ($traineeRec) {
                        $userId = $traineeRec->user_id;
                    } else {
                        $userRec = \App\Models\User::find($resolvedId);
                        if ($userRec) {
                            $userId = $userRec->id;
                        } else {
                            $userId = $resolvedId;
                        }
                    }
                }

                return [
                    'name' => 'required|string|max:255',
                    'email' => ['required', 'email', Rule::unique('user', 'email')->ignore($userId)],
                    'status' => 'required|in:active,inactive,expired',
                    'password' => 'nullable|string|min:6',
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
