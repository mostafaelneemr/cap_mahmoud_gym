<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class SiteFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST':
            case 'PUT':
            case 'PATCH':{
                return [
                    'title' =>  'required',
                    'link_url'      =>  'required|url|regex:/^https?:\/\//i',
                    'icon'      =>  'required',
                    'status'     =>  'required|in:active,inactive',
                ];
            }
            default:break;
        }

    }
}
