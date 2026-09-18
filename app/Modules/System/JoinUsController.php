<?php

namespace App\Modules\System;

use App\Services\JoinUsService;
use Illuminate\Http\Request;

class JoinUsController extends SystemController
{
    protected $joinUsService;

    public function __construct(JoinUsService $joinUsService)
    {
        parent::__construct();
        $this->joinUsService = $joinUsService;
    }

    /**
     * List all Join Us submissions.
     * Handles both normal page render and DataTable AJAX requests.
     */
    public function index(Request $request)
    {
        if ($request->isDataTable) {
            return $this->joinUsService->datatable();
        }

        $data = $this->joinUsService->loadViewData();
        $data['counts'] = $this->joinUsService->statusCounts();

        return $this->view('join-us.index', $data);
    }

    /**
     * Show a single submission detail.
     */
    public function show(int $id)
    {
        $submission = \App\Models\JoinUsSubmission::findOrFail($id);

        return $this->view('join-us.show', [
            'pageTitle'  => __('Join Us Submission #') . $id,
            'submission' => $submission,
            'breadcrumb' => [
                ['text' => __('Join Us Submissions'), 'url' => route('system.join-us.index')],
                ['text' => __('Submission #') . $id],
            ],
        ]);
    }

    /**
     * Update the status (and optional notes) of a submission from the dashboard.
     */
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:new,contacted,converted,rejected',
            'notes'  => 'nullable|string|max:2000',
        ]);

        $updated = $this->joinUsService->updateStatus($id, $request->status, $request->notes);

        if ($updated) {
            flash_msg('success', __('Submission status updated successfully.'));
            return $this->success(__('Status updated.'), ['url' => route('system.join-us.show', $id)]);
        }

        return $this->fail(__('Sorry, we could not update the status.'));
    }

    /**
     * Delete a submission permanently.
     */
    public function destroy(int $id)
    {
        $submission = \App\Models\JoinUsSubmission::findOrFail($id);
        $submission->delete();

        flash_msg('success', __('Submission deleted successfully.'));
        return $this->success(__('Deleted.'), ['url' => route('system.join-us.index')]);
    }
}
