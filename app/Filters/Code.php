<?php

namespace App\Filters;


class Code extends Filter
{

    public function applyFilter($builder)
    {
        return $builder->where($builder->getModel()->getTable().'.code', 'LIKE', '%' . request($this->filterName()) . '%');
    }
}
