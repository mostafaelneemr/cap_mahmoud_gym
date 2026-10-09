<?php

namespace App\Modules\System;

use App\Http\Requests\TraineeProgressRequest;
use App\Models\Trainee;
use App\Services\TraineeProgressService;
use App\Services\TraineeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

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
        $trainee = null;

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
            $trainee = $this->progressService->resolveTrainee($authUser);
            if (!$trainee && ($authUser->user_type == 1 || $authUser->user_type === null)) {
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

        try {
            $authUser = Auth::guard('user')->user();
            $checkin = $this->progressService->updateCoachNotes((int) $id, $request->input('coach_notes'), $authUser);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->success(__('Coach notes updated successfully!'), [
                    'checkin' => $checkin
                ]);
            }

            flash_msg('success', __('Coach notes updated successfully!'));
            return redirect()->back();
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
        $traineeId = $request->input('trainee_id');
        $date1 = $request->input('date1');
        $date2 = $request->input('date2');

        if (!$traineeId) {
            return $this->fail(__('Trainee ID is required for comparison.'));
        }

        $comparison = $this->progressService->getComparisonData((int) $traineeId, $date1, $date2);

        return $this->success(__('Comparison data retrieved successfully'), $comparison);
    }
}
