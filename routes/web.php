<?php

use App\Modules\Web\SendEmailController;
use App\Modules\Web\WebController;
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

Route::get('/', [WebController::class,'index'])->name('home');
Route::get('/contact', [WebController::class,'contact'])->name('contact');
Route::post('send-email', [SendEmailController::class, 'store'])->name('sendmail');
