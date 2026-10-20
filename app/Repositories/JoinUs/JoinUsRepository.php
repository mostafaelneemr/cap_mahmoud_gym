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
        return $this->modeler->select(['id','name', 'phone', 'age', 'training_level', 'status', 'country', 'governorate', 'created_at'])->latest();
    }

    /**
     * Count submissions by status.
     */
    public function countByStatus(string $status): int
    {
        return $this->modeler->where('status', $status)->count();
    }

    /**
     * Get aggregated status counts in a single query.
     */
    public function getStatusCounts(): array
    {
        $counts = $this->modeler
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'new' => (int) ($counts['new'] ?? 0),
            'contacted' => (int) ($counts['contacted'] ?? 0),
            'converted' => (int) ($counts['converted'] ?? 0),
            'rejected' => (int) ($counts['rejected'] ?? 0),
        ];
    }
}
