<?php

namespace App\Filters;

use Closure;
use Illuminate\Support\Str;

class Firstname extends Filter
{
    public function applyFilter($builder)
    {
        return $builder->where($builder->getModel()->getTable().'.'.$this->filterName(),'LIKE', '%'.request($this->filterName()).'%');
    }

}
