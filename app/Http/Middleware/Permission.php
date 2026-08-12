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
        $authUser = Auth::guard('user')->user();
        if (Auth::guard('user')->check()) {
            if ($authUser->status == 'in-active' || (empty($authUser->permission_group_id) && $authUser->user_type != 2 && $authUser->user_type != 3)) {
                Auth::logout();
                return redirect('/system/login');
            }

            if ($authUser->user_type == 2) {
                $whitelist = ['system.dashboard.trainer', 'logout', 'auth.google', 'auth.google.callback'];
                if (in_array(Route::currentRouteName(), $whitelist)) {
                    return $next($request);
                }
                return redirect()->route('system.dashboard.trainer');
            }

            $canAccess = array_merge(ignoredRoutes(), User::UserPerms($authUser->id)->toArray());

            if (!in_array(Route::currentRouteName(), $canAccess)) {
                abort(401, 'Unauthorized.');
            }

        }
        return $next($request);
    }
}
