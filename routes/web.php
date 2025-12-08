<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\usercontroller;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\requestcontroller;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SystemSettingsController;
use App\Http\Controllers\BranchController;

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
    Route::resource('branch', BranchController::class);
    Route::resource('request', requestcontroller::class);
    Route::patch('/request/{id}/status', [RequestController::class, 'updateStatus'])
        ->name('request.updateStatus');
    Route::resource('systems', SystemSettingsController::class);
    Route::post('/system-settings/auto-assign', [SystemSettingsController::class, 'updateAutoAssignSetting'])
        ->name('system-settings.auto-assign.update');

    Route::get('/whatsapp/sender/{id}', [requestcontroller::class, 'openForSender'])
    ->name('whatsapp.sender');

Route::get('/whatsapp/receiver/{id}', [requestcontroller::class, 'openForReceiver'])
    ->name('whatsapp.receiver');

    Route::get('/request/{id}/invoice', [requestController::class, 'invoice'])->name('request.invoice');
    Route::get('/system-settings/auto-assign', [SystemSettingsController::class, 'getAutoAssignSetting'])
        ->name('system-settings.auto-assign.get');
});

require __DIR__ . '/auth.php';
