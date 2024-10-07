<?php

namespace App\Filters;

use Illuminate\Support\Str;

abstract class Filter
{
    public function handle($request, \Closure $next)
    {
        if (!request()->has($this->filterName()) || empty(request($this->filterName()))) {
            return $next($request);
        }

        $builder = $next($request);
        return $this->applyFilter($builder);
    }

    public abstract function applyFilter($builder);

    protected function filterName()
    {
        return Str::snake(class_basename($this));
    }
}
