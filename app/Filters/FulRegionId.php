<?php

namespace App\Filters;

use Closure;

class FulRegionId extends Filter
{
    public function applyFilter($builder)
    {
        return $builder->whereHas('order',function($order){
            $order->whereHas('city',function($city){
                $city->where('region_id', request($this->filterName()));
            });
        });
    }
}
