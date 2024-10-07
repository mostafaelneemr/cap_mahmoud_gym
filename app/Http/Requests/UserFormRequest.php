<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class UserFormRequest extends FormRequest
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
     * @return array
     */
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
                    'two_fa_secret'                     =>  'nullable',
                    'status'     =>  'required|in:1,0',
                    'force_reset_password'     =>  'required|in:1,0',
                    'permission_group_id'=> 'required|exists:permission_groups,id',
                    'department_id'=> 'nullable|exists:department,id',
                    'password'                  =>  'required|confirmed',
                    'password_confirmation'     =>  'required',
                ];

            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'name' =>  'required',
                    'email'      =>  'required|email|unique:user,email,'.$rowid.',id',
                    'two_fa_secret'                     =>  'nullable',
                    'status'     =>  'required|in:1,0',
                    'permission_group_id'=> 'required|exists:permission_groups,id',
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
