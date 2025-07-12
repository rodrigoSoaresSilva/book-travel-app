<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\TravelRequestController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('jwt.auth')->group(function () {
    Route::apiResource('travel-requests', TravelRequestController::class);
    Route::post('travel-requests/{travelRequest}/approve', [TravelRequestController::class, 'approve']);
    Route::post('travel-requests/{travelRequest}/cancel', [TravelRequestController::class, 'cancel']);
});

Route::prefix('auth')->group(function () {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    Route::middleware('jwt.auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('me', [AuthController::class, 'me']);
    });
});
