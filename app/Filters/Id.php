<?php

namespace App\Filters;

use Closure;

class Id extends Filter
{
    public function applyFilter($builder)
    {
        return $builder->where($builder->getModel()->getTable().'.'.$this->filterName(), request($this->filterName()));
    }
}
