<?php

use App\Modules\System\SettingController;

Route::get('/logout', 'Auth\LoginController@logout')->name('logout'); //
Route::post('/reset-password','Auth\LoginController@updatePassword')->name('system.reset-password');

Auth::routes();

Route::get('/user/change-password', 'UserController@changePassword')->name('system.user.change-password');

Route::post('/user/change-password', 'UserController@changePasswordPost')->name('system.user.change-password-post');

Route::get('/user/profile-update', 'UserController@editProfile')->name('system.user.profile');
Route::get('/user/show-profile', 'UserController@showProfile')->name('system.user.show-profile');

Route::patch('/user/update-profile', 'UserController@updateProfile')->name('system.user.update-profile');


Route::resource('/user', 'UserController', ['as' => 'system']); //

Route::get('/user/get-activity-log/{id}', 'UserController@getUserActivityLog')->name('system.get-user-activity-log'); //
Route::get('/user/get-auth-session/{id}', 'UserController@getAuthSession')->name('system.get-auth-session'); //

Route::get('/ajax', 'AjaxController@index')->name('system.misc.ajax'); //

Route::resource('/permission-group', 'PermissionGroupsController', ['as' => 'system']); //

Route::get('/', 'Dashboard@index')->name('system.dashboard');
Route::get('/user-sessions', 'AuthSessionController@authSessionForUser')->name('system.user.user-sessions');
Route::resource('/auth-sessions', 'AuthSessionController', ['as' => 'system']); //
// Activity LOG
Route::get( '/activity-log/{ID}', 'ActivityController@show' )->name( 'system.activity-log.show' ); //
Route::get( '/activity-log', 'ActivityController@index' )->name( 'system.activity-log.index' ); //


Route::resource('/language', 'LanguageController', ['as' => 'system']);


Route::resource('/slider', 'SliderController', ['as' => 'system']); //
Route::resource('/choose-item', 'ChooseItemController', ['as' => 'system']); //
Route::resource('/testimonial', 'TestimonialController', ['as' => 'system']); //
Route::resource('/blog', 'BlogController', ['as' => 'system']); //
Route::resource('/message', 'MessageController', ['as' => 'system']); //

Route::get('/setting', 'SettingController@index')->name('system.setting.index'); //
Route::patch('/setting', 'SettingController@update')->name('system.setting.update'); //


