<?php

namespace App\Filters;


class AddressRegionId extends Filter
{
    public function applyFilter($builder)
    {
        return $builder->whereHas('region', function ($q) {
            $q->where('address_city.region_id',  request($this->filterName()) );
        });
    }
}
