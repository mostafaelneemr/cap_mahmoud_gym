<?php

namespace App\Filters;


class Name extends Filter
{

    public function applyFilter($builder)
    {
        return $builder->where($builder->getModel()->getTable().'.name', 'LIKE', '%' . request($this->filterName()) . '%');
    }
}
