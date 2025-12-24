<?php

use App\Http\Controllers\DriverController;
use App\Http\Controllers\ReportController;
use App\Models\AdminActivity;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Requestcontroller;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SystemSettingsController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BranchFinanceController;
use App\Http\Controllers\CustomerController;

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
    Route::resource('users', UserController::class);
    Route::resource('branch', BranchController::class);
    Route::resource('request', Requestcontroller::class);
    Route::patch('/request/{id}/status', [RequestController::class, 'updateStatus'])
        ->name('request.updateStatus');

        
    Route::resource('systems', SystemSettingsController::class);
    Route::post('/system-settings/auto-assign', [SystemSettingsController::class, 'updateAutoAssignSetting'])
        ->name('system-settings.auto-assign.update');
    Route::post('/users/toggle-status/{id}', [UserController::class, 'toggleStatus']);

    Route::get('/whatsapp/sender/{id}', [Requestcontroller::class, 'openForSender'])
        ->name('whatsapp.sender');

    Route::get('/admin/logs', [RequestController::class, 'adminlog'])
        ->name('request.adminlog');
    Route::resource('drivers', DriverController::class);
    Route::get('/drivers/{id}/shipments', [DriverController::class, 'shipments'])
        ->name('drivers.shipments');
    Route::get('/drivers/{id}/shipments/print', [DriverController::class, 'printShipments'])
        ->name('drivers.shipments.print');


    Route::get('/reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');

    Route::get('/reports/revenue/pdf', [ReportController::class, 'exportRevenuePDF'])
        ->name('reports.revenue.pdf');

    Route::get('/whatsapp/receiver/{id}', [Requestcontroller::class, 'openForReceiver'])
        ->name('whatsapp.receiver');

    Route::get('/request/{id}/invoice', [RequestController::class, 'invoice'])->name('request.invoice');
    Route::get('/shipments/select-customer', [RequestController::class, 'selectCustomer'])
    ->name('shipments.selectCustomer');

    Route::get('/shipments/create-customer', [RequestController::class, 'createCustomer'])
    ->name('shipments.createCustomer');

Route::post('/shipments/store-customer', [RequestController::class, 'storeCustomer'])
    ->name('shipments.storeCustomer');

    Route::get('/system-settings/auto-assign', [SystemSettingsController::class, 'getAutoAssignSetting'])
        ->name('system-settings.auto-assign.get');
Route::get('/customers/search', [CustomerController::class, 'search'])
    ->name('customers.search');

    Route::resource('customers', CustomerController::class);

    Route::get('/finance/branches', [BranchFinanceController::class, 'index'])
        ->name('finance.branches.index');

    Route::get('/finance/branches/{branch}', [BranchFinanceController::class, 'show'])
        ->name('finance.branches.show');

    Route::get('/finance/settlements/create', [BranchFinanceController::class, 'createSettlement'])
        ->name('finance.settlements.create');

    Route::post('/finance/settlements', [BranchFinanceController::class, 'storeSettlement'])
        ->name('finance.settlements.store');

    Route::get('/api/branches/{branch}/balance', [BranchFinanceController::class, 'apiBranchBalance'])
        ->name('api.branch.balance');





    // Dashboard النظام
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard.index');

    // التقارير
    Route::get('/reports', [ReportController::class, 'index'])
        ->name('reports.index');

    Route::get('/reports/dashboard', [ReportController::class, 'dashboard'])
        ->name('reports.dashboard');
    Route::get('/reports/shipments', [ReportController::class, 'shipments'])
        ->name('reports.shipments');

    // تقارير العملاء
    Route::get('/reports/customers/{id}', [ReportController::class, 'customerStatement'])
        ->name('reports.customers.statement');

    Route::get('/reports/customers/{id}/pdf', [ReportController::class, 'customerStatementPDF'])
        ->name('reports.customers.statement.pdf');

    // تقارير الفروع
    Route::get('/reports/branches/{id}', [ReportController::class, 'branchStatement'])
        ->name('reports.branches.statement');

    Route::get('/reports/branches/{id}/pdf', [ReportController::class, 'branchStatementPDF'])
        ->name('reports.branches.statement.pdf');

    // الإقفال الشهري
    Route::get('/reports/monthly/closing/pdf', [ReportController::class, 'monthlyClosingPDF'])
        ->name('reports.monthly.closing.pdf');


Route::get('/reports/revenue', [ReportController::class, 'dashboard'])
    ->name('reports.revenue');

});

require __DIR__ . '/auth.php';