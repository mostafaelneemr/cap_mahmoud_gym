<?php


Route::get('/logout', 'Auth\LoginController@logout')->name('logout'); //
Route::post('/reset-password','Auth\LoginController@updatePassword')->name('system.reset-password');

Auth::routes();

Route::controller('UserController')->prefix('user')->group(function () {
    Route::get('/change-password', 'changePassword')->name('system.user.change-password');
    Route::post('/change-password', 'changePasswordPost')->name('system.user.change-password-post');
    Route::get('/profile-update', 'editProfile')->name('system.user.profile');
    Route::get('/show-profile', 'showProfile')->name('system.user.show-profile');
    Route::patch('/update-profile', 'updateProfile')->name('system.user.update-profile');
    Route::get('/get-activity-log/{id}', 'getUserActivityLog')->name('system.get-user-activity-log'); //
    Route::get('/get-auth-session/{id}', 'getAuthSession')->name('system.get-auth-session'); //
});
Route::resource('/user', 'UserController', ['as' => 'system']); //

Route::get('/ajax', 'AjaxController@index')->name('system.misc.ajax'); //

Route::resource('/permission-group', 'PermissionGroupsController', ['as' => 'system']); //

Route::get('/', 'Dashboard@index')->name('system.dashboard');
Route::get('/user-sessions', 'AuthSessionController@authSessionForUser')->name('system.user.user-sessions');
Route::resource('/auth-sessions', 'AuthSessionController', ['as' => 'system']); //
// Activity LOG
Route::controller('ActivityController')->group(function () {
    Route::get('/activity-log/{ID}', 'show')->name('system.activity-log.show'); //
    Route::get('/activity-log', 'index')->name('system.activity-log.index'); //
});

Route::resource('/language', 'LanguageController', ['as' => 'system']);
Route::resource('/slider', 'SliderController', ['as' => 'system']); //
Route::resource('/choose-item', 'ChooseItemController', ['as' => 'system']); //
Route::resource('/testimonial', 'TestimonialController', ['as' => 'system']); //
Route::resource('/blog', 'BlogController', ['as' => 'system']); //
Route::resource('/message', 'MessageController', ['as' => 'system']); //
Route::post('/message/update-status', 'MessageController@updateStatus')->name('system.message.update-status');

Route::controller('SettingController')->group(function () {
    Route::get('/setting', 'index')->name('system.setting.index'); //
    Route::patch('/setting', 'update')->name('system.setting.update'); //
    Route::get('/activate-sections', 'getActivateSection')->name('system.activate.index'); //
    Route::post('/activate-sections/{id}', 'updateActivateSection')->name('system.activate.update'); //
});

Route::resource('/trainee', 'TraineeController', ['as' => 'system']); //




Route::get('/trainer', 'Dashboard@trainerDashboard')->name('system.dashboard.trainer');










