<?php


namespace App\Repositories\Language;

use App\Models\Language;
use App\Repositories\BaseRepository;

class LanguageRepository extends BaseRepository
{
    protected $modeler = Language::class;

    public function getDataTableQuery()
    {
        return $this->modeler->select(['id','name','code','status','sort_order','image']);
    }

}
