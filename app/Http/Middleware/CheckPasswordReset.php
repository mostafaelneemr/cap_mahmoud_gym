<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPasswordReset
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (Auth::check() && Auth::user()->force_reset_password ) {
            session(['force_password_reset' => true]);
        } else {
            session()->forget('force_password_reset');
        }

        if (Auth::check() && Auth::user()->force_reset_password) {
            if (request()->route()->getName() != 'system.dashboard' && request()->route()->getName() != 'system.reset-password') {
                return redirect()->route('system.dashboard');
            }
        }

        return $next($request);
    }
}
