<?php

namespace App\Providers;

use App\Auth\DBSessionAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Opcodes\LogViewer\Facades\LogViewer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if($this->app->environment('production')) {
            \URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', true);
        }

        set_time_limit(0);

        // DBSessionAuth Auth Provider
        Auth::extend('DBSessionAuth', function($app,$name, array $config) {
            $providerData = config('auth.providers.'.$config['provider']);
            return new DBSessionAuth($providerData['model'],$name);
        });

//        LogViewer::auth(function ($request) {
//           return auth('staff')->check() && staffCan(Route::currentRouteName());
//        });
    }
}
