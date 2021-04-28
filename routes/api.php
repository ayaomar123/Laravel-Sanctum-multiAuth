<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ForgotController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/user/register', [UserController::class, 'register']);

Route::post('/user/login', [UserController::class, 'login']);

Route::group(['middleware' => ['auth:users']], function () {
    Route::post('/user/logout', [UserController::class, 'logout']);
    Route::get('/user/profile', [UserController::class, 'profile']);
    Route::post('/user/editProfile', [UserController::class, 'editProfile']);
    Route::post('/user/editPassword', [UserController::class, 'editPassword']);
});

Route::post('/admin/register', [AdminController::class, 'register']);

Route::post('/admin/login', [AdminController::class, 'login']);

Route::group(['middleware' => ['auth:admins']], function () {
    Route::post('/admin/logout', [AdminController::class, 'logout']);
    Route::get('/admin/profile', [AdminController::class, 'profile']);
    Route::post('/admin/updateProfile', [AdminController::class, 'updateProfile']);
    Route::post('/admin/editPassword', [AdminController::class, 'editPassword']);
    Route::get('/admin/getAllUser', [AdminController::class, 'getAllUser']);
});

Route::post('/forgot', [ForgotController::class, 'forgot']);
Route::post('/password/reset', [ForgotController::class, 'reset'])->name('password.reset');

