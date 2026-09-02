<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Api\WebsiteController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Single section home page data endpoint: GET /api/v1/home/{type}
Route::prefix('v1')->group(function () {

    // Global Data (Socials, Contact numbers, Logo)
    Route::get('global', [WebsiteController::class, 'getGlobalData']);

    // Page Specific GET Endpoints (by section {type})
    Route::get('home/{type}', [WebsiteController::class, 'getHomePageData']);
    Route::get('transformations/{type}', [WebsiteController::class, 'getTransformationPageData']);
    Route::get('join-us/{type}', [WebsiteController::class, 'getJoinUsPageData']);
    Route::get('contact/{type}', [WebsiteController::class, 'getContactPageData']);

    // Form Submissions POST Endpoints
    Route::post('reviews', [WebsiteController::class, 'submitReview']);
    Route::post('join-us', [WebsiteController::class, 'submitJoinUs']);
    Route::post('contact', [WebsiteController::class, 'submitContact']);

});
