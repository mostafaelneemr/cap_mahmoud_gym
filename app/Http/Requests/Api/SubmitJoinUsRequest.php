<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubmitJoinUsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => 'required|string|min:2|max:255',
            'phone'          => 'required|string|min:7|max:50',
            'age'            => 'required|integer|min:10|max:80',
            'country'        => 'required|string|min:2|max:100',
            'governorate'    => 'required|string|min:2|max:100',
            'training_level' => 'required|string|min:2|max:100',
            'goal'           => 'required|string|min:5|max:1000',
            'injuries'       => 'required|string|min:1|max:255',
            'injury_details' => 'nullable|string|max:2000',
            'reason'         => 'required|string|min:5|max:1000',
            'routine'        => 'required|string|min:5|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'           => 'Please enter your full name.',
            'phone.required'          => 'Please enter your phone number.',
            'age.required'            => 'Please enter your age.',
            'age.integer'             => 'Age must be a valid number.',
            'age.min'                 => 'Age must be at least 10.',
            'age.max'                 => 'Age must not exceed 80.',
            'country.required'        => 'Please enter your country.',
            'governorate.required'    => 'Please enter your governorate/city.',
            'training_level.required' => 'Please enter your training level.',
            'goal.required'           => 'Please describe your training goal.',
            'injuries.required'       => 'Please answer the injuries question.',
            'reason.required'         => 'Please explain why you want to train.',
            'routine.required'        => 'Please describe your daily routine.',
        ];
    }

    /**
     * Return JSON validation errors for API consumers instead of a redirect.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
