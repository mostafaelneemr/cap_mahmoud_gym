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

        if(Auth::check()) {

            if(in_array($request->lang,['ar','en-gb'])){

                if($request->lang != Auth::user()->language_key){
                    Auth::user()->update(['default_language'=>$request->lang]);
                }

                App::setLocale($request->lang);
                if($request->backByLanguage){
                    return redirect()->back();
                }

            } else {
                App::setLocale(Auth::user()->default_language);
             }

        } else {
            App::setLocale('en-gb');
            if($request->backByLanguage){
                return redirect()->back();
            }
         }
        return $next($request);
    }
}
