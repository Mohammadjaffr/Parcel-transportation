<?php

namespace Tests\Feature;

use App\Models\App;
use App\Models\Branch;
use App\Models\Package;
use App\Models\Shipment;
use App\Models\Subscription;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected App $appModel;
    protected App $otherAppModel;
    protected User $adminUser;
    protected User $otherAdminUser;
    protected User $regularUser;
    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. إنشاء باقة تجريبية
        $package = Package::firstOrCreate(
            ['name' => 'الباقة الأساسية'],
            [
                'price'            => 0,
                'duration_in_days' => 30,
                'max_branches'     => 5,
                'max_drivers'      => 5,
                'max_shipments'    => 100,
                'max_packages'     => 100,
            ]
        );

        // 2. إنشاء التطبيق / المكتب الأول
        $this->appModel = App::create([
            'name'      => 'شركة الأمان للشحن',
            'is_active' => true,
        ]);

        $subscription = Subscription::create([
            'app_id'            => $this->appModel->id,
            'package_id'        => $package->id,
            'price_paid'        => 0,
            'allowed_branches'  => 5,
            'allowed_drivers'   => 5,
            'allowed_shipments' => 100,
            'allowed_packages'  => 100,
            'status'            => 'active',
            'starts_at'         => now()->subDay(),
            'ends_at'           => now()->addDays(20),
        ]);

        $this->appModel->update([
            'current_subscription_id' => $subscription->id,
            'is_active'               => true,
        ]);

        $this->branch = Branch::create([
            'app_id'  => $this->appModel->id,
            'name'    => 'فرع صنعاء الرئيسي',
            'code'    => 'SAN01',
            'city'    => 'صنعاء',
            'address' => 'شارع حدة',
            'phone'   => '771234567',
            'is_main' => true,
        ]);

        // 3. إنشاء التطبيق / المكتب الثاني للتحقق من العزل
        $this->otherAppModel = App::create([
            'name'      => 'شركة النجم للنقل',
            'is_active' => true,
        ]);

        $subscription2 = Subscription::create([
            'app_id'            => $this->otherAppModel->id,
            'package_id'        => $package->id,
            'price_paid'        => 0,
            'allowed_branches'  => 5,
            'allowed_drivers'   => 5,
            'allowed_shipments' => 100,
            'allowed_packages'  => 100,
            'status'            => 'active',
            'starts_at'         => now()->subDay(),
            'ends_at'           => now()->addDays(20),
        ]);

        $this->otherAppModel->update([
            'current_subscription_id' => $subscription2->id,
            'is_active'               => true,
        ]);

        $otherBranch = Branch::create([
            'app_id'  => $this->otherAppModel->id,
            'name'    => 'فرع عدن',
            'code'    => 'ADN01',
            'city'    => 'عدن',
            'address' => 'المعلا',
            'phone'   => '772223334',
            'is_main' => true,
        ]);

        // 4. إنشاء المستخدمين
        $this->adminUser = User::create([
            'name'              => 'مدير شركة الأمان',
            'phone'             => '967771112233',
            'password'          => bcrypt('password'),
            'type'              => 'admin',
            'app_id'            => $this->appModel->id,
            'branch_id'         => $this->branch->id,
            'is_phone_verified' => true,
        ]);

        $this->otherAdminUser = User::create([
            'name'              => 'مدير شركة النجم',
            'phone'             => '967779998877',
            'password'          => bcrypt('password'),
            'type'              => 'admin',
            'app_id'            => $this->otherAppModel->id,
            'branch_id'         => $otherBranch->id,
            'is_phone_verified' => true,
        ]);

        $this->regularUser = User::create([
            'name'              => 'موظف عادي',
            'phone'             => '967774445566',
            'password'          => bcrypt('password'),
            'type'              => 'user',
            'app_id'            => $this->appModel->id,
            'branch_id'         => $this->branch->id,
            'is_phone_verified' => true,
        ]);
    }

    /**
     * 1. فتح صفحة إعدادات WhatsApp للمدير
     */
    public function test_admin_can_open_whatsapp_settings_page(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('app.whatsapp.settings'));

        $response->assertStatus(200);
        $response->assertSee('إعدادات تكامل الواتساب (WhatsApp)');
        $response->assertSee('شركة الأمان للشحن');
    }

    /**
     * 2. منع المستخدم غير المصرح له (الموظف العادي) من فتح إعدادات WhatsApp
     */
    public function test_regular_user_cannot_access_whatsapp_settings(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('app.whatsapp.settings'));

        $response->assertStatus(403);
    }

    /**
     * 3. حفظ وتحديث إعدادات WhatsApp للمكتب
     */
    public function test_admin_can_save_whatsapp_settings(): void
    {
        $response = $this->actingAs($this->adminUser)->put(route('app.whatsapp.settings.update'), [
            'whatsapp_base_url'       => 'https://api.mywhatsapp.com',
            'whatsapp_instance_id'    => 'aman_instance_01',
            'whatsapp_instance_token' => 'super_secret_token_12345678',
            'whatsapp_webhook_url'    => 'https://aman.com/api/webhook',
            'whatsapp_timeout'        => 45,
        ]);

        $response->assertRedirect(route('app.whatsapp.settings'));

        $this->appModel->refresh();
        $this->assertEquals('https://api.mywhatsapp.com', $this->appModel->whatsapp_base_url);
        $this->assertEquals('aman_instance_01', $this->appModel->whatsapp_instance_id);
        $this->assertEquals('super_secret_token_12345678', $this->appModel->whatsapp_instance_token);
        $this->assertEquals('https://aman.com/api/webhook', $this->appModel->whatsapp_webhook_url);
        $this->assertEquals(45, $this->appModel->whatsapp_timeout);
        $this->assertTrue($this->appModel->hasWhatsAppConfigured());
    }

    /**
     * 4. الاحتفاظ بالرمز السري السابق عند تركه فارغاً أو مقنعاً
     */
    public function test_blank_or_masked_token_does_not_overwrite_existing_token(): void
    {
        $this->appModel->update([
            'whatsapp_instance_id'    => 'existing_instance',
            'whatsapp_instance_token' => 'existing_token_secret_999',
            'whatsapp_timeout'        => 30,
        ]);

        // إرسال الحقل فارغاً
        $response = $this->actingAs($this->adminUser)->put(route('app.whatsapp.settings.update'), [
            'whatsapp_instance_id'    => 'updated_instance',
            'whatsapp_instance_token' => '',
        ]);

        $this->appModel->refresh();
        $this->assertEquals('existing_token_secret_999', $this->appModel->whatsapp_instance_token);
        $this->assertEquals('updated_instance', $this->appModel->whatsapp_instance_id);

        // إرسال الحقل مقنعاً بنجوم أو نقاط
        $response2 = $this->actingAs($this->adminUser)->put(route('app.whatsapp.settings.update'), [
            'whatsapp_instance_id'    => 'updated_instance_2',
            'whatsapp_instance_token' => '••••••••••••••••',
        ]);

        $this->appModel->refresh();
        $this->assertEquals('existing_token_secret_999', $this->appModel->whatsapp_instance_token);
        $this->assertEquals('updated_instance_2', $this->appModel->whatsapp_instance_id);
    }

    /**
     * 5. حماية الرمز السري وإظهاره مقنعاً في الواجهة
     */
    public function test_token_is_masked_in_view(): void
    {
        $this->appModel->update([
            'whatsapp_instance_id'    => 'inst_1',
            'whatsapp_instance_token' => 'my_super_secret_token_987654321',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('app.whatsapp.settings'));

        $response->assertStatus(200);
        $response->assertDontSee('my_super_secret_token_987654321');
        $response->assertSee($this->appModel->masked_whatsapp_token);
    }

    /**
     * 6. عزل إعدادات المكاتب ومنع تسربها
     */
    public function test_office_settings_isolation(): void
    {
        // تهيئة إعدادات مختلفة للمكتبين
        $this->appModel->update([
            'whatsapp_base_url'       => 'https://office1.example.com',
            'whatsapp_instance_id'    => 'office1_inst',
            'whatsapp_instance_token' => 'office1_token_123',
        ]);

        $this->otherAppModel->update([
            'whatsapp_base_url'       => 'https://office2.example.com',
            'whatsapp_instance_id'    => 'office2_inst',
            'whatsapp_instance_token' => 'office2_token_456',
        ]);

        /** @var WhatsAppService $service */
        $service = app(WhatsAppService::class);

        // تحميل خدمة المكتب الأول
        $serviceApp1 = $service->forApp($this->appModel);
        $this->assertEquals('https://office1.example.com', $serviceApp1->getBaseUrl());
        $this->assertEquals('office1_inst', $serviceApp1->getInstanceId());
        $this->assertEquals('office1_token_123', $serviceApp1->getToken());

        // تحميل خدمة المكتب الثاني
        $serviceApp2 = $service->forApp($this->otherAppModel);
        $this->assertEquals('https://office2.example.com', $serviceApp2->getBaseUrl());
        $this->assertEquals('office2_inst', $serviceApp2->getInstanceId());
        $this->assertEquals('office2_token_456', $serviceApp2->getToken());

        // التأكد من أن الكائن الأول لم يتأثر
        $this->assertEquals('office1_inst', $serviceApp1->getInstanceId());
    }

    /**
     * 7. فحص حالة الاتصال مع المزود بنجاح
     */
    public function test_check_connection_status_success(): void
    {
        $this->appModel->update([
            'whatsapp_base_url'       => 'https://wa.mock-server.com',
            'whatsapp_instance_id'    => 'mock_inst',
            'whatsapp_instance_token' => 'mock_token',
        ]);

        Http::fake([
            'https://wa.mock-server.com/instance/status' => Http::response([
                'state' => 'open',
                'instance' => ['status' => 'connected']
            ], 200),
        ]);

        $response = $this->actingAs($this->adminUser)->getJson(route('app.whatsapp.settings.status'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'state'   => 'open',
        ]);
    }

    /**
     * 8. فحص حالة الاتصال عند فشل المزود
     */
    public function test_check_connection_status_failure(): void
    {
        $this->appModel->update([
            'whatsapp_base_url'       => 'https://wa.mock-server.com',
            'whatsapp_instance_id'    => 'mock_inst',
            'whatsapp_instance_token' => 'mock_token',
        ]);

        Http::fake([
            'https://wa.mock-server.com/instance/status' => Http::response([
                'message' => 'Unauthorized Apikey',
            ], 401),
        ]);

        $failResponse = $this->actingAs($this->adminUser)->getJson(route('app.whatsapp.settings.status'));
        $failResponse->assertStatus(400);
        $failResponse->assertJson([
            'success' => false,
            'state'   => 'error',
        ]);
    }

    /**
     * 8. استخراج QR Code بنجاح
     */
    public function test_fetch_qr_code_success(): void
    {
        $this->appModel->update([
            'whatsapp_base_url'       => 'https://wa.mock-server.com',
            'whatsapp_instance_id'    => 'mock_inst',
            'whatsapp_instance_token' => 'mock_token',
        ]);

        Http::fake([
            'https://wa.mock-server.com/instance/qr' => Http::response([
                'base64' => 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            ], 200),
        ]);

        $response = $this->actingAs($this->adminUser)->getJson(route('app.whatsapp.settings.qr'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'qrcode'  => 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);
    }

    /**
     * 9. طلب كود الاقتران السريع برقم الهاتف
     */
    public function test_pairing_code_generation(): void
    {
        $this->appModel->update([
            'whatsapp_base_url'       => 'https://wa.mock-server.com',
            'whatsapp_instance_id'    => 'mock_inst',
            'whatsapp_instance_token' => 'mock_token',
        ]);

        Http::fake([
            'https://wa.mock-server.com/instance/pair' => Http::response([
                'code' => 'ABCD-1234',
            ], 200),
        ]);

        $response = $this->actingAs($this->adminUser)->postJson(route('app.whatsapp.settings.pair'), [
            'phone' => '771234567',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'code'    => 'ABCD-1234',
        ]);
    }

    /**
     * 10. تنفيذ أوامر الجلسة (Connect, Reconnect, Disconnect, Logout)
     */
    public function test_session_lifecycle_actions(): void
    {
        $this->appModel->update([
            'whatsapp_base_url'       => 'https://wa.mock-server.com',
            'whatsapp_instance_id'    => 'mock_inst',
            'whatsapp_instance_token' => 'mock_token',
        ]);

        Http::fake([
            'https://wa.mock-server.com/instance/connect'    => Http::response(['status' => 'connecting'], 200),
            'https://wa.mock-server.com/instance/reconnect'  => Http::response(['status' => 'reconnected'], 200),
            'https://wa.mock-server.com/instance/disconnect' => Http::response(['status' => 'disconnected'], 200),
            'https://wa.mock-server.com/instance/logout'     => Http::response(['status' => 'logged_out'], 200),
        ]);

        // Connect
        $connRes = $this->actingAs($this->adminUser)->postJson(route('app.whatsapp.settings.connect'));
        $connRes->assertStatus(200)->assertJson(['success' => true]);

        // Reconnect
        $recRes = $this->actingAs($this->adminUser)->postJson(route('app.whatsapp.settings.reconnect'));
        $recRes->assertStatus(200)->assertJson(['success' => true]);

        // Disconnect
        $disRes = $this->actingAs($this->adminUser)->postJson(route('app.whatsapp.settings.disconnect'));
        $disRes->assertStatus(200)->assertJson(['success' => true]);

        // Logout
        $logRes = $this->actingAs($this->adminUser)->postJson(route('app.whatsapp.settings.logout'));
        $logRes->assertStatus(200)->assertJson(['success' => true]);
    }

    /**
     * 12. إرسال رسالة تجريبية بنجاح
     */
    public function test_send_test_message_success(): void
    {
        $this->appModel->update([
            'whatsapp_base_url'       => 'https://wa.mock-server.com',
            'whatsapp_instance_id'    => 'mock_inst',
            'whatsapp_instance_token' => 'mock_token',
        ]);

        Http::fake([
            'https://wa.mock-server.com/send/text' => Http::response([
                'key' => ['id' => 'MSG_12345'],
                'message' => 'Sent successfully'
            ], 200),
        ]);

        $successRes = $this->actingAs($this->adminUser)->postJson(route('app.whatsapp.settings.test'), [
            'recipient' => '771234567',
            'message'   => 'رسالة تجريبية للتحقق من الاتصال',
        ]);

        $successRes->assertStatus(200);
        $successRes->assertJson(['success' => true]);
    }

    /**
     * 13. معالجة أخطاء إرسال الرسالة التجريبية من المزود
     */
    public function test_send_test_message_failure(): void
    {
        $this->appModel->update([
            'whatsapp_base_url'       => 'https://wa.mock-server.com',
            'whatsapp_instance_id'    => 'mock_inst',
            'whatsapp_instance_token' => 'mock_token',
        ]);

        Http::fake([
            'https://wa.mock-server.com/send/text' => Http::response([
                'error' => 'Connection closed or instance not ready',
            ], 400),
        ]);

        $failRes = $this->actingAs($this->adminUser)->postJson(route('app.whatsapp.settings.test'), [
            'recipient' => '771234567',
            'message'   => 'رسالة تجريبية أخرى',
        ]);

        $failRes->assertStatus(400);
        $failRes->assertJson(['success' => false]);
    }

    /**
     * 12. التحقق من تنسيق الأرقام المحلية والدولية دون تكرار
     */
    public function test_phone_number_formatting(): void
    {
        /** @var WhatsAppService $service */
        $service = app(WhatsAppService::class);

        // رقم يمني يبدأ بـ 0
        $this->assertEquals('967771234567', $service->formatNumber('0771234567'));

        // رقم يمني بدون بادئة
        $this->assertEquals('967771234567', $service->formatNumber('771234567'));

        // رقم يمني به تكرار المفتاح 967967
        $this->assertEquals('967771234567', $service->formatNumber('967967771234567'));

        // رقم سعودي به مفتاح دولي
        $this->assertEquals('966512345678', $service->formatNumber('+966512345678'));

        // رقم يبدأ بـ 00
        $this->assertEquals('967771234567', $service->formatNumber('00967771234567'));
    }

    /**
     * 13. استمرار الوظائف السابقة ودوال توليد الروابط
     */
    public function test_legacy_whatsapp_link_generation(): void
    {
        /** @var WhatsAppService $service */
        $service = app(WhatsAppService::class);

        $link = $service->createWhatsAppLink('771234567', 'مرحباً بكم');
        $this->assertNotNull($link);
        $this->assertStringContainsString('https://api.whatsapp.com/send', $link);
        $this->assertStringContainsString('967771234567', $link);
        $this->assertStringContainsString(urlencode('مرحباً بكم'), $link);
    }

    /**
     * 14. فتح صفحة إعدادات WhatsApp عبر الموبايل
     */
    public function test_mobile_whatsapp_settings_view(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148',
            ])
            ->get(route('app.whatsapp.settings'));

        $response->assertStatus(200);
        $response->assertSee('إعدادات WhatsApp');
        $response->assertSee('شركة الأمان للشحن');
    }
}
