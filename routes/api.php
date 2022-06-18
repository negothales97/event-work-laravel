<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ResetPasswordController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group([
    'middleware' => 'guest'
], function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('forgot-password', [ResetPasswordController::class, 'sendResetLink'])->name('forgot-password');
    Route::post('reset-password', [ResetPasswordController::class, 'resetPassword'])->name('reset-password');
});

Route::group([], function () {
    Route::apiResources([
        'users' => UserController::class,
    ]);
});

Route::post('/login', [AuthController::class, 'login'])->name('login');
