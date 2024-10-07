<?php

namespace App\Http\Middleware;

use Closure;

class ArabicNumbers
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
        $request->replace(str_replace(['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'],[0,1,2,3,4,5,6,7,8,9],$request->all()));
        return $next($request);
    }
}
