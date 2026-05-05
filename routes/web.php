<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BranchFinanceController;
use App\Http\Controllers\CashClosingController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerFinanceController;
use App\Http\Controllers\CustomerPaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\FinanceSettlementController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PassengersController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReceiptHeaderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\ShipmentPackagesController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TransactionCategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WhatsAppController;
use Illuminate\Support\Facades\Route;



Route::get('/', [LandingPageController::class,'index'])->name('welcome');
Route::get('/receipt/{type}/{id}', [ReceiptController::class, 'generate'])->name('receipt.generate');
Route::get('/pricing', [PackageController::class, 'index'])->name('pricing.page');
Route::middleware('guest')->group(function () {
    
    // صفحة عرض إدخال الكود
    Route::get('/verify-otp', [OtpController::class, 'showVerifyForm'])->name('otp.verify.form');
    
    // إرسال الكود للتحقق منه
    Route::post('/verify-otp', [OtpController::class, 'verify'])->name('otp.verify');
    
    // مسار إعادة إرسال الكود (الذي كان يسبب المشكلة)
    Route::post('/verify-otp/resend', [OtpController::class, 'resend'])->name('otp.resend');
    
});

Route::middleware('auth')->group(function () {
    Route::post('/subscription/request', [SubscriptionController::class, 'requestSubscription'])->name('subscription.request');
    Route::get('/account/pending', function () {
        $app = auth()->user()->App ?? null;
        if ($app && $app->hasActiveSubscription()) {
            return redirect()->route('dashboard.index')
                             ->with('info', 'تم تفعيل حسابك بنجاح! أهلاً بك.');
        }
        $adminPhone = "967780261952";
        return view('auth.pending', compact('adminPhone'));
    })->name('account.pending');
    // ===================================================)}|===========
    // 2. نظام إدارة الشحن (محمي: يتطلب أن يكون التطبيق مف)}|عل is_active = true)
    // ===================================================)}|===========
    Route::middleware(['app.active','active.Subscription'])->group(function () {
        
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::resource('users', UserController::class)->middleware('admin');

    // Branch routes with super admin middleware for create and store
    Route::get('branch', [BranchController::class, 'index'])->name('branch.index');
    Route::get('branch/create', [BranchController::class, 'create'])->name('branch.create')->middleware('admin');
    Route::post('branch', [BranchController::class, 'store'])->name('branch.store')->middleware(['admin','check.limit:branches']);
    Route::get('branch/{branch}', [BranchController::class, 'show'])->name('branch.show');
    Route::get('branch/{branch}/edit', [BranchController::class, 'edit'])->name('branch.edit');
    Route::put('branch/{branch}', [BranchController::class, 'update'])->name('branch.update');
    Route::delete('branch/{branch}', [BranchController::class, 'destroy'])->name('branch.destroy');

    //==================================================================   معتمد   ===================================================
    // shipment outgoing
    Route::get('/shipment/outgoing', [ShipmentController::class, 'outgoingIndex'])->name('shipment.outgoing.index');
    Route::get('/shipment/outgoing/create', [ShipmentController::class,'outgoingCreate'])->name('shipment.outgoing.create');
    Route::post('/shipment/outgoing/store',[ShipmentController::class,'outgoingStore'])->name('shipment.outgoing.store')->middleware(['check.limit:shipments']);
    Route::get('/shipment/outgoing/show/{id}', [ShipmentController::class,'outgoingShow'])->name('shipment.outgoing.show');
    Route::get('/shipments/outgoing/{id}', [ShipmentController::class, 'outgoingEdit'])->name('shipment.outgoing.edit');
    Route::put('/shipments/outgoing/{id}', [ShipmentController::class, 'outgoingUpdate'])->name('shipment.outgoing.update');
    // shipment incoming
    Route::get('/shipment/incoming', [ShipmentController::class, 'incomingIndex'])->name('shipment.incoming.index');
    Route::get('/shipment/incoming/show/{id}', [ShipmentController::class, 'incomingShow'])->name('shipment.incoming.show');

    Route::resource('customers', CustomerController::class);
    Route::resource('passengers', PassengersController::class);
    Route::resource('offices', OfficeController::class);
    Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/customers/{id}/add-payment', [CustomerController::class, 'addPayment'])
    ->name('customers.addPayment');

    // shipmentpackage outgoing
    Route::get('shipmentpackage/outgoing/index', [ShipmentPackagesController::class,'sentIndex'])->name('shipmentpackage.outgoing.index');
    Route::get('shipmentpackage/outgoing/create', [ShipmentPackagesController::class,'sentCreate'])->name('shipmentpackage.outgoing.create');
    Route::post('shipmentpackage/outgoing/store', [ShipmentPackagesController::class,'sentStore'])->middleware(['check.limit:packages'])->name('shipmentpackage.outgoing.store');
    Route::get('shipmentpackage/outgoing/show/{id}', [ShipmentPackagesController::class,'sentShow'])->name('shipmentpackage.outgoing.show');
    Route::post('shipmentpackage/outgoing/updateStatus/{id}', [ShipmentPackagesController::class,'updateStatus'])->name('shipmentpackage.updateStatus');
    Route::post('shipmentpackage/{package}/remove-shipment/{shipment}', [ShipmentPackagesController::class, 'removeShipment'])->name('shipmentpackage.removeShipment');
    Route::post('shipmentpackage/add-shipment/{package}', [ShipmentPackagesController::class, 'addShipment'])->name('shipmentpackage.addShipment');

    // shipmentpackage incoming
    Route::get('shipmentpackage/incoming/index', [ShipmentPackagesController::class, 'incomingIndex'])->name('shipmentpackage.incoming.index');
    Route::get('shipmentpackage/incoming/create', [ShipmentPackagesController::class, 'incomingCreate'])->name('shipmentpackage.incoming.create');
    Route::post('shipmentpackage/incoming/store', [ShipmentPackagesController::class,'incomingStore'])->name('shipmentpackage.incoming.store');
    Route::get('shipmentpackage/incoming/show/{id}', [ShipmentPackagesController::class,'incomingShow'])->name('shipmentpackage.incoming.show');

    // mobile routes
    Route::view('/mobile/people', 'mobile.pages.people.index')->name('people.index');
    Route::view('/mobile/shipmentpackage', 'mobile.pages.shipmentpackage.index')->name('mobile.shipmentpackage.index');
    Route::view('/mobile/office','mobile.pages.office.index')->name('mobile.office');
    Route::view('/mobile/shipment','mobile.pages.shipment.index')->name('mobile.shipment');

    //=============================================================================   معتمد   ======================================

   
 
    // Route::resource('shipment', ShipmentController::class);
    Route::post('/shipment/{id}/status', [ShipmentController::class, 'updateStatus'])
        ->name('shipment.updateStatus');
    Route::patch('/shipment/{id}/return', [ShipmentController::class, 'returnShipment'])
        ->name('shipment.return');
    Route::patch('/shipment/{id}/cancel', [ShipmentController::class, 'cancelShipment'])
        ->name('shipment.cancel');

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

    Route::post('/finance/settlements', [FinanceSettlementController::class, 'store'])
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

    Route::get('/shipmentpackage/print/{id}', [ShipmentPackagesController::class, 'printManifest'])->name('shipmentpackage.print');

    Route::get('/shipmentpackage/print-driver/{id}', [ShipmentPackagesController::class, 'printManifestD'])->name('shipmentpackage.printD');

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

    Route::patch('/shipment-packages/{shipmentId}/unlink', [ShipmentPackagesController::class, 'unlinkFromPackage'])
        ->name('shipmentpackage.unlink');

    // Database Backup
    Route::post('/backup/upload', [BackupController::class, 'uploadBackup'])
        ->name('backup.upload');

    // بيانات الاستلام
    Route::get('/receipts', [ReceiptHeaderController::class, 'index'])->name('receipts.index');
    Route::get('/receipts/create', [ReceiptHeaderController::class, 'create'])->name('receipts.create');
    Route::post('/receipts', [ReceiptHeaderController::class, 'store'])->name('receipts.store');
    Route::get('/receipts/{receipt}', [ReceiptHeaderController::class, 'show'])->name('receipts.show');
    Route::get('/receipts/{receipt}/edit', [ReceiptHeaderController::class, 'edit'])->name('receipts.edit');
    Route::put('/receipts/{receipt}', [ReceiptHeaderController::class, 'update'])->name('receipts.update');
    Route::put('/receipt-items/{item}/toggle-delivery', [ReceiptHeaderController::class, 'toggleDelivery'])->name('receipt-items.toggle-delivery');
    Route::post('/receipts/{receipt}/add-item', [ReceiptHeaderController::class, 'addItem'])->name('receipts.add-item');
    Route::put('/receipt-items/{item}', [ReceiptHeaderController::class, 'updateItem'])->name('receipt-items.update');
    Route::delete('/receipt-items/{item}', [ReceiptHeaderController::class, 'destroyItem'])->name('receipt-items.destroy');
    Route::get('/offices/unverified', [OfficeController::class, 'unverifiedIndex'])->name('offices.unverified.index');
    Route::get('/offices/create', [OfficeController::class, 'create'])->name('offices.create');
    Route::get('/offices/{office}', [OfficeController::class, 'show'])->name('offices.show');
    Route::get('/offices/{office}', [OfficeController::class, 'edit'])->name('offices.edit');

    Route::delete('/offices/{office}', [OfficeController::class, 'destroy'])->name('offices.destroy');
    Route::get('/app/settings', [AppController::class, 'settings'])->name('app.settings');
    Route::get('/app', [AppController::class, 'index'])->name('app.index');
    Route::PUT('/app/update', [AppController::class, 'update'])->name('app.update');
    Route::post('/connect/send/{receiverAppId}', [ConnectionController::class, 'sendRequest'])->name('offices.connect');
    Route::post('/connect/accept/{id}', [ConnectionController::class, 'accept'])->name('connections.accept');
    Route::post('/connect/reject/{id}', [ConnectionController::class, 'reject'])->name('connections.reject');
    
    
    });
});


require __DIR__ . '/auth.php';
