<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\BranchFinanceController;
use App\Http\Controllers\CashClosingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerFinanceController;
use App\Http\Controllers\CustomerPaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\ShipmentPackagesController;
use App\Http\Controllers\TransactionCategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WhatsAppController;
use Illuminate\Support\Facades\Route;


// الصفحة الرئيسية تحول للدخول
Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::resource('users', UserController::class)->middleware('admin');
    
    // Branch routes with super admin middleware for create and store
    Route::get('branch', [BranchController::class, 'index'])->name('branch.index');
    Route::get('branch/create', [BranchController::class, 'create'])->name('branch.create')->middleware('admin');
    Route::post('branch', [BranchController::class, 'store'])->name('branch.store')->middleware('admin');
    Route::get('branch/{branch}', [BranchController::class, 'show'])->name('branch.show');
    Route::get('branch/{branch}/edit', [BranchController::class, 'edit'])->name('branch.edit');
    Route::put('branch/{branch}', [BranchController::class, 'update'])->name('branch.update');
    Route::delete('branch/{branch}', [BranchController::class, 'destroy'])->name('branch.destroy');
    
    Route::resource('shipment', ShipmentController::class);
    Route::patch('/shipment/{id}/status', [ShipmentController::class, 'updateStatus'])
        ->name('shipment.updateStatus');
    Route::put('shipment/updatePaymentMethod/{id}', [ShipmentController::class, 'updatePaymentMethod'])->name('shipment.updatePaymentMethod');
    Route::get('/shipments/{id}/thermal', [InvoiceController::class, 'printThermal'])
        ->name('shipment.printThermal');
    Route::post('/users/toggle-status/{id}', [UserController::class, 'toggleStatus']);

    Route::get('/whatsapp/sender/{id}', [WhatsAppController::class, 'openForSender'])
        ->name('whatsapp.sender');

    Route::get('/whatsapp/receiver/{id}', [WhatsAppController::class, 'openForReceiver'])
        ->name('whatsapp.receiver');

    Route::get('/whatsapp/branch-manifest/{id}/{branchCode?}', [WhatsAppController::class, 'sendBranchManifest'])
        ->name('whatsapp.branchManifest');

    Route::get('/whatsapp/driver-manifest/{id}', [WhatsAppController::class, 'sendDriverManifest'])
        ->name('whatsapp.driverManifest');

    Route::get('/admin/logs', [ShipmentController::class, 'adminlog'])
        ->name('shipment.adminlog');
    Route::resource('drivers', DriverController::class);
    Route::get('/drivers/{id}/shipments', [DriverController::class, 'shipments'])
        ->name('drivers.shipments');
    Route::get('/drivers/{id}/shipments/print', [DriverController::class, 'printShipments'])
        ->name('drivers.shipments.print');

    Route::resource('shipments', ShipmentController::class);
    Route::get('/reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');

    Route::get('/reports/revenue/pdf', [ReportController::class, 'exportRevenuePDF'])
        ->name('reports.revenue.pdf');

    Route::get('/shipment/{id}/invoice', [InvoiceController::class, 'printInvoice'])->name('shipment.invoice');
    Route::get('/shipments/select-customer', [ShipmentController::class, 'selectCustomer'])
        ->name('shipments.selectCustomer');

    Route::get('/shipments/create-customer', [ShipmentController::class, 'createCustomer'])
        ->name('shipments.createCustomer');

    Route::post('/shipments/store-customer', [ShipmentController::class, 'storeCustomer'])
        ->name('shipments.storeCustomer');

    
    Route::get('/customers/search', [CustomerController::class, 'search'])
        ->name('customers.search');
    Route::get('/customers/{id}/comprehensive-report', [CustomerController::class, 'comprehensiveReport'])
        ->name('customers.comprehensive-report');
    Route::post('/customers/{customer}/clear-balance', [CustomerController::class, 'clearBalance'])
        ->name('customers.clear-balance');

    Route::resource('customers', CustomerController::class);


    // دفعات العملاء
    Route::post('/shipments/{shipment}/payment', [CustomerPaymentController::class, 'store'])
        ->name('customer-payments.store');
    Route::get('/shipments/{shipment}/payments', [CustomerPaymentController::class, 'index'])
        ->name('customer-payments.index');
    Route::delete('/customer-payments/{payment}', [CustomerPaymentController::class, 'destroy'])
        ->name('customer-payments.destroy');

    // مالية العملاء
    Route::get('/finance/customers', [CustomerFinanceController::class, 'index'])->name('finance.customers.index');
    Route::get('/finance/customers/{customer}/settle', [CustomerFinanceController::class, 'createSettlement'])->name('finance.customers.settle');
    Route::post('/finance/customers/{customer}/settle', [CustomerFinanceController::class, 'storeSettlement'])->name('finance.customers.storeSettlement');
// صفحة عرض تفاصيل العميل
Route::get('/finance/customers/{customer}', [CustomerFinanceController::class, 'show'])
    ->name('finance.customers.show');
    // مالية الفروع
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

    Route::resource('shipmentpackage', ShipmentPackagesController::class);
    Route::get('/shipmentpackage/print/{id}',[ShipmentPackagesController::class, 'printManifest'])->name('shipmentpackage.print');

    Route::get('/shipmentpackage/print-driver/{id}',[ShipmentPackagesController::class, 'printManifestD'])->name('shipmentpackage.printD');

    // دفعات الحزم للفروع
    Route::post('/branch-package-payment', [App\Http\Controllers\BranchPackagePaymentController::class, 'store'])
        ->name('branch-package-payment.store');
    Route::get('/branch-package-payment/{branchShipmentPackageId}', [App\Http\Controllers\BranchPackagePaymentController::class, 'index'])
        ->name('branch-package-payment.index');
    Route::delete('/branch-package-payment/{id}', [App\Http\Controllers\BranchPackagePaymentController::class, 'destroy'])
        ->name('branch-package-payment.destroy');

    // Cash Box / Petty Cash Transactions
    Route::resource('transactions', TransactionController::class)->only(['index', 'create', 'store']);
    Route::get('/transactions/{transaction}/receipt', [TransactionController::class, 'generateReceipt'])
        ->name('transactions.receipt');
    
    // Transaction Category Settings
    Route::resource('transaction-categories', TransactionCategoryController::class)->except(['show', 'create', 'edit'])->middleware('super.admin');
    
    // Daily Cash Closing
    Route::get('/closings/export', [CashClosingController::class, 'export'])->name('closings.export');
    Route::get('/closings', [CashClosingController::class, 'index'])->name('closings.index');
    Route::get('/closings/create', [CashClosingController::class, 'create'])->name('closings.create');
    Route::post('/closings', [CashClosingController::class, 'store'])->name('closings.store');

    Route::patch('/shipment-packages/{id}/mark-all-delivered', [ShipmentPackagesController::class, 'markAllDelivered'])
    ->name('shipmentpackage.mark-all-delivered');
});


require __DIR__ . '/auth.php';