<?php

namespace App\Filters;

use Closure;
use Illuminate\Support\Facades\DB;

class UserFullName extends Filter
{

    public function applyFilter($builder)
    {
        return $builder->where(function ($q){
            $q->where('user.firstname', 'LIKE', '%' . request($this->filterName()) . '%')
                ->orWhere('user.lastname', 'LIKE', '%' . request($this->filterName()) . '%')
                ->orWhere(DB::raw("CONCAT(user.firstname,' ',user.lastname)"), 'LIKE', '%' . request($this->filterName()) . '%');
        });
    }
}
