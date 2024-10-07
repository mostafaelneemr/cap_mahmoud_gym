<?php

namespace App\Filters;


class AddressCityId extends Filter
{
    public function applyFilter($builder)
    {
        return $builder->whereHas('city', function ($q) {
            $q->where('address_zone.city_id',  request($this->filterName()) );
        });
    }
}
