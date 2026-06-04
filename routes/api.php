<?php

use App\Http\Controllers\Api\PwaAuthController;
use Illuminate\Support\Facades\Route;

// PWA Auth Routes
Route::post('/pwa/login', [PwaAuthController::class, 'login']);

Route::middleware('pwa.auth')->group(function () {
    Route::get('/pwa/check', [PwaAuthController::class, 'check']);
    Route::post('/pwa/logout', [PwaAuthController::class, 'logout']);
    Route::post('/pwa/refresh', [PwaAuthController::class, 'refresh']);
});

// Existing Sanctum routes
Route::middleware('auth:sanctum')->post('/ping', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});
