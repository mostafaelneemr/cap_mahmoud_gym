<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class TraineeFormRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rowid = $this->segment(3);
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST': {
                return [
                    'name' =>  'required',
                    'email'      =>  'required|email|unique:user,email',
                    'status'     =>  'required|in:1,0',
                    'password'                  =>  'required|confirmed',
                    'password_confirmation'     =>  'required',
                    'membership_start' => 'required|date',
                    'membership_end' => 'required|date|after:membership_start',
                ];

            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'name' =>  'required',
                    'email'      =>  'required|email|unique:user,email,'.$rowid.',id',
                    'status'     =>  'required|in:1,0',
                    'password'                  =>  'nullable|confirmed',
                    'password_confirmation'     =>  'required_with:password',
                ];
            }
            default:break;
        }

    }
    public function messages()
    {
        return [
            'telephone.min' =>__('The phone number must be at least 9 digits long'),
            'telephone.max' =>__('The phone number must be at most 12 digits long'),
        ];
    }
}
