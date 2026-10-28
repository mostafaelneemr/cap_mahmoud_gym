<?php

namespace App\Repositories\ExerciseLibrary;

use App\Models\ExerciseLibrary;
use App\Repositories\BaseRepository;

class ExerciseLibraryRepository extends BaseRepository
{
    protected $modeler = ExerciseLibrary::class;

    /**
     * Query for the central library management DataTable.
     */
    public function getDataTableQuery()
    {
        return $this->modeler->newQuery()
            ->withCount('exercises')
            ->select(['id', 'name', 'video_url', 'description', 'status', 'created_at', 'updated_at']);
    }

    /**
     * Search active library entries for selection in workout plan creation and day editing.
     */
    public function searchActive(?string $term = null, int $limit = 25)
    {
        $query = $this->modeler->active()->orderBy('name', 'asc');

        if (!empty($term)) {
            $query->where('name', 'like', '%' . trim($term) . '%');
        }

        return $query->limit($limit)->get(['id', 'name', 'video_url', 'description']);
    }

    /**
     * Get all active library exercises for dropdowns.
     */
    public function getActiveList()
    {
        return $this->modeler->active()
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'video_url']);
    }
}
