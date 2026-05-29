<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\ConsultationController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SyncController;

// Auth (public)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register/first-admin', [AuthController::class, 'firstAdmin']);

// Auth (protected)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/doctors', [AuthController::class, 'doctors']);
});

// Dashboard
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});

// Patients
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/patients', [PatientController::class, 'index']);
    Route::get('/patients/stats', [PatientController::class, 'stats']);
    Route::get('/patients/{uuid}', [PatientController::class, 'show']);
    Route::post('/patients', [PatientController::class, 'store']);
    Route::put('/patients/{uuid}', [PatientController::class, 'update']);
    Route::delete('/patients/{uuid}', [PatientController::class, 'destroy']);
});

// Consultations
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/consultations', [ConsultationController::class, 'index']);
    Route::get('/consultations/today', [ConsultationController::class, 'today']);
    Route::get('/consultations/{uuid}', [ConsultationController::class, 'show']);
    Route::post('/consultations', [ConsultationController::class, 'store']);
    Route::put('/consultations/{uuid}', [ConsultationController::class, 'update']);
    Route::delete('/consultations/{uuid}', [ConsultationController::class, 'destroy']);
});

// Services
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/services/active', [ServiceController::class, 'active']);
    Route::get('/services/{uuid}', [ServiceController::class, 'show']);
    Route::post('/services', [ServiceController::class, 'store']);
    Route::put('/services/{uuid}', [ServiceController::class, 'update']);
    Route::delete('/services/{uuid}', [ServiceController::class, 'destroy']);
});

// Health check
Route::get('/health', fn () => response()->json(['status' => 'ok']));

// Sync (batch offline operations)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/sync/batch', [SyncController::class, 'batch']);
});
