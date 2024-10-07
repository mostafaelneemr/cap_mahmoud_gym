<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/system';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        Route::middleware('system')
            ->namespace('App\Modules\System')
            ->prefix('system')
            ->group(base_path('routes/system.php'));

        Route::middleware('web')
            ->namespace('App\Modules\Web')
            ->group(base_path('routes/web.php'));

        Route::middleware('api')
            ->namespace('App\Modules\Api')
            ->prefix('api')
            ->group(base_path('routes/api.php'));
    }

}
