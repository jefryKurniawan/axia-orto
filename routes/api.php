<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\PatientAttachmentController;
use App\Http\Controllers\Api\ProductCategoryController;
use App\Http\Controllers\Api\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Patients and Nested Routes
Route::prefix('v1')->group(function () {
    // 1. Rute Pasien (Parent Resource)
    Route::apiResource('patients', PatientController::class);

    // 2. Rute Nested untuk Lampiran (index dan store yang terikat ke pasien)
    // URL: /api/v1/patients/{patient}/attachments
    Route::resource('patients.attachments', PatientAttachmentController::class)->only([
        'index',
        'store'
    ]);

    // 3. Rute Resource API untuk Product Categories
    Route::apiResource('product-categories', ProductCategoryController::class)->parameters([
        // Opsional: Gunakan kolom 'uuid' sebagai route key
        'product-categories' => 'product_category:uuid',
    ]);

    // 4. Rute Resource API untuk Products
    Route::apiResource('products', ProductController::class)->parameters([
        'products' => 'product:uuid', // Menggunakan kolom 'uuid' untuk URL
    ]);
});
Route::apiResource('attachments', PatientAttachmentController::class)->only([
    'show',
    'update',
    'destroy'
])->parameters([
    'attachments' => 'patient_attachment:uuid',
]);
