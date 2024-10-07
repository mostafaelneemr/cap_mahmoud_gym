<?php

namespace App\Repositories\Testimonial;

use App\Models\Testimonial;
use App\Repositories\BaseRepository;

class TestimonialRepository extends BaseRepository
{
    protected $modeler = Testimonial::class;

    public function getDataTableQuery()
    {
        return $this->modeler->select(['id','image','name_en','name_ar','title_en','title_ar','text_en','text_ar','status']);
    }
}
