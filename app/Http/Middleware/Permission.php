<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class Permission
{
    public function handle($request, Closure $next)
    {
        $currentRoute = Route::currentRouteName();

        if (Auth::guard('trainee')->check()) {
            $allowedTraineeRoutes = [
                'system.dashboard.trainer',
                'logout',
                'auth.google',
                'auth.google.callback'
            ];

            if (in_array($currentRoute, $allowedTraineeRoutes)) {
                return $next($request);
            }

            return redirect()->route('system.dashboard.trainer');
        }

        if (Auth::guard('user')->check()) {
            $authUser = Auth::guard('user')->user();

            if ($authUser->status == 0 || $authUser->status == 'in-active' || (empty($authUser->permission_group_id) && $authUser->user_type != 2)) {
                Auth::guard('user')->logout();
                return redirect('/system/login');
            }

            $canAccess = array_merge(ignoredRoutes(), User::UserPerms($authUser->id)->toArray());

            if (!in_array($currentRoute, $canAccess)) {
                abort(401, 'Unauthorized.');
            }

            return $next($request);
        }

        return redirect('/system/login');
    }
}
