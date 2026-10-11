<?php

namespace App\Modules\Api;

use App\Http\Controllers\Controller;
use App\Models\Trainee;
use App\Models\TraineeProgressCheckin;
use App\Models\TraineeProgressPhoto;
use App\Services\TraineeProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TraineePhotoApiController extends Controller
{
    protected $progressService;

    public function __construct(TraineeProgressService $progressService)
    {
        $this->progressService = $progressService;
    }

    /**
     * Handle progress photo upload with WebP 75% scaling compression.
     * Endpoint: POST /api/trainee/photos/upload
     */
    public function upload(Request $request)
    {
        $request->validate([
            'trainee_id'   => 'nullable|exists:trainees,id',
            'checkin_id'   => 'nullable|exists:trainee_progress_checkins,id',
            'checkin_date' => 'nullable|date',
            'photo'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'photo_base64' => 'nullable|string',
            'notes'        => 'nullable|string|max:2000',
            'weight'       => 'nullable|numeric',
        ]);

        try {
            $user = Auth::guard('sanctum')->user() ?? Auth::user();
            $trainee = null;

            if ($user && $user->user_type == 2) {
                $trainee = Trainee::where('user_id', $user->id)->first();
            } elseif ($request->filled('trainee_id')) {
                $trainee = Trainee::find($request->trainee_id);
            }

            if (!$trainee) {
                $trainee = Trainee::first();
            }

            if (!$trainee) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Trainee record not found.'
                ], 404);
            }

            // Create or fetch check-in session
            $checkinDate = $request->input('checkin_date', date('Y-m-d'));
            $checkin = null;

            if ($request->filled('checkin_id')) {
                $checkin = TraineeProgressCheckin::find($request->checkin_id);
            }

            if (!$checkin) {
                $checkin = TraineeProgressCheckin::create([
                    'trainee_id'   => $trainee->id,
                    'coach_id'     => ($user && $user->user_type != 2) ? $user->id : null,
                    'checkin_date' => $checkinDate,
                    'weight'       => $request->input('weight'),
                    'notes'        => $request->input('notes'),
                ]);
            }

            // Determine image payload source
            $imageInput = null;
            if ($request->hasFile('photo')) {
                $imageInput = $request->file('photo');
            } elseif ($request->filled('photo_base64')) {
                $imageInput = $request->input('photo_base64');
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'No photo file or base64 string provided.'
                ], 422);
            }

            // Route payload through WebP 75% scaling compression service
            $relativePath = $this->progressService->processAndCompressToWebp($imageInput);

            if (!$relativePath) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Failed to compress photo to WebP 75%. Please ensure payload is a valid image.'
                ], 422);
            }

            // Create Photo record
            $photoRecord = TraineeProgressPhoto::create([
                'checkin_id' => $checkin->id,
                'photo_path' => $relativePath,
                'caption'    => $request->input('caption'),
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Progress photo processed and compressed (WebP 75%) successfully!',
                'assistant_guidance' => 'Awesome progress! To ensure Coach Mahmoud gets the best assessment, make sure future photos are clear and well-lit from the front, side, and back views!',
                'data'    => [
                    'photo_id'   => $photoRecord->id,
                    'checkin_id' => $checkin->id,
                    'photo_url'  => $photoRecord->photo_url,
                    'format'     => 'webp',
                    'quality'    => '75%'
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to process photo: ' . $e->getMessage()
            ], 500);
        }
    }
}
