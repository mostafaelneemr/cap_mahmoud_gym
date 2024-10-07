<?php

namespace App\Repositories\ChooseItem;

use App\Models\ChooseItem;
use App\Repositories\BaseRepository;

class ChooseItemRepository extends BaseRepository
{
    protected $modeler = ChooseItem::class;

    public function getDataTableQuery()
    {
        return $this->modeler->select(['id','image','title_en','title_ar','text_en','text_ar','status']);
    }
}
