<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Semua endpoint API di sini. Diproteksi oleh Sanctum (cookie-based SPA).
| Prefix: /api
|
*/

// Auth (public)
Route::post('/login', [AuthController::class, 'login']);

// Auth (protected)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

// Health check
Route::get('/health', fn () => response()->json(['status' => 'ok']));
