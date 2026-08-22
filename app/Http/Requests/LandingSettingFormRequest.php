<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LandingSettingFormRequest extends FormRequest
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
        return [
            'hero_title_ar' => 'nullable|string|max:255',
            'hero_title_en' => 'nullable|string|max:255',
            'hero_subtitle_ar' => 'nullable|string',
            'hero_subtitle_en' => 'nullable|string',
            'hero_button_text' => 'nullable|string|max:100',
            'hero_button_link' => 'nullable|string|max:255',
            'hero_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'about_bio_ar' => 'nullable|string',
            'about_bio_en' => 'nullable|string',
            'about_experience' => 'nullable|string|max:100',
            'about_clients' => 'nullable|string|max:100',
            'about_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
        ];
    }

    /**
     * Custom attribute names for validation errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'hero_title_ar' => __('Hero Title (Arabic)'),
            'hero_title_en' => __('Hero Title (English)'),
            'hero_subtitle_ar' => __('Hero Subtitle (Arabic)'),
            'hero_subtitle_en' => __('Hero Subtitle (English)'),
            'hero_button_text' => __('CTA Button Text'),
            'hero_button_link' => __('CTA Button Link'),
            'hero_image' => __('Hero Image'),
            'about_bio_ar' => __('Coach Bio (Arabic)'),
            'about_bio_en' => __('Coach Bio (English)'),
            'about_experience' => __('Years of Experience'),
            'about_clients' => __('Transformed Clients'),
            'about_image' => __('Coach Photo'),
        ];
    }
}
