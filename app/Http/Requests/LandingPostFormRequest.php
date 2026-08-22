<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LandingPostFormRequest extends FormRequest
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
        switch ($this->method()) {
            case 'GET':
            case 'DELETE': {
                return [];
            }
            case 'POST':
            case 'PUT':
            case 'PATCH': {
                return [
                    'type' => 'required|in:transformation,pricing_plan,faq,testimonial,service,why_us,review',
                    'title' => 'required|string|max:255',
                    'subtitle' => 'nullable|string|max:255',
                    'description' => 'nullable|string',
                    'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
                    'extra_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
                    'link' => 'nullable|string|max:255',
                    'button_text' => 'nullable|string|max:100',
                    'price' => 'nullable|string|max:50',
                    'features' => 'nullable',
                    'sort_order' => 'nullable|integer',
                    'status' => 'nullable|in:active,inactive',
                ];
            }
            default:
                break;
        }
        return [];
    }

    /**
     * Custom attribute names for validation errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'type' => __('Type'),
            'title' => __('Title'),
            'subtitle' => __('Subtitle'),
            'description' => __('Description'),
            'image' => __('Image'),
            'extra_image' => __('Before Image'),
            'link' => __('Link'),
            'button_text' => __('Button Text'),
            'price' => __('Price'),
            'features' => __('Features'),
            'sort_order' => __('Sort Order'),
            'status' => __('Status'),
        ];
    }
}
