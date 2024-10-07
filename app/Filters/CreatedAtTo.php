<?php

namespace App\Filters;

use Closure;

class CreatedAtTo extends Filter
{

    public function applyFilter($builder)
    {
        return $builder->whereDate($builder->from.'.created_at','<=', request($this->filterName()));
    }
}
