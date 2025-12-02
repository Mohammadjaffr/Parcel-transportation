<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\usercontroller;
use App\Http\Controllers\couponcontroller;
use App\Http\Controllers\drivercontroller;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\requestcontroller;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\SystemSettingsController;

// Route::get('/', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::resource('users', usercontroller::class);
    Route::resource('drivers', drivercontroller::class);
    Route::resource('request',requestcontroller::class);
    Route::resource('Coupon',couponcontroller::class);
    Route::resource('Vehicle',VehicleController::class);
    Route::resource('systems',SystemSettingsController::class);
    Route::post('/system-settings/auto-assign', [SystemSettingsController::class, 'updateAutoAssignSetting'])
        ->name('system-settings.auto-assign.update');

        
    Route::get('/system-settings/auto-assign', [SystemSettingsController::class, 'getAutoAssignSetting'])
        ->name('system-settings.auto-assign.get');
});

require __DIR__.'/auth.php';