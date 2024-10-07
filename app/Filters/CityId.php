<?php

namespace App\Filters;

use App\Models\City;
use Closure;

class CityId extends Filter
{
    public function applyFilter($builder)
    {
        $cities_name = array();
        $cities= City::whereIn('id',request($this->filterName()))->get();
        foreach ($cities as $city) {
            array_push($cities_name,$city->name_en);
            array_push($cities_name,$city->name_ar);
        }
        return $builder->where(function($query)use($cities_name){
            $query->whereIn('order.city_id', request($this->filterName()))
            ->orwhereIn('shipping_city',$cities_name);
        });
    }
}
