<?php


Route::get('/logout', 'Auth\LoginController@logout')->name('logout'); //
Route::post('/reset-password', 'Auth\LoginController@updatePassword')->name('system.reset-password');

// Auth::routes();
Auth::routes(['register' => false, 'reset' => false, 'verify' => false]);

Route::get('/auth/google', 'Auth\LoginController@redirectToGoogle')->name('auth.google');
Route::get('/auth/google/callback', 'Auth\LoginController@handleGoogleCallback')->name('auth.google.callback');

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

Route::controller('SettingController')->group(function () {
    Route::get('/setting', 'index')->name('system.setting.index'); //
    Route::patch('/setting', 'update')->name('system.setting.update'); //
    Route::get('/activate-sections', 'getActivateSection')->name('system.activate.index'); //
    Route::post('/activate-sections/{id}', 'updateActivateSection')->name('system.activate.update'); //
});

Route::controller('TraineeController')->prefix('trainee')->group(function () {
    Route::get('/get-activity-log/{id}', 'getUserActivityLog')->name('system.trainee.get-activity-log');
    Route::get('/get-auth-session/{id}', 'getAuthSession')->name('system.trainee.get-auth-session');
    Route::get('/get-workout/{id}', 'getWorkout')->name('system.trainee.get-workout');
    Route::post('/{id}/reset-plan', 'resetPlan')->name('system.trainee.reset-plan');
});
Route::resource('/trainee', 'TraineeController', ['as' => 'system']); //
Route::post('/workout/day/{dayId}/update', 'WorkoutController@storeDayExercises')->name('system.workout.updateDay');
Route::resource('/workout', 'WorkoutController', ['as' => 'system']); //

Route::get('/nutrition/my-plan', 'NutritionController@myPlan')->name('system.nutrition.my-plan');
Route::resource('/nutrition', 'NutritionController', ['as' => 'system']);

Route::resource('/website', 'WebsiteController', ['as' => 'system']); //
Route::prefix('website')->controller('WebsiteController')->group(function () {
    Route::post('/settings', 'updateSettings')->name('system.website.settings.update');
    Route::post('/section/{type}', 'updateSection')->name('system.website.section.update');
    Route::post('/items', 'storePostItem')->name('system.website.items.store');
    Route::post('/items/{id}', 'updatePostItem')->name('system.website.items.update');
    Route::delete('/items/{id}', 'destroyPostItem')->name('system.website.items.destroy');
    Route::post('/posts', 'storePostItem')->name('system.website.posts.store');
    Route::post('/posts/{id}', 'updatePostItem')->name('website.posts.update');
    Route::delete('/posts/{id}', 'destroyPostItem')->name('website.posts.destroy');
});


Route::get('/trainer', 'Dashboard@trainerDashboard')->name('system.dashboard.trainer');

// ─── Contact Messages ──────────────────────────────────────────────────────
Route::controller('MessageController')->prefix('message')->group(function () {
    Route::get('/', 'index')->name('system.message.index');
    Route::post('/update-status', 'updateStatus')->name('system.message.update-status');
});

// ─── Join Us Submissions ───────────────────────────────────────────────────
Route::controller('JoinUsController')->prefix('join-us')->group(function () {
    Route::get('/', 'index')->name('system.join-us.index');
    Route::get('/{id}', 'show')->name('system.join-us.show');
    Route::post('/{id}/status', 'updateStatus')->name('system.join-us.update-status');
    Route::delete('/{id}', 'destroy')->name('system.join-us.destroy');
});
