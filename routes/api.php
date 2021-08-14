<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\RestaurantContorller;


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
Route::prefix('v1')->group(function () {
    
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/verify', [AuthController::class, 'verify']);
    Route::post('/get-otp', [AuthController::class, 'getOtp']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);


    Route::post('/send-notification', [NotificationController::class, 'send']);
    
    
    Route::middleware('auth:sanctum')->group( function () {


        Route::prefix('user')->group(function () {
            Route::get('/profile', [AuthController::class, 'profile']);
            Route::post('/store-notification-token', [AuthController::class, 'storeToken']);
        });

        Route::prefix('restaurant')->group(function () {
            Route::get('/list', [RestaurantContorller::class, 'index']);
        });


    });

});



