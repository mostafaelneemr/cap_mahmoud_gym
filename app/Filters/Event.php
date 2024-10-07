<?php

namespace App\Filters;

use Closure;

class Event extends Filter
{

    public function applyFilter($builder)
    {
        return $builder->where($this->filterName(),'LIKE', '%'.request($this->filterName()).'%');
    }
}
