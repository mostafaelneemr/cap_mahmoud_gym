<?php

namespace App\Filters;


class AddressStatus extends Filter
{
    public function applyFilter($builder)
    {
        return $builder->where($builder->getModel()->getTable().'.status',request($this->filterName()));
    }

}
