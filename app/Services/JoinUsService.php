<?php

namespace App\Services;

use App\Repositories\JoinUs\JoinUsRepository;
use Illuminate\Support\Facades\DB;
use Datatables;

class JoinUsService extends BaseService
{
    protected $joinUsRepository;

    public function __construct(JoinUsRepository $joinUsRepository)
    {
        parent::__construct();
        $this->joinUsRepository = $joinUsRepository;
    }

    public function loadViewData(): array
    {
        $this->pageTitle('Join Us Submissions');
        $this->breadcrumb('Join Us Submissions');
        $this->tableColumns([
            __('ID'),
            __('Name'),
            __('Phone'),
            __('Age'),
            __('Country / Governorate'),
            __('Training Level'),
            __('Status'),
            __('Date'),
            __('Actions'),
        ]);

        $this->jsColumns([
            'id' => 'id',
            'name' => 'name',
            'phone' => 'mobile',
            'age' => 'name',
            'location' => '',
            'training_level' => '',
            'status' => '',
            'created_at' => '',
            'action' => ''
        ]);

        return $this->retunData;
    }

    /**
     * Return paginated, searchable DataTable JSON.
     */
    public function loadDataTableData()
    {
        $query = $this->joinUsRepository->getDataTableQuery();

        return Datatables::eloquent($query)
            ->addColumn('id', '{{$id}}')
            ->addColumn('name', function ($data) {
                return $data->name;
            })
            ->addColumn('phone', '{{$phone}}')
            ->addColumn('status', function ($data) {
                return $data->status;
            })
            ->addColumn('location', function ($data) {
                return $data->country . ' / ' . $data->governorate;
            })
            ->addColumn('created_at', function ($data) {
                if ($data->created_at)
                    return $data->created_at->format('Y-m-d H:i');
                return '--';
            })
            ->editColumn('action', function ($data) {
                $this->actionButtons(datatable_menu_show(route('system.join-us.show', $data->id), 'system.join-us.show'));
                return $this->actionButtonsRender($this->joinUsRepository->modelPath(), $data->id);
            })->escapeColumns([])
            ->make(true);
    }

    /**
     * Store a new Join Us submission from the public API.
     */
    public function store(array $data): bool
    {
        try {
            DB::beginTransaction();
            $this->joinUsRepository->store($data);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            errorLog($e->getMessage());
            return false;
        }
    }

    /**
     * Update the status/notes of a submission from the dashboard.
     */
    public function updateStatus(int $id, string $status, ?string $notes = null): bool
    {
        try {
            DB::beginTransaction();
            $update = ['status' => $status];
            if (!is_null($notes)) {
                $update['notes'] = $notes;
            }
            $this->joinUsRepository->update($update, $id);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            errorLog($e->getMessage());
            return false;
        }
    }


    public function show(int $id): array
    {
        $data = $this->joinUsRepository->find($id);

        $this->pageTitle('Join Us Submission');
        $this->breadcrumb('Submissions', 'system.join-us.index');

        $this->otherData(['result' => $data]);
        return $this->retunData;
    }

    /**
     * Summary counts for dashboard widgets.
     */
    public function statusCounts(): array
    {
        return [
            'new' => $this->joinUsRepository->countByStatus('new'),
            'contacted' => $this->joinUsRepository->countByStatus('contacted'),
            'converted' => $this->joinUsRepository->countByStatus('converted'),
            'rejected' => $this->joinUsRepository->countByStatus('rejected'),
        ];
    }
}
