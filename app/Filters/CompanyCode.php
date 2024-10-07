<?php

namespace App\Filters;


class CompanyCode extends Filter
{

    public function applyFilter($builder)
    {
        return $builder->where($builder->getModel()->getTable().'.company_code', 'LIKE', '%' . request($this->filterName()) . '%');
    }
}
