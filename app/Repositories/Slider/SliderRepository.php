<?php

namespace App\Repositories\Slider;

use App\Models\Slider;
use App\Repositories\BaseRepository;

class SliderRepository extends BaseRepository
{
    protected $modeler = Slider::class;

    public function getDataTableQuery()
    {
        return $this->modeler->select([
            'id','image','title_en','title_ar','sub_title_en','sub_title_ar','button_en','button_ar','type','status'
        ]);
    }
}
