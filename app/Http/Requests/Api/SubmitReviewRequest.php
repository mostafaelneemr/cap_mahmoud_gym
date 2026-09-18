<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubmitReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'    => 'required|string|min:2|max:255',
            'email'   => 'required|email|max:255',
            'rating'  => 'required|integer|min:1|max:5',
            'message' => 'required|string|min:10|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => 'Please enter your name.',
            'email.required'   => 'Please enter your email address.',
            'email.email'      => 'The email address format is invalid.',
            'rating.required'  => 'Please select a star rating.',
            'rating.min'       => 'Rating must be between 1 and 5 stars.',
            'rating.max'       => 'Rating must be between 1 and 5 stars.',
            'message.required' => 'Please write your review.',
            'message.min'      => 'Review must be at least 10 characters.',
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
