<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TreatmentOrderController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;

// Authentication routes (from Breeze)
require __DIR__ . '/auth.php';

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('welcome');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Patient routes
    Route::resource('patients', PatientController::class);
    Route::get('patients/{patient}/medical-history', [PatientController::class, 'medicalHistory'])->name('patients.medical-history');
    Route::get('patients/search', [PatientController::class, 'search'])->name('patients.search');
    Route::get('patients/stats', [PatientController::class, 'stats'])->name('patients.stats');

    // Consultation routes
    Route::resource('consultations', ConsultationController::class);

    // Service routes
    Route::resource('services', ServiceController::class);

    // Treatment Order routes
    Route::resource('treatment-orders', TreatmentOrderController::class);

    // Inventory routes
    Route::resource('inventory', InventoryController::class);

    // Payment routes
    Route::resource('payments', PaymentController::class);

    // Report routes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
