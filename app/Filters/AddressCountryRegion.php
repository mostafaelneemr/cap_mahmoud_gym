<?php

namespace App\Filters;


class AddressCountryRegion extends Filter
{
    public function applyFilter($builder)
    {
        return $builder->whereHas('region', function ($query) {
            $query->whereHas('country', function ($query) {
                $query->where('id', request($this->filterName()));
            });
        });
    }
}
