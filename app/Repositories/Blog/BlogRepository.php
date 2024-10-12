<?php

namespace App\Repositories\Blog;

use App\Models\Blog;
use App\Repositories\BaseRepository;

class BlogRepository extends BaseRepository
{
    protected $modeler = Blog::class;

    public function getDataTableQuery()
    {
        return $this->modeler->select('*');
    }
}
