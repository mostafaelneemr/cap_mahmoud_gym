<?php

namespace App\Filters;

use Closure;

class UserId extends Filter
{
    public function applyFilter($builder)
    {
        return $builder->where($this->filterName(), request($this->filterName()));
    }
}
