<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->post('/ping', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});
