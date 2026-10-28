<?php

namespace App\Modules\System;

use App\Http\Requests\ExerciseLibraryFormRequest;
use App\Services\ExerciseLibraryService;
use Illuminate\Http\Request;

class ExerciseLibraryController extends SystemController
{
    protected ExerciseLibraryService $exerciseLibraryService;

    public function __construct(ExerciseLibraryService $exerciseLibraryService)
    {
        parent::__construct();
        $this->exerciseLibraryService = $exerciseLibraryService;
    }

    public function index(Request $request)
    {
        if ($request->isDataTable) {
            return $this->exerciseLibraryService->loadDataTableData();
        }

        return $this->view('exercise-library.index', $this->exerciseLibraryService->loadViewData());
    }

    public function create()
    {
        return $this->view('exercise-library.create', $this->exerciseLibraryService->create());
    }

    public function store(ExerciseLibraryFormRequest $request)
    {
        $created = $this->exerciseLibraryService->store($request);

        if ($created) {
            flash_msg('success', __('Library exercise created successfully'));
            return $this->success(
                __('Library exercise created successfully'),
                ['url' => route('system.exercise-library.index')]
            );
        }

        return $this->fail(__('Could not save exercise to library'));
    }

    public function edit($id)
    {
        return $this->view('exercise-library.create', $this->exerciseLibraryService->edit($id));
    }

    public function update(ExerciseLibraryFormRequest $request, $id)
    {
        $updated = $this->exerciseLibraryService->update($request, $id);

        if ($updated) {
            flash_msg('success', __('Library exercise updated successfully'));
            return $this->success(
                __('Library exercise updated successfully'),
                ['url' => route('system.exercise-library.index')]
            );
        }

        return $this->fail(__('Could not update library exercise'));
    }

    public function destroy($id, Request $request)
    {
        $deleted = $this->exerciseLibraryService->destroy($id);

        if ($deleted) {
            if ($request->ajax() || $request->wantsJson()) {
                return $this->success(__('Library exercise deleted successfully'), [
                    'url' => route('system.exercise-library.index')
                ]);
            }
            flash_msg('success', __('Library exercise deleted successfully'));
            return redirect()->route('system.exercise-library.index');
        }

        return $this->fail(__('Could not delete library exercise'));
    }

    public function toggleStatus($id, Request $request)
    {
        $res = $this->exerciseLibraryService->toggleStatus($id);

        if ($res['success']) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'success' => true,
                    'message' => $res['message'],
                    'data' => $res,
                ]);
            }
            flash_msg('success', $res['message']);
            return redirect()->back();
        }

        return $this->fail($res['message'] ?? __('Failed to toggle status'));
    }

    public function search(Request $request)
    {
        return response()->json($this->exerciseLibraryService->search($request));
    }
}
