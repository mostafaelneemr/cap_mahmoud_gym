<?php

namespace App\Services;

use App\Models\Trainee;
use App\Models\TraineeProgressCheckin;
use App\Models\TraineeProgressPhoto;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;

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
     * Process image payload (UploadedFile or base64 data string) into WebP format with 75% quality scaling.
     * Prevents raw base64 storage without passing through the compression service.
     */
    public function processAndCompressToWebp($imageInput): ?string
    {
        try {
            $manager = new ImageManager(new Driver());
            $img = null;

            if ($imageInput instanceof UploadedFile) {
                if (!$imageInput->isValid()) {
                    return null;
                }
                $img = $manager->decodePath($imageInput->getRealPath());
            } elseif (is_string($imageInput)) {
                $data = trim($imageInput);
                if (str_starts_with($data, 'data:image')) {
                    $img = $manager->decodeDataUri($data);
                } else {
                    $img = $manager->decodeBase64($data);
                }
            } else {
                return null;
            }

            if (!$img) {
                return null;
            }

            // WebP 75% scaling compression
            $encodedWebp = $img->encode(new WebpEncoder(75));
            $filename = 'progress_' . Str::random(16) . '.webp';
            $relativePath = 'progress_photos/' . $filename;

            Storage::disk('public')->put($relativePath, (string) $encodedWebp);

            return $relativePath;
        } catch (\Exception $e) {
            Log::error('WebP 75% Compression Service Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Store a new check-in with photos (handles multipart files & base64 payloads).
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

        // Process multipart file uploads through WebP 75% scaling compression service
        if (!empty($photoFiles) && is_array($photoFiles)) {
            foreach ($photoFiles as $index => $file) {
                $relativePath = $this->processAndCompressToWebp($file);
                if ($relativePath) {
                    TraineeProgressPhoto::create([
                        'checkin_id' => $checkin->id,
                        'photo_path' => $relativePath,
                        'caption'    => !empty($data['captions'][$index]) ? $data['captions'][$index] : null,
                    ]);
                }
            }
        }

        // Process base64 payload array if passed (e.g. camera capture)
        if (!empty($data['photos_base64']) && is_array($data['photos_base64'])) {
            foreach ($data['photos_base64'] as $index => $b64String) {
                $relativePath = $this->processAndCompressToWebp($b64String);
                if ($relativePath) {
                    TraineeProgressPhoto::create([
                        'checkin_id' => $checkin->id,
                        'photo_path' => $relativePath,
                        'caption'    => !empty($data['captions_base64'][$index]) ? $data['captions_base64'][$index] : null,
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
