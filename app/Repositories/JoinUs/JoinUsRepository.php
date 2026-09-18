<?php

namespace App\Repositories\JoinUs;

use App\Models\JoinUsSubmission;
use App\Repositories\BaseRepository;

class JoinUsRepository extends BaseRepository
{
    protected $modeler = JoinUsSubmission::class;

    /**
     * Return base query for DataTables, ordered newest first.
     */
    public function getDataTableQuery()
    {
        return $this->modeler->select('*')->latest();
    }

    /**
     * Count submissions by status.
     */
    public function countByStatus(string $status): int
    {
        return $this->modeler->where('status', $status)->count();
    }
}
