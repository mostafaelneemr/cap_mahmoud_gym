<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class Permission
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::guard('user')->check()) {
            if (Auth::guard('user')->user()->status == 'in-active' || empty(Auth::guard('user')->user()->permission_group_id)) {
                Auth::logout();
                return redirect('/system/login');
            }

            $canAccess = array_merge(ignoredRoutes(), User::UserPerms(Auth::guard('user')->user()->id)->toArray());
            
            if (!in_array(Route::currentRouteName(), $canAccess)) {
                abort(401, 'Unauthorized.');
            }
        }
        return $next($request);
    }
}
