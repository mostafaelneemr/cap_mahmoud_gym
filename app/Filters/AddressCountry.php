<?php

namespace App\Filters;


class AddressCountry extends Filter
{
    public function applyFilter($builder)
    {
        return $builder->whereHas('country', function ($q) {
            $q->where('address_region.county_id',  request($this->filterName()) );
        });
    }
}
