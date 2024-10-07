<?php

namespace App\Filters;


class NameWithLang extends Filter
{
    public function applyFilter($builder)
    {
        return $builder->where($builder->getModel()->getTable().'.name_ar', 'LIKE', '%' . request($this->filterName()) . '%')
            ->orwhere($builder->getModel()->getTable().'.name_en', 'LIKE', '%' . request($this->filterName()) . '%');

    }
}
