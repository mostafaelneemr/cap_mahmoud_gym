<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TraineeProgressRequest extends FormRequest
{
    public function authorize()
    {
        $authUser = \Illuminate\Support\Facades\Auth::guard('user')->user();
        if (!$authUser) {
            return false;
        }

        if (in_array((int) $authUser->user_type, [2, 3], true)) {
            $ownTrainee = \App\Models\Trainee::where('user_id', $authUser->id)->first();
            if (!$ownTrainee) {
                return false;
            }
            if ($this->filled('trainee_id') && (int) $this->input('trainee_id') !== (int) $ownTrainee->id) {
                return false;
            }
        }

        return true;
    }

    public function rules()
    {
        return [
            'trainee_id'   => 'nullable|exists:trainees,id',
            'checkin_date' => 'required|date',
            'weight'       => 'nullable|numeric|min:1|max:500',
            'notes'        => 'nullable|string|max:2000',
            'coach_notes'  => 'nullable|string|max:2000',
            'photos'       => 'nullable|array',
            'photos.*'     => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ];
    }

    public function messages()
    {
        return [
            'checkin_date.required' => __('The check-in date is required.'),
            'checkin_date.date'     => __('Please enter a valid date.'),
            'weight.numeric'        => __('Weight must be a valid number.'),
            'photos.*.image'        => __('All uploaded files must be images.'),
            'photos.*.mimes'        => __('Images must be in jpeg, png, jpg, or webp format.'),
            'photos.*.max'          => __('Maximum image size is 5MB per file.'),
        ];
    }
}
