<?php

namespace App\Modules\System;

use App\Http\Requests\TraineeProgressRequest;
use App\Models\Trainee;
use App\Models\TraineeProgressPhoto;
use App\Services\TraineeProgressService;
use App\Services\TraineeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class TraineeProgressController extends SystemController
{
    protected $progressService,$traineeService;

    public function __construct(TraineeProgressService $progressService, TraineeService $traineeService)
    {
        parent::__construct();
        $this->progressService = $progressService;
        $this->traineeService = $traineeService;
    }

    /**
     * Display Trainee Progress Check-ins & Gallery.
     */
    public function index(Request $request)
    {
        $authUser = Auth::guard('user')->user();
        $isTrainee = in_array((int) $authUser->user_type, [2, 3], true);
        $trainee = null;

        if ($isTrainee) {
            $ownTrainee = Trainee::where('user_id', $authUser->id)->first();
            if (!$ownTrainee) {
                flash_msg('error', __('Trainee profile not found.'));
                return redirect()->back();
            }

            // Reject IDOR attempts by trainee to access another trainee's record
            if ($request->filled('trainee')) {
                try {
                    $decryptedId = Crypt::decrypt($request->trainee);
                    $targetTrainee = Trainee::find($decryptedId) ?? Trainee::where('user_id', $decryptedId)->first();
                    if (!$targetTrainee || (int) $targetTrainee->user_id !== (int) $authUser->id) {
                        abort(403, __('Unauthorized access to trainee progress.'));
                    }
                } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
                    throw $e;
                } catch (\Exception $e) {
                    abort(403, __('Invalid trainee identifier.'));
                }
            } elseif ($request->filled('trainee_id')) {
                if ((int) $request->trainee_id !== (int) $ownTrainee->id) {
                    abort(403, __('Unauthorized access to trainee progress.'));
                }
            }

            $trainee = $ownTrainee;
        } else {
            // Coach / Admin workflow
            if ($request->filled('trainee')) {
                try {
                    $decryptedId = Crypt::decrypt($request->trainee);
                    $trainee = Trainee::find($decryptedId) ?? Trainee::where('user_id', $decryptedId)->first();
                } catch (\Exception $e) {
                    $trainee = null;
                }
            } elseif ($request->filled('trainee_id')) {
                $trainee = Trainee::find($request->trainee_id);
            }

            if (!$trainee) {
                $trainee = Trainee::first();
            }
        }

        if (!$trainee) {
            flash_msg('error', __('Trainee profile not found.'));
            return redirect()->back();
        }

        $checkins = $this->progressService->getCheckinsForTrainee($trainee->id);

        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Progress & Photos'),
            ]
        ];
        $this->viewData['pageTitle'] = __('Progress & Photos Gallery');
        $this->viewData['authUser']  = $authUser;
        $this->viewData['trainee']   = $trainee;
        $this->viewData['checkins']  = $checkins;

        return $this->view('trainee-progress.index', $this->viewData);
    }

    /**
     * Store a new progress check-in with photos.
     */
    public function store(TraineeProgressRequest $request)
    {
        try {
            $authUser = Auth::guard('user')->user();
            $photoFiles = $request->file('photos', []);

            $checkin = $this->progressService->storeCheckin($request->validated(), $authUser, $photoFiles);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->success(__('Progress check-in saved successfully!'), [
                    'checkin' => $checkin
                ]);
            }

            flash_msg('success', __('Progress check-in and photos uploaded successfully!'));
            return redirect()->back();
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return $this->fail($e->getMessage());
            }

            flash_msg('error', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Update coach notes / feedback.
     */
    public function updateCoachNotes(Request $request, $id)
    {
        $request->validate([
            'coach_notes' => 'nullable|string|max:2000',
        ]);

        $authUser = Auth::guard('user')->user();
        if (in_array((int) $authUser->user_type, [2, 3], true)) {
            abort(403, __('Unauthorized to update coach feedback.'));
        }

        try {
            $checkin = $this->progressService->updateCoachNotes((int) $id, $request->input('coach_notes'), $authUser);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->success(__('Coach notes updated successfully!'), [
                    'checkin' => $checkin
                ]);
            }

            flash_msg('success', __('Coach notes updated successfully!'));
            return redirect()->back();
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return $this->fail($e->getMessage());
            }

            flash_msg('error', $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Delete a check-in.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $authUser = Auth::guard('user')->user();
            $this->progressService->deleteCheckin((int) $id, $authUser);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->success(__('Check-in session deleted successfully!'));
            }

            flash_msg('success', __('Check-in session deleted successfully!'));
            return redirect()->back();
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return $this->fail($e->getMessage());
            }

            flash_msg('error', $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Delete a single photo.
     */
    public function destroyPhoto(Request $request, $photoId)
    {
        try {
            $authUser = Auth::guard('user')->user();
            $this->progressService->deletePhoto((int) $photoId, $authUser);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->success(__('Photo deleted successfully!'));
            }

            flash_msg('success', __('Photo deleted successfully!'));
            return redirect()->back();
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return $this->fail($e->getMessage());
            }

            flash_msg('error', $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * AJAX endpoint for Side-by-Side Comparison.
     */
    public function compare(Request $request)
    {
        $authUser = Auth::guard('user')->user();
        $isTrainee = in_array((int) $authUser->user_type, [2, 3], true);
        $traineeId = $request->input('trainee_id');
        $date1 = $request->input('date1');
        $date2 = $request->input('date2');

        if ($isTrainee) {
            $ownTrainee = Trainee::where('user_id', $authUser->id)->first();
            if (!$ownTrainee) {
                abort(403, __('Unauthorized access to trainee comparison.'));
            }

            if (!empty($traineeId) && (int) $traineeId !== (int) $ownTrainee->id) {
                abort(403, __('Unauthorized access to trainee comparison.'));
            }

            $traineeId = $ownTrainee->id;
        } else {
            if (!$traineeId) {
                return $this->fail(__('Trainee ID is required for comparison.'));
            }
        }

        $comparison = $this->progressService->getComparisonData((int) $traineeId, $date1, $date2);

        return $this->success(__('Comparison data retrieved successfully'), $comparison);
    }

    /**
     * عرض صورة تطور المتدرب بشكل آمن ومحمي بالصلاحيات
     * Securely stream a private progress photo after verifying authentication, trainee ownership, and preventing path traversal.
     */
    public function showPhoto(Request $request, $photoId)
    {
        $authUser = Auth::guard('user')->user();
        if (!$authUser) {
            abort(401, __('Unauthenticated.'));
        }

        if (!is_numeric($photoId)) {
            abort(404, __('Invalid photo identifier.'));
        }

        $photo = TraineeProgressPhoto::with('checkin.trainee')->find((int) $photoId);
        if (!$photo) {
            abort(404, __('Photo not found.'));
        }

        // التحقق من ملكية المتدرب للصورة وعدم السماح لمتدرب بالوصول لصور متدرب آخر
        $isTrainee = in_array((int) $authUser->user_type, [2, 3], true);
        if ($isTrainee) {
            $ownTrainee = Trainee::where('user_id', $authUser->id)->first();
            if (!$ownTrainee || !$photo->checkin || (int) $photo->checkin->trainee_id !== (int) $ownTrainee->id) {
                abort(403, __('Unauthorized access to trainee progress photo.'));
            }
        }

        // الحماية من مسارات غير آمنة أو محاولات Path Traversal
        $photoPath = $photo->photo_path;
        if (empty($photoPath) || !is_string($photoPath)) {
            abort(404, __('Invalid photo path in record.'));
        }

        if (str_contains($photoPath, '..') || str_contains($photoPath, "\0") || str_contains($photoPath, '://')) {
            abort(400, __('Invalid file path detected.'));
        }

        $disk = Storage::disk('public');
        if (!$disk->exists($photoPath)) {
            abort(404, __('Photo file not found on disk.'));
        }

        $fullPath = $disk->path($photoPath);
        $realDiskRoot = realpath($disk->path(''));
        $realFilePath = realpath($fullPath);

        if ($realFilePath === false || !str_starts_with($realFilePath, $realDiskRoot)) {
            abort(403, __('Unauthorized path traversal access.'));
        }

        $mime = mime_content_type($realFilePath);
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($mime, $allowedMimes, true)) {
            abort(403, __('Invalid image format.'));
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
