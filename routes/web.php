<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;

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
});

// Route::get('/', function () {
//     return view('pages.index'); 
// });


// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {

    Route::resource('patients', PatientController::class);

    Route::get('/dashboard', function () {
        return redirect()->route('cleopatra.dashboard');
    })->name('dashboard');
    
    Route::get('/cleopatra-dashboard', function () {
        return view('cleopatra-dashboard');
    })->name('cleopatra.dashboard');

    Route::get('/alert', function () { 
        return view('pages.alert'); 
    })->name('alert');
    
    Route::get('/buttons', fn() => view('pages.buttons'))->name('buttons');
    Route::get('/index-1', fn() => view('pages.index-1'))->name('index-1');
    Route::get('/typography', fn() => view('pages.typography'))->name('typography');
    Route::get('/email', fn() => view('pages.email'))->name('email');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
 
});

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__.'/auth.php';
