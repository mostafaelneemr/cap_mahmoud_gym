<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::name('web.')->group(function () {
    Route::get('/', 'WebController@home')->name('home');
    Route::get('/transformation', 'WebController@transformations')->name('transformations');
    Route::get('/transformations', 'WebController@transformations')->name('transformations.alias');
    Route::get('/join-us', 'WebController@joinUs')->name('join-us');
    Route::get('/contact', 'WebController@contact')->name('contact');

    // Form Submissions
    Route::post('/join-us', 'WebController@submitJoinUs')->name('join-us.store');
    Route::post('/contact', 'WebController@submitContact')->name('contact.store');
    Route::post('/reviews', 'WebController@submitReview')->name('reviews.store');
});
