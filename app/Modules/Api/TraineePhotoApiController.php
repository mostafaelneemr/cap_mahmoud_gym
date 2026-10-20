<?php

namespace App\Modules\Api;

use App\Http\Controllers\Controller;
use App\Models\Trainee;
use App\Models\TraineeProgressCheckin;
use App\Models\TraineeProgressPhoto;
use App\Services\TraineeProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        $user = Auth::guard('sanctum')->user() ?? Auth::user();
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

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
            $trainee = null;
            $isTrainee = in_array((int) $user->user_type, [2, 3], true);

            if ($isTrainee) {
                $trainee = Trainee::where('user_id', $user->id)->first();
                if (!$trainee) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Trainee profile not found.'
                    ], 404);
                }

                if ($request->filled('trainee_id') && (int) $request->trainee_id !== (int) $trainee->id) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Unauthorized access to trainee.'
                    ], 403);
                }
            } elseif ($request->filled('trainee_id')) {
                $trainee = Trainee::find($request->trainee_id);
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
                if (!$checkin || (int) $checkin->trainee_id !== (int) $trainee->id) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Unauthorized check-in record.'
                    ], 403);
                }
            }

            if (!$checkin) {
                $checkin = TraineeProgressCheckin::create([
                    'trainee_id'   => $trainee->id,
                    'coach_id'     => (!$isTrainee) ? $user->id : null,
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

    /**
     * تخديم الصورة بشكل آمن عبر الـ API بعد التحقق من الهوية والصلاحيات
     * Securely serve a trainee progress photo via API ensuring authorization and path safety.
     * Endpoint: GET /api/v1/trainee/photos/{photoId}
     */
    public function serve(Request $request, $photoId)
    {
        $user = Auth::guard('sanctum')->user() ?? Auth::user();
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        if (!is_numeric($photoId)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid photo identifier.'
            ], 404);
        }

        $photo = TraineeProgressPhoto::with('checkin.trainee')->find((int) $photoId);
        if (!$photo) {
            return response()->json([
                'status'  => false,
                'message' => 'Photo not found.'
            ], 404);
        }

        $isTrainee = in_array((int) $user->user_type, [2, 3], true);
        if ($isTrainee) {
            $trainee = Trainee::where('user_id', $user->id)->first();
            if (!$trainee || !$photo->checkin || (int) $photo->checkin->trainee_id !== (int) $trainee->id) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthorized access to photo.'
                ], 403);
            }
        }

        $photoPath = $photo->photo_path;
        if (empty($photoPath) || !is_string($photoPath) || str_contains($photoPath, '..') || str_contains($photoPath, "\0") || str_contains($photoPath, '://')) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid file path.'
            ], 400);
        }

        $disk = Storage::disk('public');
        if (!$disk->exists($photoPath)) {
            return response()->json([
                'status'  => false,
                'message' => 'File not found on disk.'
            ], 404);
        }

        $fullPath = $disk->path($photoPath);
        $realDiskRoot = realpath($disk->path(''));
        $realFilePath = realpath($fullPath);

        if ($realFilePath === false || !str_starts_with($realFilePath, $realDiskRoot)) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthorized path traversal access.'
            ], 403);
        }

        $mime = mime_content_type($realFilePath);
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($mime, $allowedMimes, true)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid image format.'
            ], 403);
        }

        $response = response()->file($realFilePath, [
            'Content-Type'           => $mime,
            'Pragma'                 => 'no-cache',
            'Expires'                => '0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
        $response->setPrivate();
        $response->headers->set('Cache-Control', 'private, no-cache, no-store, must-revalidate');

        return $response;
    }
}
