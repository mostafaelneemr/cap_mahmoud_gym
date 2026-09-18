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
        $this->pageTitle(__('Join Us Submissions'));
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
            'id'           => 'join_us_submissions.id',
            'name'         => '',
            'phone'        => '',
            'age'          => '',
            'location'     => '',
            'training_level' => '',
            'status'       => '',
            'created_at'   => '',
            'actions'      => ['orderable' => false, 'searchable' => false],
        ]);

        $this->breadcrumb('Join Us Submissions');

        return $this->retunData;
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

    /**
     * Return paginated, searchable DataTable JSON.
     */
    public function datatable()
    {
        $query = $this->joinUsRepository->getDataTableQuery();

        return Datatables::of($query)
            ->addColumn('location', fn($row) => $row->country . ' / ' . $row->governorate)
            ->addColumn('status', fn($row) => $row->status_label)
            ->addColumn('actions', function ($row) {
                return '
                    <a href="' . route('system.join-us.show', $row->id) . '"
                       class="btn btn-sm btn-light-primary">
                        <i class="fa-solid fa-eye me-1"></i>' . __('View') . '
                    </a>';
            })
            ->rawColumns(['status', 'actions'])
            ->make(true);
    }

    /**
     * Summary counts for dashboard widgets.
     */
    public function statusCounts(): array
    {
        return [
            'new'       => $this->joinUsRepository->countByStatus('new'),
            'contacted' => $this->joinUsRepository->countByStatus('contacted'),
            'converted' => $this->joinUsRepository->countByStatus('converted'),
            'rejected'  => $this->joinUsRepository->countByStatus('rejected'),
        ];
    }
}
