<?php

namespace App\Services;

use App\Repositories\ExerciseLibrary\ExerciseLibraryRepository;
use Datatables;
use Illuminate\Http\Request;

class ExerciseLibraryService extends BaseService
{
    protected ExerciseLibraryRepository $exerciseLibraryRepository;

    public function __construct(ExerciseLibraryRepository $exerciseLibraryRepository)
    {
        parent::__construct();
        $this->exerciseLibraryRepository = $exerciseLibraryRepository;
    }

    public function loadViewData(): array
    {
        $this->pageTitle('Exercise Video Library');
        $this->tableColumns([
            __('ID'),
            __('Exercise Name'),
            __('Video Preview'),
            __('Linked Exercises'),
            __('Status'),
            __('Created At'),
            __('Action')
        ]);

        $this->jsColumns([
            'id' => 'id',
            'name' => 'name',
            'video_preview' => 'video_url',
            'exercises_count' => 'exercises_count',
            'status' => 'status',
            'created_at' => 'created_at',
            'action' => 'action'
        ]);

        $this->breadcrumb('Workout Plans', 'system.workout.index');
        $this->breadcrumb('Exercise Library');

        $this->addButton('system.exercise-library.create');
        $this->filterIgnoreColumns(['action', 'video_preview']);

        return $this->retunData;
    }

    public function loadDataTableData()
    {
        $query = $this->exerciseLibraryRepository->getDataTableQuery();

        return Datatables::eloquent($query)
            ->addColumn('id', '{{$id}}')
            ->editColumn('name', function ($data) {
                $html = '<div class="d-flex flex-column">';
                $html .= '<span class="text-gray-800 fw-bold fs-6">' . e($data->name) . '</span>';
                if (!empty($data->description)) {
                    $html .= '<span class="text-muted fs-7 text-truncate" style="max-width: 250px;">' . e($data->description) . '</span>';
                }
                $html .= '</div>';
                return $html;
            })
            ->addColumn('video_preview', function ($data) {
                if (empty($data->video_url)) {
                    return '<span class="text-muted fs-7">' . __('No video') . '</span>';
                }

                $formatted = $data->formatted_video_url;
                return '
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-icon btn-light-primary btn-preview-video"
                                data-url="' . e($formatted) . '"
                                data-name="' . e($data->name) . '"
                                title="' . __('Preview Video') . '">
                            <i class="fas fa-play fs-7"></i>
                        </button>
                        <a href="' . e($data->video_url) . '" target="_blank" rel="noopener noreferrer"
                           class="text-primary fs-7 text-truncate" style="max-width: 180px;" title="' . e($data->video_url) . '">
                           <i class="fas fa-external-link-alt fs-8 me-1"></i>' . __('Open Link') . '
                        </a>
                    </div>
                ';
            })
            ->editColumn('exercises_count', function ($data) {
                $count = (int) $data->exercises_count;
                $badgeClass = $count > 0 ? 'badge-light-primary' : 'badge-light-secondary';
                return '<span class="badge ' . $badgeClass . ' fw-bold">' . $count . ' ' . __('assigned') . '</span>';
            })
            ->editColumn('status', function ($data) {
                $isActive = ($data->status === 'active');
                $badge = $isActive
                    ? '<span class="badge badge-light-success fw-bold">' . __('Active') . '</span>'
                    : '<span class="badge badge-light-danger fw-bold">' . __('Inactive') . '</span>';

                if (userCan('system.exercise-library.toggle-status')) {
                    $btnClass = $isActive ? 'btn-light-warning' : 'btn-light-success';
                    $btnTitle = $isActive ? __('Deactivate') : __('Activate');
                    $btnIcon = $isActive ? 'fa-pause' : 'fa-play';
                    return '
                        <div class="d-flex align-items-center gap-2">
                            ' . $badge . '
                            <button type="button" class="btn btn-sm btn-icon ' . $btnClass . ' btn-toggle-status"
                                    data-id="' . $data->id . '"
                                    data-url="' . route('system.exercise-library.toggle-status', $data->id) . '"
                                    data-status="' . $data->status . '"
                                    title="' . $btnTitle . '">
                                <i class="fas ' . $btnIcon . ' fs-8"></i>
                            </button>
                        </div>
                    ';
                }

                return $badge;
            })
            ->editColumn('created_at', function ($data) {
                return $data->created_at ? $data->created_at->format('Y-m-d H:i') : '';
            })
            ->editColumn('action', function ($data) {
                $this->actionButtons(datatable_menu_edit(route('system.exercise-library.edit', $data->id), 'system.exercise-library.edit'));
                $this->actionButtons(datatable_menu_delete(route('system.exercise-library.destroy', $data->id), 'system.exercise-library.destroy', $data->id));
                return $this->actionButtonsRender($this->exerciseLibraryRepository->modelPath(), $data->id);
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function create(): array
    {
        $this->pageTitle('Add Library Exercise');
        $this->breadcrumb('Workout Plans', 'system.workout.index');
        $this->breadcrumb('Exercise Library', 'system.exercise-library.index');
        $this->breadcrumb('Create');

        return $this->retunData;
    }

    public function store($request)
    {
        $data = $request->only(['name', 'video_url', 'description', 'status']);
        return $this->exerciseLibraryRepository->store($data);
    }

    public function edit($id): array
    {
        $record = $this->exerciseLibraryRepository->find($id);
        $this->pageTitle('Edit Library Exercise');
        $this->breadcrumb('Workout Plans', 'system.workout.index');
        $this->breadcrumb('Exercise Library', 'system.exercise-library.index');
        $this->breadcrumb('Edit');

        $this->retunData['record'] = $record;
        return $this->retunData;
    }

    public function update($request, $id)
    {
        $data = $request->only(['name', 'video_url', 'description', 'status']);
        return $this->exerciseLibraryRepository->update($data, $id);
    }

    public function destroy($id): bool
    {
        $record = $this->exerciseLibraryRepository->find($id);
        if ($record) {
            return (bool) $record->delete();
        }
        return false;
    }

    public function toggleStatus($id): array
    {
        $record = $this->exerciseLibraryRepository->find($id);
        if (!$record) {
            return ['success' => false, 'message' => __('Record not found')];
        }

        $newStatus = ($record->status === 'active') ? 'inactive' : 'active';
        $record->status = $newStatus;
        $record->save();

        return [
            'success' => true,
            'id' => $record->id,
            'status' => $newStatus,
            'message' => ($newStatus === 'active')
                ? __('Exercise ":name" activated successfully.', ['name' => $record->name])
                : __('Exercise ":name" deactivated. It cannot be chosen for new assignments.', ['name' => $record->name]),
        ];
    }

    public function search(Request $request): array
    {
        $term = $request->input('q') ?? $request->input('term');
        $limit = (int) ($request->input('limit') ?: 30);

        $results = $this->exerciseLibraryRepository->searchActive($term, $limit);

        $formatted = [];
        foreach ($results as $item) {
            $formatted[] = [
                'id' => $item->id,
                'name' => $item->name,
                'video_url' => $item->video_url,
                'formatted_video_url' => $item->formatted_video_url,
                'description' => $item->description,
            ];
        }

        return [
            'status' => true,
            'results' => $formatted,
        ];
    }
}
