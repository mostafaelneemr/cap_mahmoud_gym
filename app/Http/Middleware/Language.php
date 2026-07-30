<?php

namespace App\Http\Middleware;

use App\Modules\Api\User\MerchantsApiController;
use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class Language
{
    public function handle($request, Closure $next)
    {
        // Trainees have no language preference columns — skip language switching
        if (Auth::guard('trainee')->check()) {
            App::setLocale('ar');
            return $next($request);
        }

        if (Auth::guard('user')->check()) {
            $user = Auth::guard('user')->user();

            if (in_array($request->lang, ['ar', 'en-gb'])) {
                if ($request->lang != $user->default_language) {
                    $user->update(['default_language' => $request->lang]);
                }
                App::setLocale($request->lang);
                if ($request->backByLanguage) {
                    return redirect()->back();
                }
            } else {
                App::setLocale($user->default_language ?? 'en-gb');
            }
        } else {
            App::setLocale('en-gb');
            if ($request->backByLanguage) {
                return redirect()->back();
            }
        }

        return $next($request);
    }
}
