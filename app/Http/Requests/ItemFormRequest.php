<?php

namespace App\Http\Requests;

use App\Repositories\Language\LanguageRepository;
use Illuminate\Foundation\Http\FormRequest;

class ItemFormRequest extends FormRequest
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
                $lang = new LanguageRepository();
                $languages = $lang->getWhere(['status' => 1]);

                $validation = [];

                foreach ($languages as $language) {
                    $validation['input.lang.' . $language->id . '.title'] = 'required';
                }
                $validation['status'] = 'required|in:active,inactive';
                $validation['image'] = 'required|image|mimes:jpeg,jpg,png|max:2048';

                return $validation;

            }

            case 'PUT':
            case 'PATCH':
            {
                $lang = new LanguageRepository();
                $languages = $lang->getWhere(['status' => 1]);

                $validation = [];

                foreach ($languages as $language) {
                    $validation['input.lang.' . $language->id . '.title'] = 'required';
                }
                $validation['status'] = 'required|in:active,inactive';
                $validation['image'] = 'image|mimes:jpeg,jpg,png|max:2048';

                return $validation;

            }

            default:
                break;
        }

    }

    public function attributes()
    {
        $lang = new LanguageRepository();
        $languages = $lang->getWhere(['status' => 1]);
        $messages = [];
        foreach ($languages as $language) {
            $messages  ['input.lang.' . $language->id . '.title'] = __('Title');
            $messages  ['input.lang.' . $language->id . '.description'] = __('Description');
        }
        return $messages;
    }
}
