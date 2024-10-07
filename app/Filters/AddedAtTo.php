<?php

namespace App\Filters;


class AddedAtTo extends Filter
{

    public function applyFilter($builder)
    {
        return $builder->whereDate($builder->getModel()->getTable().'.added_at','<=', request($this->filterName()));
    }
}
