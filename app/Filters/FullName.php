<?php

namespace App\Filters;

use Closure;
use Illuminate\Support\Facades\DB;

class FullName extends Filter
{

    public function applyFilter($builder)
    {
        return $builder->where(function ($q){
            $q->where('customer.firstname', 'LIKE', '%' . request($this->filterName()) . '%')
                ->orWhere('customer.lastname', 'LIKE', '%' . request($this->filterName()) . '%')
                ->orWhere(DB::raw("CONCAT(customer.firstname,' ',customer.lastname)"), 'LIKE', '%' . request($this->filterName()) . '%');
        });
    }
}
