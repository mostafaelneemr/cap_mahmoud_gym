<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubmitContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'    => 'required|string|min:2|max:100',
            'phone'   => 'required|string|min:7|max:25',
            'email'   => 'required|email|max:100',
            'message' => 'required|string|min:10|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => 'Please enter your name.',
            'phone.required'   => 'Please enter your phone number.',
            'email.required'   => 'Please enter a valid email address.',
            'email.email'      => 'The email address format is invalid.',
            'message.required' => 'Please write your message.',
            'message.min'      => 'Message must be at least 10 characters.',
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
