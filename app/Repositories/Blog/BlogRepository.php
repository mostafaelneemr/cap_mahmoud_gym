<?php

namespace App\Repositories\Blog;


use App\Models\Post;
use App\Repositories\BaseRepository;

class BlogRepository extends BaseRepository
{
    protected $modeler = Post::class;

    public function getDataTableQuery()
    {
        return $this->modeler->select('*');
    }
}
