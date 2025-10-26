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

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Route::get('/', function () {
//     return view('pages.index'); 
// });


// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Authentication routes (from Breeze)
require __DIR__ . '/auth.php';

Route::middleware(['auth'])->group(function () {

    Route::resource('patients', PatientController::class);

    Route::get('/dashboard', function () {
        return redirect()->route('cleopatra.dashboard');
    })->name('dashboard');

    // Dashboard
    Route::get('/cleopatra-dashboard', function () {
        return view('cleopatra-dashboard');
    })->name('cleopatra.dashboard');

    // Patient Management
    Route::prefix('patients')->group(function () {
        Route::get('/', [PatientController::class, 'index'])->name('patients.index');
        Route::get('/create', [PatientController::class, 'create'])->name('patients.create');
        Route::post('/', [PatientController::class, 'store'])->name('patients.store');
        Route::get('/{patient}', [PatientController::class, 'show'])->name('patients.show');
        Route::get('/{patient}/edit', [PatientController::class, 'edit'])->name('patients.edit');
        Route::put('/{patient}', [PatientController::class, 'update'])->name('patients.update');
        Route::delete('/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');
        Route::get('/{patient}/medical-history', [PatientController::class, 'medicalHistory'])->name('patients.medical-history');
    });

    // Consultations - khusus dokter & admin
    Route::middleware(['role:dokter,admin'])->prefix('consultations')->group(function () {
        Route::get('/', [ConsultationController::class, 'index'])->name('consultations.index');
        Route::get('/create', [ConsultationController::class, 'create'])->name('consultations.create');
        Route::post('/', [ConsultationController::class, 'store'])->name('consultations.store');
        Route::get('/{consultation}', [ConsultationController::class, 'show'])->name('consultations.show');
        Route::get('/{consultation}/edit', [ConsultationController::class, 'edit'])->name('consultations.edit');
        Route::put('/{consultation}', [ConsultationController::class, 'update'])->name('consultations.update');
        Route::patch('/{consultation}/status', [ConsultationController::class, 'updateStatus'])->name('consultations.status');
        Route::get('/today', [ConsultationController::class, 'today'])->name('consultations.today');
    });

    // Services Management
    Route::prefix('services')->group(function () {
        Route::get('/', [ServiceController::class, 'index'])->name('services.index');
        Route::get('/create', [ServiceController::class, 'create'])->name('services.create');
        Route::post('/', [ServiceController::class, 'store'])->name('services.store');
        Route::get('/{service}', [ServiceController::class, 'show'])->name('services.show');
        Route::get('/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
        Route::put('/{service}', [ServiceController::class, 'update'])->name('services.update');
        Route::patch('/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('services.toggle-status');
        Route::get('/type/{type}', [ServiceController::class, 'byType'])->name('services.by-type');
    });

    // Treatment Orders
    Route::prefix('treatment-orders')->group(function () {
        Route::get('/', [TreatmentOrderController::class, 'index'])->name('treatment-orders.index');
        Route::get('/create', [TreatmentOrderController::class, 'create'])->name('treatment-orders.create');
        Route::post('/', [TreatmentOrderController::class, 'store'])->name('treatment-orders.store');
        Route::get('/{order}', [TreatmentOrderController::class, 'show'])->name('treatment-orders.show');
        Route::get('/{order}/edit', [TreatmentOrderController::class, 'edit'])->name('treatment-orders.edit');
        Route::put('/{order}', [TreatmentOrderController::class, 'update'])->name('treatment-orders.update');
        Route::patch('/{order}/status', [TreatmentOrderController::class, 'updateStatus'])->name('treatment-orders.status');
        Route::get('/{order}/production', [TreatmentOrderController::class, 'production'])->name('treatment-orders.production');
    });

    // Inventory Management - khusus admin & staf
    Route::middleware(['role:admin,staf_klinik'])->prefix('inventory')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('/create', [InventoryController::class, 'create'])->name('inventory.create');
        Route::post('/', [InventoryController::class, 'store'])->name('inventory.store');
        Route::get('/{item}', [InventoryController::class, 'show'])->name('inventory.show');
        Route::get('/{item}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
        Route::put('/{item}', [InventoryController::class, 'update'])->name('inventory.update');
        Route::patch('/{item}/toggle-status', [InventoryController::class, 'toggleStatus'])->name('inventory.toggle-status');
        Route::get('/category/{category}', [InventoryController::class, 'byCategory'])->name('inventory.by-category');
    });

    // Payments
    Route::prefix('payments')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::patch('/{payment}/status', [PaymentController::class, 'updateStatus'])->name('payments.status');
        Route::get('/order/{order}', [PaymentController::class, 'byOrder'])->name('payments.by-order');
    });

    // Reports - khusus admin
    Route::middleware(['role:admin'])->prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/daily', [ReportController::class, 'daily'])->name('reports.daily');
        Route::get('/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
        Route::get('/patients', [ReportController::class, 'patients'])->name('reports.patients');
        Route::get('/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
        Route::get('/consultations', [ReportController::class, 'consultations'])->name('reports.consultations');
        Route::get('/orders', [ReportController::class, 'orders'])->name('reports.orders');
    });

    // UI Components & Pages
    Route::get('/alert', function () {
        return view('pages.alert');
    })->name('alert');

    Route::get('/buttons', function () {
        return view('pages.buttons');
    })->name('buttons');

    Route::get('/index-1', function () {
        return view('pages.index-1');
    })->name('index-1');

    Route::get('/typography', function () {
        return view('pages.typography');
    })->name('typography');

    Route::get('/email', function () {
        return view('pages.email');
    })->name('email');


    // Profile Management (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route::get('/alert', function () {
    //     return view('pages.alert');
    // })->name('alert');

    // Route::get('/buttons', fn() => view('pages.buttons'))->name('buttons');
    // Route::get('/index-1', fn() => view('pages.index-1'))->name('index-1');
    // Route::get('/typography', fn() => view('pages.typography'))->name('typography');
    // Route::get('/email', fn() => view('pages.email'))->name('email');

    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__ . '/auth.php';
