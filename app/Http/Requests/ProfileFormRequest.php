<?php


namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class ProfileFormRequest extends FormRequest
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
        $rowid = auth()->user()->id;
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'email'      =>  'required|email|unique:user,email,'.$rowid.',id',
                    'default_language'     =>  'required|in:en-gb,ar',
                    'password'                  =>  'nullable|confirmed',
                    'password_confirmation'     =>  'required_with:password',
                ];
            }
            default:break;
        }

    }
}
