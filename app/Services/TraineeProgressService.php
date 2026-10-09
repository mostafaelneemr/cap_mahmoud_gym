<?php

namespace App\Services;

use App\Models\Trainee;
use App\Models\TraineeProgressCheckin;
use App\Models\TraineeProgressPhoto;
use App\Models\User;
use Illuminate\Support\Str;

class TraineeProgressService extends BaseService
{
    /**
     * Resolve the trainee model based on input or current authenticated user.
     */
    public function resolveTrainee(User $authUser, $traineeId = null): ?Trainee
    {
        if ($authUser->user_type == 2) {
            // Logged-in user is a trainee
            return Trainee::where('user_id', $authUser->id)->first();
        }

        if ($traineeId) {
            return Trainee::find($traineeId);
        }

        return null;
    }

    /**
     * Get list of check-ins for a given trainee.
     */
    public function getCheckinsForTrainee(int $traineeId)
    {
        return TraineeProgressCheckin::with(['photos', 'coach', 'trainee.user'])
            ->where('trainee_id', $traineeId)
            ->orderBy('checkin_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Store a new check-in with optional photo uploads.
     */
    public function storeCheckin(array $data, User $authUser, $photoFiles = [])
    {
        $trainee = null;

        if ($authUser->user_type == 2) {
            $trainee = Trainee::where('user_id', $authUser->id)->first();
        } elseif (!empty($data['trainee_id'])) {
            $trainee = Trainee::find($data['trainee_id']);
        }

        if (!$trainee) {
            throw new \Exception(__('Trainee profile not found.'));
        }

        // Determine coach ID
        $coachId = null;
        if ($authUser->user_type != 2) {
            $coachId = $authUser->id;
        }

        $checkin = TraineeProgressCheckin::create([
            'trainee_id'   => $trainee->id,
            'coach_id'     => $coachId,
            'checkin_date' => $data['checkin_date'] ?? now()->toDateString(),
            'weight'       => !empty($data['weight']) ? $data['weight'] : null,
            'notes'        => !empty($data['notes']) ? $data['notes'] : null,
            'coach_notes'  => !empty($data['coach_notes']) ? $data['coach_notes'] : null,
        ]);

        // Upload photos if provided
        if (!empty($photoFiles) && is_array($photoFiles)) {
            foreach ($photoFiles as $index => $file) {
                if ($file && $file->isValid()) {
                    $filename = 'progress_' . $checkin->id . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('progress_photos', $filename, 'public');

                    TraineeProgressPhoto::create([
                        'checkin_id' => $checkin->id,
                        'photo_path' => $path,
                        'caption'    => !empty($data['captions'][$index]) ? $data['captions'][$index] : null,
                    ]);
                }
            }
        }

        return $checkin->load('photos');
    }

    /**
     * Update coach notes for a specific check-in.
     */
    public function updateCoachNotes(int $checkinId, ?string $coachNotes, User $authUser)
    {
        $checkin = TraineeProgressCheckin::findOrFail($checkinId);

        // Security check: only coaches/admins can update coach notes
        if ($authUser->user_type == 2) {
            throw new \Exception(__('Unauthorized to update coach feedback.'));
        }

        $checkin->update([
            'coach_notes' => $coachNotes,
            'coach_id'    => $authUser->id,
        ]);

        return $checkin;
    }

    /**
     * Delete a check-in and all associated photo files.
     */
    public function deleteCheckin(int $checkinId, User $authUser)
    {
        $checkin = TraineeProgressCheckin::with('photos')->findOrFail($checkinId);

        // Security check
        if ($authUser->user_type == 2) {
            $trainee = Trainee::where('user_id', $authUser->id)->first();
            if (!$trainee || $checkin->trainee_id != $trainee->id) {
                throw new \Exception(__('Unauthorized to delete this check-in.'));
            }
        }

        return $checkin->delete();
    }

    /**
     * Delete an individual photo.
     */
    public function deletePhoto(int $photoId, User $authUser)
    {
        $photo = TraineeProgressPhoto::with('checkin')->findOrFail($photoId);

        // Security check
        if ($authUser->user_type == 2) {
            $trainee = Trainee::where('user_id', $authUser->id)->first();
            if (!$trainee || $photo->checkin->trainee_id != $trainee->id) {
                throw new \Exception(__('Unauthorized to delete this photo.'));
            }
        }

        return $photo->delete();
    }

    /**
     * Get comparison data for side-by-side view.
     */
    public function getComparisonData(int $traineeId, ?string $date1 = null, ?string $date2 = null)
    {
        $checkin1 = null;
        $checkin2 = null;

        if ($date1) {
            $checkin1 = TraineeProgressCheckin::with('photos')
                ->where('trainee_id', $traineeId)
                ->where('checkin_date', $date1)
                ->first();
        }

        if ($date2) {
            $checkin2 = TraineeProgressCheckin::with('photos')
                ->where('trainee_id', $traineeId)
                ->where('checkin_date', $date2)
                ->first();
        }

        return [
            'checkin1' => $checkin1,
            'checkin2' => $checkin2,
        ];
    }
}
