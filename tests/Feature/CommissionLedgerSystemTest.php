<?php

namespace Tests\Feature;

use App\Http\Controllers\PassengersController;
use App\Http\Controllers\PassengerTripController;
use App\Http\Controllers\ShipmentPackagesController;
use App\Models\App;
use App\Models\Branch;
use App\Models\CashCategory;
use App\Models\CashTransaction;
use App\Models\Driver;
use App\Models\Passengers;
use App\Models\PassengerTrip;
use App\Models\Shipment;
use App\Models\ShipmentPackage;
use App\Models\User;
use App\Services\CashTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class CommissionLedgerSystemTest extends TestCase
{
    protected App $appModel;
    protected Branch $branch;
    protected User $user;
    protected Driver $driver;
    protected CashTransactionService $cashService;

    /**
     * إعداد بيئة الاختبار:
     * - بدء Transaction لحماية قاعدة البيانات الحقيقية من أي تعديل
     * - تجهيز المستخدم والفرع والسائق لجميع الاختبارات
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 🛡️ شرط الأمان: بدء المعاملة المالية لحماية البيانات الحقيقية
        DB::beginTransaction();

        // جلب أو إنشاء تطبيق أساسي
        $this->appModel = App::first() ?? App::create([
            'name'       => 'Test App',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // جلب أو إنشاء فرع للاختبار
        $this->branch = Branch::where('app_id', $this->appModel->id)->first() ?? Branch::create([
            'app_id'  => $this->appModel->id,
            'name'    => 'فرع الاختبار الرئيسي',
            'code'    => 'TEST_BR_' . rand(100, 999),
            'city'    => 'صنعاء',
            'address' => 'شارع الستين',
            'phone'   => '967711223344',
        ]);

        // جلب أو إنشاء مستخدم للاختبار
        $this->user = User::where('app_id', $this->appModel->id)->first() ?? User::create([
            'app_id'    => $this->appModel->id,
            'branch_id' => $this->branch->id,
            'name'      => 'مشغل الاختبار',
            'phone'     => '967' . rand(700000000, 799999999),
            'password'  => bcrypt('password'),
            'type'      => 'user',
        ]);

        // جلب أو إنشاء سائق للاختبار
        $this->driver = Driver::where('app_id', $this->appModel->id)->first() ?? Driver::create([
            'app_id'     => $this->appModel->id,
            'branch_id'  => $this->branch->id,
            'created_by' => $this->user->id,
            'name'       => 'سائق الاختبار',
            'phone'      => '967' . rand(700000000, 799999999),
        ]);

        // تسجيل الدخول للمستخدم
        $this->actingAs($this->user);
        auth()->login($this->user);

        $this->cashService = app(CashTransactionService::class);
    }

    /**
     * إلغاء جميع التعديلات في نهاية كل دالة اختبار بنسبة 100%
     */
    protected function tearDown(): void
    {
        // 🛡️ شرط الأمان: التراجع عن أي عملية تمت في قاعدة البيانات
        while (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        parent::tearDown();
    }

    /**
     * دالة مساعدة لإنشاء راكب تجريبي
     */
    protected function createPassenger(float $commission = 1000.0, string $status = 'pending'): Passengers
    {
        return Passengers::create([
            'date'                    => now()->toDateString(),
            'passenger_number'        => '967' . rand(700000000, 799999999),
            'pickup_location'         => 'صنعاء',
            'destination'             => 'عدن',
            'count'                   => 1,
            'office_commission'       => $commission,
            'other_office_commission' => 0.0,
            'status'                  => $status,
            'branch_id'               => $this->branch->id,
            'note'                    => 'راكب تجريبي',
        ]);
    }

    /**
     * دالة مساعدة لإنشاء طرد تجريبي
     */
    protected function createShipment(float $commission = 500.0, string $status = 'pending', ?int $packageId = null): Shipment
    {
        return Shipment::create([
            'bond_number'             => 'BOND-' . Str::upper(Str::random(8)),
            'sender_branch_id'        => $this->branch->id,
            'receiver_branch_id'      => $this->branch->id,
            'created_by'              => $this->user->id,
            'package_type'            => 'parcel',
            'package_fee'             => $commission,
            'package_commission_rate' => 100.0,
            'package_commission_amount' => $commission,
            'total_commission'        => $commission,
            'total_amount'            => $commission,
            'payment_method'          => 'prepaid',
            'status'                  => $status,
            'shipment_package_id'     => $packageId,
        ]);
    }

    /**
     * دالة مساعدة لإنشاء حمولة شحنات تجريبية
     */
    protected function createPackage(string $status = 'pending'): ShipmentPackage
    {
        return ShipmentPackage::create([
            'app_id'           => $this->appModel->id,
            'created_by'       => $this->user->id,
            'sender_branch_id' => $this->branch->id,
            'driver_id'        => $this->driver->id,
            'status'           => $status,
        ]);
    }

    // =========================================================================
    // 1. اختبار منع ازدواجية عمولة الركاب (الإصلاح #1)
    // =========================================================================
    public function test_01_prevent_duplicate_passenger_commission_from_passenger_controller(): void
    {
        // إنشاء راكب بعمولة 1000
        $passenger1 = $this->createPassenger(1000.0, 'pending');

        $controller = app(PassengersController::class);

        // استدعاء PassengersController::update
        $updateRequest = Request::create('/passengers/' . $passenger1->id, 'PUT', [
            'date'              => now()->toDateString(),
            'passenger_number'  => $passenger1->passenger_number,
            'pickup_location'   => 'صنعاء',
            'destination'       => 'عدن',
            'count'             => 1,
            'office_commission' => 1000.0,
        ]);
        $controller->update($updateRequest, $passenger1->id);

        // استدعاء PassengersController::updateStatus لتحويله إلى completed
        $statusRequest = Request::create('/passengers/' . $passenger1->id . '/status', 'POST', [
            'status' => 'completed',
        ]);
        $statusRequest->headers->set('referer', '/passengers');
        $controller->updateStatus($statusRequest, $passenger1->id);

        // التحقق من عدم وجود أي قيد مالي مسجل باسم Passengers::class في cash_transactions
        $individualCount = CashTransaction::withoutGlobalScopes()
            ->where('source_type', Passengers::class)
            ->where('source_id', $passenger1->id)
            ->count();
        $this->assertEquals(0, $individualCount, 'يجب عدم تسجيل أي قيد مالي فردي للراكب عند تغيير حالته إلى completed.');

        // إضافة هذا الراكب مع راكب آخر (عمولته 1500) في رحلة عبر PassengerTripController::store
        $passenger2 = $this->createPassenger(1500.0, 'pending');

        $tripController = app(PassengerTripController::class);
        $tripStoreRequest = Request::create('/trips', 'POST', [
            'driver_id'     => $this->driver->id,
            'passenger_ids' => [$passenger1->id, $passenger2->id],
        ]);
        $tripController->store($tripStoreRequest);

        // جلب الرحلة المنشأة
        $trip = PassengerTrip::where('driver_id', $this->driver->id)->latest('id')->first();
        $this->assertNotNull($trip, 'يجب إنشاء الرحلة بنجاح.');

        // التحقق من تسجيل قيد مالي واحد فقط مرتبط بـ PassengerTrip::class بقيمة 2500
        $tripTransactions = CashTransaction::withoutGlobalScopes()
            ->where('source_type', PassengerTrip::class)
            ->where('source_id', $trip->id)
            ->get();

        $this->assertCount(1, $tripTransactions, 'يجب وجود قيد مالي واحد فقط مرتبط بالرحلة.');
        $transaction = $tripTransactions->first();
        $this->assertEquals(2500.00, (float) $transaction->amount, 'يجب أن تكون قيمة القيد 2500 (مجموع عمولة الراكبين).');

        // التحقق من أن التصنيف هو "عمولة ركاب"
        $category = CashCategory::withoutGlobalScopes()->find($transaction->cash_category_id);
        $this->assertNotNull($category);
        $this->assertEquals('عمولة ركاب', $category->name, 'يجب أن يكون تصنيف قيد الرحلة هو عمولة ركاب.');
    }

    // =========================================================================
    // 2. اختبار دقة تصنيف عمولة الشحنة والـ Fallback (الإصلاح #2)
    // =========================================================================
    public function test_02_accurate_shipment_commission_category_fallback(): void
    {
        // إنشاء تطبيق وفرع مخصصين لهذا الاختبار لضمان عدم وجود تصنيف "عمولة شحنة" مسبقاً
        $isolatedApp = App::create([
            'name'      => 'Isolated App ' . rand(1000, 9999),
            'is_active' => true,
        ]);

        $isolatedBranch = Branch::create([
            'app_id'  => $isolatedApp->id,
            'name'    => 'فرع العزل',
            'code'    => 'ISO_' . rand(100, 999),
            'city'    => 'صنعاء',
            'address' => 'شارع الستين',
            'phone'   => '967722334455',
        ]);

        // إنشاء تصنيف "عمولة ركاب" كأول تصنيف income موجود للتطبيق الجديد
        $passengerCategory = CashCategory::withoutGlobalScopes()->create([
            'app_id'    => $isolatedApp->id,
            'name'      => 'عمولة ركاب',
            'type'      => 'income',
            'is_active' => true,
        ]);

        // التأكد من عدم وجود أي تصنيف باسم "عمولة شحنة" لهذا التطبيق
        $this->assertNull(
            CashCategory::withoutGlobalScopes()
                ->where('app_id', $isolatedApp->id)
                ->where('name', 'عمولة شحنة')
                ->first()
        );

        // إنشاء حمولة وطرد للتطبيق الجديد بعمولة 500
        $package = ShipmentPackage::create([
            'app_id'           => $isolatedApp->id,
            'created_by'       => $this->user->id,
            'sender_branch_id' => $isolatedBranch->id,
            'driver_id'        => $this->driver->id,
            'status'           => 'pending',
        ]);

        $shipment = Shipment::create([
            'bond_number'             => 'BOND-' . Str::upper(Str::random(8)),
            'sender_branch_id'        => $isolatedBranch->id,
            'receiver_branch_id'      => $isolatedBranch->id,
            'created_by'              => $this->user->id,
            'package_type'            => 'parcel',
            'package_fee'             => 500.0,
            'package_commission_rate' => 100.0,
            'package_commission_amount' => 500.0,
            'total_commission'        => 500.0,
            'total_amount'            => 500.0,
            'payment_method'          => 'prepaid',
            'status'                  => 'pending',
            'shipment_package_id'     => $package->id,
        ]);

        // استدعاء recordPackageCommission
        $transaction = $this->cashService->recordPackageCommission($package);

        $this->assertNotNull($transaction, 'يجب إنشاء قيد مالي للحمولة.');

        // التحقق من أن النظام قام بإنشاء تصنيف جديد باسم "عمولة شحنة"
        $shipmentCategory = CashCategory::withoutGlobalScopes()
            ->where('app_id', $isolatedApp->id)
            ->where('name', 'عمولة شحنة')
            ->first();

        $this->assertNotNull($shipmentCategory, 'يجب إنشاء تصنيف عمولة شحنة تلقائياً للتطبيق.');
        $this->assertEquals($shipmentCategory->id, $transaction->cash_category_id, 'يجب ربط القيد بتصنيف عمولة شحنة.');
        $this->assertNotEquals($passengerCategory->id, $transaction->cash_category_id, 'يجب عدم استخدام تصنيف عمولة ركاب بالخطأ كـ Fallback.');
    }

    // =========================================================================
    // 3. اختبار تسقيط العمولة في المسار القديم لإنشاء الحمولة (الإصلاح #3)
    // =========================================================================
    public function test_03_old_store_method_records_package_commission_immediately(): void
    {
        // إنشاء طردين بعمولة 500 لكل طرد (الإجمالي 1000)
        $shipment1 = $this->createShipment(500.0, 'pending');
        $shipment2 = $this->createShipment(500.0, 'pending');

        $packageController = app(ShipmentPackagesController::class);

        // استدعاء المسار القديم ShipmentPackagesController::store
        $storeRequest = Request::create('/shipmentpackage', 'POST', [
            'driver_name'  => 'سائق المسار القديم',
            'driver_phone' => '967733445566',
            'selected_ids' => [$shipment1->id, $shipment2->id],
        ]);

        $response = $packageController->store($storeRequest);

        // التحقق من تحديث حالة الطرود إلى in_transit وربطها بالحمولة
        $shipment1->refresh();
        $shipment2->refresh();
        $this->assertEquals('in_transit', $shipment1->status);
        $this->assertEquals('in_transit', $shipment2->status);
        $this->assertNotNull($shipment1->shipment_package_id);

        $packageId = $shipment1->shipment_package_id;

        // التحقق من نزول قيد العمولة فوراً في cash_transactions بقيمة 1000
        $transaction = CashTransaction::withoutGlobalScopes()
            ->where('source_type', ShipmentPackage::class)
            ->where('source_id', $packageId)
            ->where('type', 'income')
            ->first();

        $this->assertNotNull($transaction, 'يجب نزول قيد العمولة فوراً عند إنشاء الحمولة عبر store القديم.');
        $this->assertEquals(1000.00, (float) $transaction->amount, 'يجب أن تكون قيمة القيد 1000 (مجموع عمولتي الطردين).');

        $category = CashCategory::withoutGlobalScopes()->find($transaction->cash_category_id);
        $this->assertEquals('عمولة شحنة', $category->name, 'يجب أن يكون التصنيف هو عمولة شحنة.');
    }

    // =========================================================================
    // 4. اختبار تعديل عمولة الرحلة عند إضافة أو إزالة ركاب (الإصلاح #4)
    // =========================================================================
    public function test_04_adjust_trip_commission_on_passenger_add_remove_and_update(): void
    {
        // إنشاء رحلة بركاب إجمالي عمولتهم 2500 (راكب 1000 وراكب 1500)
        $p1 = $this->createPassenger(1000.0, 'pending');
        $p2 = $this->createPassenger(1500.0, 'pending');

        $trip = PassengerTrip::create([
            'app_id'     => $this->appModel->id,
            'branch_id'  => $this->branch->id,
            'created_by' => $this->user->id,
            'driver_id'  => $this->driver->id,
        ]);

        $p1->update(['trip_id' => $trip->id, 'status' => 'completed']);
        $p2->update(['trip_id' => $trip->id, 'status' => 'completed']);

        // تسجيل القيد الأولي بقيمة 2500
        $this->cashService->recordTripCommission($trip);

        $transaction = CashTransaction::withoutGlobalScopes()
            ->where('source_type', PassengerTrip::class)
            ->where('source_id', $trip->id)
            ->first();
        $this->assertEquals(2500.00, (float) $transaction->amount);

        $tripController = app(PassengerTripController::class);

        // 4.1 اختبار addPassenger: إضافة راكب بعمولة 500 -> الصافي 3000
        $p3 = $this->createPassenger(500.0, 'pending');
        $addRequest = Request::create("/trips/{$trip->id}/passengers/add", 'POST', [
            'passenger_id' => $p3->id,
        ]);
        $tripController->addPassenger($addRequest, $trip->id);

        $transaction->refresh();
        $this->assertEquals(3000.00, (float) $transaction->amount, 'يجب تحديث القيد ليصبح 3000 بعد إضافة راكب.');

        // 4.2 اختبار removePassenger: إزالة راكب عمولته 1000 -> الصافي 2000
        $tripController->removePassenger($trip->id, $p1->id);

        $transaction->refresh();
        $this->assertEquals(2000.00, (float) $transaction->amount, 'يجب تحديث القيد ليصبح 2000 بعد إزالة راكب بقيمة 1000.');

        // 4.3 اختبار PassengerTripController::update بتغيير قائمة الركاب
        // سنبقي فقط الراكب 2 (عمولته 1500) ونستبعد الباقي
        $updateTripRequest = Request::create("/trips/{$trip->id}", 'PUT', [
            'driver_id'     => $this->driver->id,
            'passenger_ids' => [$p2->id],
        ]);
        $tripController->update($updateTripRequest, $trip->id);

        $transaction->refresh();
        $this->assertEquals(1500.00, (float) $transaction->amount, 'يجب تحديث القيد المالي بدقة ليصبح 1500 بعد تعديل قائمة الركاب عبر update.');
    }

    // =========================================================================
    // 5. اختبار تعديل عمولة الحمولة عند التعديل أو الإرجاع (الإصلاح #5 وباقي المسارات)
    // =========================================================================
    public function test_05_adjust_package_commission_on_unlink_remove_add_and_returned(): void
    {
        $packageController = app(ShipmentPackagesController::class);

        // إنشاء حمولة in_transit بها 3 طرود (500 لكل طرد = 1500)
        $package = $this->createPackage('in_transit');
        $s1 = $this->createShipment(500.0, 'in_transit', $package->id);
        $s2 = $this->createShipment(500.0, 'in_transit', $package->id);
        $s3 = $this->createShipment(500.0, 'in_transit', $package->id);

        // تسجيل قيد الحمولة الأولي
        $this->cashService->recordPackageCommission($package);

        $transaction = CashTransaction::withoutGlobalScopes()
            ->where('source_type', ShipmentPackage::class)
            ->where('source_id', $package->id)
            ->first();
        $this->assertNotNull($transaction);
        $this->assertEquals(1500.00, (float) $transaction->amount);

        // 5.1 اختبار unlinkFromPackage لفك ارتباط طرد واحد (s1) -> يصبح القيد 1000
        $unlinkRequest = Request::create("/shipment-packages/{$s1->id}/unlink", 'PATCH');
        $packageController->unlinkFromPackage($unlinkRequest, $s1->id);

        $transaction->refresh();
        $this->assertEquals(1000.00, (float) $transaction->amount, 'يجب انخفاض القيد المالي إلى 1000 بعد فك ارتباط طرد عبر unlinkFromPackage.');

        // 5.2 اختبار removeShipment لإخراج طرد آخر (s2) -> يصبح القيد 500
        $removeRequest = Request::create("/shipmentpackage/{$package->id}/remove-shipment/{$s2->id}", 'POST');
        $removeRequest->headers->set('referer', '/packages');
        $packageController->removeShipment($removeRequest, $package->id, $s2->id);

        $transaction->refresh();
        $this->assertEquals(500.00, (float) $transaction->amount, 'يجب انخفاض القيد المالي إلى 500 بعد إخراج طرد عبر removeShipment.');

        // 5.3 اختبار addShipment لإعادة إضافة طرد (s2) -> يعود القيد إلى 1000
        $addRequest = Request::create("/shipmentpackage/add-shipment/{$package->id}", 'POST', [
            'shipment_id' => $s2->id,
        ]);
        $addRequest->headers->set('referer', '/packages');
        $packageController->addShipment($addRequest, $package->id);

        $transaction->refresh();
        $this->assertEquals(1000.00, (float) $transaction->amount, 'يجب ارتفاع القيد المالي إلى 1000 بعد إعادة إضافة الطرد عبر addShipment.');

        // 5.4 اختبار تغيير حالة طرد واحد إلى مرتجع (returned) -> خصم عمولته من الحمولة
        // تحويل s2 إلى مرتجع وفك ارتباطه
        $s2->update(['status' => 'pending', 'shipment_package_id' => null, 'is_returned' => true]);
        $this->cashService->adjustPackageCommission($package);

        $transaction->refresh();
        $this->assertEquals(500.00, (float) $transaction->amount, 'يجب خصم عمولة الطرد المرتجع من قيد عمولة الحمولة ليصبح 500.');

        // 5.5 اختبار تحويل الحمولة بالكامل إلى returned -> حذف القيد من الدفتر تماماً
        $statusRequest = Request::create("/shipmentpackage/outgoing/updateStatus/{$package->id}", 'POST', [
            'status' => 'returned',
        ]);
        $statusRequest->headers->set('referer', '/packages');
        $packageController->updateStatus($statusRequest, $package->id);

        $deletedTransactionExists = CashTransaction::withoutGlobalScopes()
            ->where('source_type', ShipmentPackage::class)
            ->where('source_id', $package->id)
            ->where('notes', 'like', '%عمولة الحمولة%')
            ->exists();

        $this->assertFalse($deletedTransactionExists, 'يجب حذف قيد عمولة الحمولة من الدفتر المالي تماماً عند تحويل الحمولة إلى returned.');
    }

    // =========================================================================
    // 6. اختبار الحالات الحدية (Edge Cases & Idempotency)
    // =========================================================================
    public function test_06_edge_cases_idempotency_and_zero_commission(): void
    {
        // 6.1 Idempotency لعمولة الرحلة: استدعاء recordTripCommission مرتين متتاليتين
        $p1 = $this->createPassenger(1200.0, 'completed');
        $trip = PassengerTrip::create([
            'app_id'     => $this->appModel->id,
            'branch_id'  => $this->branch->id,
            'created_by' => $this->user->id,
            'driver_id'  => $this->driver->id,
        ]);
        $p1->update(['trip_id' => $trip->id]);

        $call1 = $this->cashService->recordTripCommission($trip);
        $call2 = $this->cashService->recordTripCommission($trip);

        $this->assertNotNull($call1, 'الاستدعاء الأول يجب أن ينشئ القيد.');
        $this->assertNull($call2, 'الاستدعاء الثاني يجب أن يُرجع null لمنع التكرار (Idempotency).');

        $tripCount = CashTransaction::withoutGlobalScopes()
            ->where('source_type', PassengerTrip::class)
            ->where('source_id', $trip->id)
            ->count();
        $this->assertEquals(1, $tripCount, 'يجب عدم تكرار قيد عمولة الرحلة.');

        // 6.2 Idempotency لعمولة الحمولة: استدعاء recordPackageCommission مرتين متتاليتين
        $package = $this->createPackage('in_transit');
        $this->createShipment(800.0, 'in_transit', $package->id);

        $pkgCall1 = $this->cashService->recordPackageCommission($package);
        $pkgCall2 = $this->cashService->recordPackageCommission($package);

        $this->assertNotNull($pkgCall1, 'الاستدعاء الأول يجب أن ينشئ قيد الحمولة.');
        $this->assertNull($pkgCall2, 'الاستدعاء الثاني يجب أن يُرجع null لمنع التكرار.');

        $pkgCount = CashTransaction::withoutGlobalScopes()
            ->where('source_type', ShipmentPackage::class)
            ->where('source_id', $package->id)
            ->count();
        $this->assertEquals(1, $pkgCount, 'يجب عدم تكرار قيد عمولة الحمولة.');

        // 6.3 العمولة الصفرية لرحلة الركاب: عمولة = 0 -> تعامل آمن بدون Crash وبدون إنشاء قيد
        $pZero = $this->createPassenger(0.0, 'completed');
        $zeroTrip = PassengerTrip::create([
            'app_id'     => $this->appModel->id,
            'branch_id'  => $this->branch->id,
            'created_by' => $this->user->id,
            'driver_id'  => $this->driver->id,
        ]);
        $pZero->update(['trip_id' => $zeroTrip->id]);

        $zeroTripResult = $this->cashService->recordTripCommission($zeroTrip);
        $this->assertNull($zeroTripResult, 'يجب أن تُرجع الدالة null عند كون العمولة 0.');

        $zeroTripCount = CashTransaction::withoutGlobalScopes()
            ->where('source_type', PassengerTrip::class)
            ->where('source_id', $zeroTrip->id)
            ->count();
        $this->assertEquals(0, $zeroTripCount, 'يجب عدم إنشاء أي قيد مالي برقم 0 للرحلة.');

        // 6.4 العمولة الصفرية للحمولة: عمولة = 0 -> تعامل آمن بدون Crash وبدون إنشاء قيد
        $zeroPackage = $this->createPackage('in_transit');
        $this->createShipment(0.0, 'in_transit', $zeroPackage->id);

        $zeroPkgResult = $this->cashService->recordPackageCommission($zeroPackage);
        $this->assertNull($zeroPkgResult, 'يجب أن تُرجع الدالة null عند كون عمولة الحمولة 0.');

        $zeroPkgCount = CashTransaction::withoutGlobalScopes()
            ->where('source_type', ShipmentPackage::class)
            ->where('source_id', $zeroPackage->id)
            ->count();
        $this->assertEquals(0, $zeroPkgCount, 'يجب عدم إنشاء أي قيد مالي برقم 0 للحمولة.');
    }

    // =========================================================================
    // 7. اختبار استعادة وإنشاء القيد عند إعادة إضافة عناصر بعد تفريغ الحمولة أو الرحلة
    // =========================================================================
    public function test_07_recreate_commission_when_items_readded_to_emptied_package_and_trip(): void
    {
        $packageController = app(ShipmentPackagesController::class);
        $tripController = app(PassengerTripController::class);

        // -------------------------------------------------------------
        // أولاً: اختبار الحمولة (ShipmentPackage)
        // -------------------------------------------------------------
        // 1. حمولة in_transit بها طرد واحد فقط بعمولة 600
        $package = $this->createPackage('in_transit');
        $s1 = $this->createShipment(600.0, 'in_transit', $package->id);

        $this->cashService->recordPackageCommission($package);

        // التأكد من وجود القيد المالي الأولي بقيمة 600
        $pkgTx = CashTransaction::withoutGlobalScopes()
            ->where('source_type', ShipmentPackage::class)
            ->where('source_id', $package->id)
            ->first();
        $this->assertNotNull($pkgTx);
        $this->assertEquals(600.00, (float) $pkgTx->amount);

        // 2. فك ارتباط الطرد الوحيد ليصبح إجمالي عمولة الحمولة = 0
        $removeRequest = Request::create("/shipmentpackage/{$package->id}/remove-shipment/{$s1->id}", 'POST');
        $removeRequest->headers->set('referer', '/packages');
        $packageController->removeShipment($removeRequest, $package->id, $s1->id);

        // التأكد من حذف القيد المالي تماماً من دفتر الحسابات
        $pkgTxExists = CashTransaction::withoutGlobalScopes()
            ->where('source_type', ShipmentPackage::class)
            ->where('source_id', $package->id)
            ->exists();
        $this->assertFalse($pkgTxExists, 'يجب حذف القيد المالي عند تفريغ الحمولة بالكامل ووصول العمولة إلى 0.');

        // 3. إعادة إضافة طردين جديدين للحمولة (طرد 400 وطرد 700)
        $s2 = $this->createShipment(400.0, 'pending');
        $s3 = $this->createShipment(700.0, 'pending');

        // إضافة الطرد الأول (400)
        $addRequest1 = Request::create("/shipmentpackage/add-shipment/{$package->id}", 'POST', [
            'shipment_id' => $s2->id,
        ]);
        $addRequest1->headers->set('referer', '/packages');
        $packageController->addShipment($addRequest1, $package->id);

        // التأكد من عودة ظهور القيد المالي في الدفتر بقيمة 400
        $recreatedTx1 = CashTransaction::withoutGlobalScopes()
            ->where('source_type', ShipmentPackage::class)
            ->where('source_id', $package->id)
            ->first();
        $this->assertNotNull($recreatedTx1, 'يجب إعادة إنشاء القيد المالي للحمولة عند إضافة طرد جديد بعد أن كان محذوفاً.');
        $this->assertEquals(400.00, (float) $recreatedTx1->amount, 'يجب أن تظهر العمولة الجديدة 400.');

        // إضافة الطرد الثاني (700) -> يصبح الإجمالي 1100
        $addRequest2 = Request::create("/shipmentpackage/add-shipment/{$package->id}", 'POST', [
            'shipment_id' => $s3->id,
        ]);
        $addRequest2->headers->set('referer', '/packages');
        $packageController->addShipment($addRequest2, $package->id);

        $recreatedTx1->refresh();
        $this->assertEquals(1100.00, (float) $recreatedTx1->amount, 'يجب تحديث القيد المالي ليصبح 1100 بعد إضافة الطرد الثاني.');

        // -------------------------------------------------------------
        // ثانياً: اختبار رحلة الركاب (PassengerTrip)
        // -------------------------------------------------------------
        // 1. رحلة بها راكب واحد فقط بعمولة 800
        $p1 = $this->createPassenger(800.0, 'pending');
        $trip = PassengerTrip::create([
            'app_id'     => $this->appModel->id,
            'branch_id'  => $this->branch->id,
            'created_by' => $this->user->id,
            'driver_id'  => $this->driver->id,
        ]);
        $p1->update(['trip_id' => $trip->id, 'status' => 'completed']);

        $this->cashService->recordTripCommission($trip);

        // التأكد من وجود القيد المالي الأولي للرحلة بقيمة 800
        $tripTx = CashTransaction::withoutGlobalScopes()
            ->where('source_type', PassengerTrip::class)
            ->where('source_id', $trip->id)
            ->first();
        $this->assertNotNull($tripTx);
        $this->assertEquals(800.00, (float) $tripTx->amount);

        // 2. إزالة الراكب الوحيد ليصبح إجمالي عمولة الرحلة = 0
        $tripController->removePassenger($trip->id, $p1->id);

        // التأكد من حذف قيد الرحلة المالي تماماً من الدفتر
        $tripTxExists = CashTransaction::withoutGlobalScopes()
            ->where('source_type', PassengerTrip::class)
            ->where('source_id', $trip->id)
            ->exists();
        $this->assertFalse($tripTxExists, 'يجب حذف القيد المالي عند إزالة جميع ركاب الرحلة ووصول العمولة إلى 0.');

        // 3. إعادة إضافة راكبين جديدين للرحلة (راكب 500 وراكب 900)
        $p2 = $this->createPassenger(500.0, 'pending');
        $p3 = $this->createPassenger(900.0, 'pending');

        // إضافة الراكب الأول (500)
        $addPassengerReq1 = Request::create("/trips/{$trip->id}/passengers/add", 'POST', [
            'passenger_id' => $p2->id,
        ]);
        $tripController->addPassenger($addPassengerReq1, $trip->id);

        // التأكد من عودة ظهور القيد المالي للرحلة بقيمة 500
        $recreatedTripTx = CashTransaction::withoutGlobalScopes()
            ->where('source_type', PassengerTrip::class)
            ->where('source_id', $trip->id)
            ->first();
        $this->assertNotNull($recreatedTripTx, 'يجب إعادة إنشاء قيد الرحلة عند إضافة راكب جديد بعد أن كان محذوفاً.');
        $this->assertEquals(500.00, (float) $recreatedTripTx->amount, 'يجب أن تظهر عمولة الرحلة الجديدة 500.');

        // إضافة الراكب الثاني (900) -> يصبح الإجمالي 1400
        $addPassengerReq2 = Request::create("/trips/{$trip->id}/passengers/add", 'POST', [
            'passenger_id' => $p3->id,
        ]);
        $tripController->addPassenger($addPassengerReq2, $trip->id);

        $recreatedTripTx->refresh();
        $this->assertEquals(1400.00, (float) $recreatedTripTx->amount, 'يجب تحديث قيد الرحلة المالي ليصبح 1400 بعد إضافة الراكب الثاني.');
    }
}
