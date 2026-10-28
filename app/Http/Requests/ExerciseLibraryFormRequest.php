<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExerciseLibraryFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('video_url')) {
            $url = trim((string) $this->input('video_url'));
            if ($url !== '' && !preg_match('~^https?://~i', $url)) {
                $url = 'https://' . $url;
            } elseif ($url === '') {
                $url = null;
            }
            $this->merge(['video_url' => $url]);
        }

        if (!$this->has('status') || empty($this->input('status'))) {
            $this->merge(['status' => 'active']);
        }
    }

    public function rules(): array
    {
        switch ($this->method()) {
            case 'GET':
            case 'DELETE':
                return [];

            case 'POST':
            case 'PUT':
            case 'PATCH':
                return [
                    'name' => 'required|string|max:150',
                    'video_url' => 'nullable|url|max:255',
                    'description' => 'nullable|string',
                    'status' => 'required|in:active,inactive',
                ];

            default:
                return [];
        }
    }

    public function attributes(): array
    {
        return [
            'name' => __('Exercise Name'),
            'video_url' => __('Video URL'),
            'description' => __('Description'),
            'status' => __('Status'),
        ];
    }
}
