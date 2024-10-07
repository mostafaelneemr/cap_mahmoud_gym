<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'password'                  =>  'required|confirmed',
            'password_confirmation'     =>  'required',
        ];
    }

    public function messages()
    {
        return [
            'password.required' =>__('The password field is required.',[],Auth::guard('user')->user()->default_language),
            'password.confirmed' =>__('The password confirmation does not match.',[],Auth::guard('user')->user()->default_language),
             'password_confirmation.required' =>__('The confirmation password field is required.',[],Auth::guard('user')->user()->default_language)
        ];
    }
}
