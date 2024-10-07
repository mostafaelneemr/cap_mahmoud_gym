<?php

namespace App\Filters;


class AddressCountryId extends Filter
{
    public function applyFilter($builder)
    {
        return $builder->where($builder->getModel()->getTable().'.country_id',request($this->filterName()));

    }

}
