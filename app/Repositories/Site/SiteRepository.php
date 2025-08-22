<?php

namespace App\Repositories\Site;

use App\Models\Site;
use App\Repositories\BaseRepository;

class SiteRepository extends BaseRepository
{
    protected $modeler = Site::class;

    public function getDataTableQuery()
    {
        return $this->modeler->select(['id','title','icon','link_url','status']);
    }
}
