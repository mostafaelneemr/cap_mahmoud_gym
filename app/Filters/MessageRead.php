<?php

namespace App\Filters;


class MessageRead extends Filter
{
    public function applyFilter($builder)
    {
        if (request($this->filterName()) != 'all') {
            return $builder->where($builder->getModel()->getTable() . '.is_read', request($this->filterName()));
        }
        return $builder;
    }

}
