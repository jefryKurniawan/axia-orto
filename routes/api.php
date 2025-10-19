<?php

use Illuminate\Support\Facades\Route;

// Test route tanpa controller
Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working!',
        'timestamp' => now(),
        'status' => 'success'
    ]);
});

// Public routes with cache
Route::middleware('cache.headers:public;max_age=1800;etag')->group(function () {
    Route::get('/services/active', [\App\Http\Controllers\Api\ServiceController::class, 'getActiveServices']);
});

// Protected routes dengan auth sanctum
Route::middleware(['auth:sanctum', 'cache.headers:public;max_age=300;etag'])->group(function () {

    // Patient Routes
    Route::prefix('patients')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\PatientController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\PatientController::class, 'store']);
        Route::get('/stats', [\App\Http\Controllers\Api\PatientController::class, 'stats']);
        Route::get('/search', [\App\Http\Controllers\Api\PatientController::class, 'search']);
        Route::get('/{id}', [\App\Http\Controllers\Api\PatientController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\Api\PatientController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\PatientController::class, 'destroy']);
    });

    // Consultation Routes
    Route::prefix('consultations')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\ConsultationController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\ConsultationController::class, 'store']);
        Route::get('/today', [\App\Http\Controllers\Api\ConsultationController::class, 'todaySchedule']);
        Route::get('/{id}', [\App\Http\Controllers\Api\ConsultationController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\Api\ConsultationController::class, 'update']);
        Route::patch('/{id}/status', [\App\Http\Controllers\Api\ConsultationController::class, 'updateStatus']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\ConsultationController::class, 'destroy']);
    });

    // Service Routes
    Route::prefix('services')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\ServiceController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\ServiceController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\Api\ServiceController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\Api\ServiceController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\ServiceController::class, 'destroy']);
        Route::get('/type/{type}', [\App\Http\Controllers\Api\ServiceController::class, 'getByType']);
    });

    // User Routes
    Route::prefix('users')->group(function () {
        Route::get('/doctors', [\App\Http\Controllers\Api\UserController::class, 'getDoctors']);
        Route::get('/{id}/schedule', [\App\Http\Controllers\Api\UserController::class, 'getDoctorSchedule']);
    });

    // Patient Measurement Routes
    Route::prefix('patient-measurements')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\PatientMeasurementController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\PatientMeasurementController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\Api\PatientMeasurementController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\Api\PatientMeasurementController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\PatientMeasurementController::class, 'destroy']);
        Route::get('/patient/{patientId}', [\App\Http\Controllers\Api\PatientMeasurementController::class, 'getByPatient']);
    });

    // Inventory Item Routes
    Route::prefix('inventory-items')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\InventoryItemController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\InventoryItemController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\Api\InventoryItemController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\Api\InventoryItemController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\InventoryItemController::class, 'destroy']);
        Route::get('/category/{category}', [\App\Http\Controllers\Api\InventoryItemController::class, 'getByCategory']);
    });

    // Treatment Order Routes
    Route::prefix('treatment-orders')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\TreatmentOrderController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\TreatmentOrderController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\Api\TreatmentOrderController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\Api\TreatmentOrderController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\TreatmentOrderController::class, 'destroy']);
        Route::get('/stats', [\App\Http\Controllers\Api\TreatmentOrderController::class, 'stats']);
        Route::patch('/{id}/status', [\App\Http\Controllers\Api\TreatmentOrderController::class, 'updateStatus']);
    });

    // Order Item Routes
    Route::prefix('order-items')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\OrderItemController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\OrderItemController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\Api\OrderItemController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\Api\OrderItemController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\OrderItemController::class, 'destroy']);
        Route::get('/order/{orderId}', [\App\Http\Controllers\Api\OrderItemController::class, 'getByOrder']);
    });

    // Production Tracking Routes
    Route::prefix('production-trackings')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\ProductionTrackingController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\ProductionTrackingController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\Api\ProductionTrackingController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\Api\ProductionTrackingController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\ProductionTrackingController::class, 'destroy']);
        Route::get('/order/{orderId}', [\App\Http\Controllers\Api\ProductionTrackingController::class, 'getByOrder']);
        Route::patch('/{id}/status', [\App\Http\Controllers\Api\ProductionTrackingController::class, 'updateStatus']);
    });

    // Payment Routes
    Route::prefix('payments')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\PaymentController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\PaymentController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\Api\PaymentController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\Api\PaymentController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\PaymentController::class, 'destroy']);
        Route::get('/stats', [\App\Http\Controllers\Api\PaymentController::class, 'stats']);
        Route::patch('/{id}/status', [\App\Http\Controllers\Api\PaymentController::class, 'updateStatus']);
        Route::get('/order/{orderId}', [\App\Http\Controllers\Api\PaymentController::class, 'getByOrder']);
    });
});

// Auth Routes (jika menggunakan authentication)
Route::prefix('auth')->group(function () {
    Route::post('/login', [\App\Http\Controllers\Api\UserController::class, 'login']);
    Route::post('/register', [\App\Http\Controllers\Api\UserController::class, 'register']);
    Route::post('/logout', [\App\Http\Controllers\Api\UserController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/user', [\App\Http\Controllers\Api\UserController::class, 'user'])->middleware('auth:sanctum');
});
