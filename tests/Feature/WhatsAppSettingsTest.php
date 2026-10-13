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
    protected User $branchAdminUser;
    protected Branch $branch;
    protected Branch $otherBranch;

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

        $this->otherBranch = Branch::create([
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
            'branch_id'         => $this->otherBranch->id,
            'is_phone_verified' => true,
        ]);

        $this->regularUser = User::create([
            'name'              => 'موظف عادي',
            'phone'             => '967774445566',
            'password'          => bcrypt('password'),
            'type'              => 'user',
            'is_branch_admin'   => false,
            'app_id'            => $this->appModel->id,
            'branch_id'         => $this->branch->id,
            'is_phone_verified' => true,
        ]);

        $this->branchAdminUser = User::create([
            'name'              => 'مدير فرع صنعاء',
            'phone'             => '967775556677',
            'password'          => bcrypt('password'),
            'type'              => 'user',
            'is_branch_admin'   => true,
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
        $response->assertSee('إعدادات الواتساب (WhatsApp)');
        $response->assertSee('فرع صنعاء الرئيسي');
    }

    /**
     * 2. فتح صفحة إعدادات WhatsApp لمدير الفرع (is_branch_admin)
     */
    public function test_branch_admin_can_open_whatsapp_settings_page(): void
    {
        $response = $this->actingAs($this->branchAdminUser)->get(route('app.whatsapp.settings'));

        $response->assertStatus(200);
        $response->assertSee('إعدادات الواتساب (WhatsApp)');
        $response->assertSee('فرع صنعاء الرئيسي');
    }

    /**
     * 3. منع المستخدم غير المصرح له (الموظف العادي ليس مدير فرع) من فتح إعدادات WhatsApp
     */
    public function test_regular_user_cannot_access_whatsapp_settings(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('app.whatsapp.settings'));

        $response->assertStatus(403);
    }

    /**
     * 4. مدير الفرع يمكنه حفظ إعدادات الواتساب لفرعه
     */
    public function test_branch_admin_can_save_whatsapp_settings_for_their_branch(): void
    {
        $response = $this->actingAs($this->branchAdminUser)->put(route('app.whatsapp.settings.update'), [
            'whatsapp_base_url'       => 'https://api.branchwa.com',
            'whatsapp_instance_id'    => 'branch_instance_01',
            'whatsapp_instance_token' => 'branch_secret_token_123',
            'whatsapp_timeout'        => 25,
        ]);

        $response->assertRedirect(route('app.whatsapp.settings'));

        $this->branch->refresh();
        $this->assertEquals('https://api.branchwa.com', $this->branch->whatsapp_base_url);
        $this->assertEquals('branch_instance_01', $this->branch->whatsapp_instance_id);
        $this->assertEquals('branch_secret_token_123', $this->branch->whatsapp_api_key);
        $this->assertEquals(25, $this->branch->whatsapp_timeout);
    }

    /**
     * 5. مدير الفرع لا يمكنه تعديل إعدادات فرع آخر حتى لو مرر branch_id لفرع آخر
     */
    public function test_branch_admin_cannot_modify_other_branch(): void
    {
        $secondBranch = Branch::create([
            'app_id'  => $this->appModel->id,
            'name'    => 'فرع الحديدة',
            'code'    => 'HOD01',
            'city'    => 'الحديدة',
            'address' => 'شارع الميناء',
            'phone'   => '773334445',
            'is_main' => false,
            'whatsapp_instance_id' => 'orig_hod_inst',
        ]);

        // مدير فرع صنعاء يحاول إرسال إعدادات لفرع الحديدة
        $response = $this->actingAs($this->branchAdminUser)->put(route('app.whatsapp.settings.update'), [
            'branch_id'               => $secondBranch->id,
            'whatsapp_instance_id'    => 'hacked_instance',
            'whatsapp_instance_token' => 'hacked_token',
        ]);

        // يجب أن يتم حفظ الإعدادات على فرع صنعاء (فرعه هو) ولا يتأثر فرع الحديدة
        $secondBranch->refresh();
        $this->assertEquals('orig_hod_inst', $secondBranch->whatsapp_instance_id);

        $this->branch->refresh();
        $this->assertEquals('hacked_instance', $this->branch->whatsapp_instance_id);
    }

    /**
     * 3. حفظ وتحديث إعدادات WhatsApp للفرع
     */
    public function test_admin_can_save_whatsapp_settings(): void
    {
        $response = $this->actingAs($this->adminUser)->put(route('app.whatsapp.settings.update'), [
            'branch_id'               => $this->branch->id,
            'whatsapp_base_url'       => 'https://api.mywhatsapp.com',
            'whatsapp_instance_id'    => 'aman_instance_01',
            'whatsapp_instance_token' => 'super_secret_token_12345678',
            'whatsapp_webhook_url'    => 'https://aman.com/api/webhook',
            'whatsapp_timeout'        => 45,
        ]);

        $response->assertRedirect(route('app.whatsapp.settings'));

        $this->branch->refresh();
        $this->assertEquals('https://api.mywhatsapp.com', $this->branch->whatsapp_base_url);
        $this->assertEquals('aman_instance_01', $this->branch->whatsapp_instance_id);
        $this->assertEquals('super_secret_token_12345678', $this->branch->whatsapp_api_key);
        $this->assertEquals('https://aman.com/api/webhook', $this->branch->whatsapp_webhook_url);
        $this->assertEquals(45, $this->branch->whatsapp_timeout);
        $this->assertTrue($this->branch->hasWhatsAppConfigured());
    }

    /**
     * 4. الاحتفاظ بالرمز السري السابق عند تركه فارغاً أو مقنعاً
     */
    public function test_blank_or_masked_token_does_not_overwrite_existing_token(): void
    {
        $this->branch->update([
            'whatsapp_instance_id' => 'existing_instance',
            'whatsapp_api_key'     => 'existing_token_secret_999',
            'whatsapp_timeout'     => 30,
        ]);

        // إرسال الحقل فارغاً
        $response = $this->actingAs($this->adminUser)->put(route('app.whatsapp.settings.update'), [
            'branch_id'               => $this->branch->id,
            'whatsapp_instance_id'    => 'updated_instance',
            'whatsapp_instance_token' => '',
        ]);

        $this->branch->refresh();
        $this->assertEquals('existing_token_secret_999', $this->branch->whatsapp_api_key);
        $this->assertEquals('updated_instance', $this->branch->whatsapp_instance_id);

        // إرسال الحقل مقنعاً بنجوم أو نقاط
        $response2 = $this->actingAs($this->adminUser)->put(route('app.whatsapp.settings.update'), [
            'branch_id'               => $this->branch->id,
            'whatsapp_instance_id'    => 'updated_instance_2',
            'whatsapp_instance_token' => '••••••••••••••••',
        ]);

        $this->branch->refresh();
        $this->assertEquals('existing_token_secret_999', $this->branch->whatsapp_api_key);
        $this->assertEquals('updated_instance_2', $this->branch->whatsapp_instance_id);
    }

    /**
     * 5. حماية الرمز السري وإظهاره مقنعاً في الواجهة
     */
    public function test_token_is_masked_in_view(): void
    {
        $this->branch->update([
            'whatsapp_instance_id' => 'inst_1',
            'whatsapp_api_key'     => 'my_super_secret_token_987654321',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('app.whatsapp.settings'));

        $response->assertStatus(200);
        $response->assertDontSee('my_super_secret_token_987654321');
        $response->assertSee($this->branch->masked_whatsapp_token);
    }

    /**
     * 6. عزل إعدادات الفروع ومنع تداخلها
     */
    public function test_office_settings_isolation(): void
    {
        // تهيئة إعدادات مختلفة للفرعين
        $this->branch->update([
            'whatsapp_base_url'    => 'https://office1.example.com',
            'whatsapp_instance_id' => 'office1_inst',
            'whatsapp_api_key'     => 'office1_token_123',
        ]);

        $this->otherBranch->update([
            'whatsapp_base_url'    => 'https://office2.example.com',
            'whatsapp_instance_id' => 'office2_inst',
            'whatsapp_api_key'     => 'office2_token_456',
        ]);

        /** @var WhatsAppService $service */
        $service = app(WhatsAppService::class);

        // تحميل خدمة الفرع الأول
        $serviceBranch1 = $service->forBranch($this->branch);
        $this->assertEquals('https://office1.example.com', $serviceBranch1->getBaseUrl());
        $this->assertEquals('office1_inst', $serviceBranch1->getInstanceId());
        $this->assertEquals('office1_token_123', $serviceBranch1->getToken());

        // تحميل خدمة الفرع الثاني
        $serviceBranch2 = $service->forBranch($this->otherBranch);
        $this->assertEquals('https://office2.example.com', $serviceBranch2->getBaseUrl());
        $this->assertEquals('office2_inst', $serviceBranch2->getInstanceId());
        $this->assertEquals('office2_token_456', $serviceBranch2->getToken());

        // التأكد من أن الكائن الأول لم يتأثر
        $this->assertEquals('office1_inst', $serviceBranch1->getInstanceId());
    }

    /**
     * 7. فحص حالة الاتصال مع المزود بنجاح
     */
    public function test_check_connection_status_success(): void
    {
        $this->branch->update([
            'whatsapp_base_url'    => 'https://wa.mock-server.com',
            'whatsapp_instance_id' => 'mock_inst',
            'whatsapp_api_key'     => 'mock_token',
        ]);

        Http::fake([
            'https://wa.mock-server.com/instance/status*' => Http::response([
                'state' => 'open',
                'instance' => ['status' => 'connected']
            ], 200),
            'https://wa.mock-server.com/instance/connectionState/*' => Http::response([
                'state' => 'open',
                'instance' => ['status' => 'connected']
            ], 200),
        ]);

        $response = $this->actingAs($this->adminUser)->getJson(route('app.whatsapp.settings.status', ['branch_id' => $this->branch->id]));

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
        $this->branch->update([
            'whatsapp_base_url'    => 'https://wa.mock-server.com',
            'whatsapp_instance_id' => 'mock_inst',
            'whatsapp_api_key'     => 'mock_token',
        ]);

        Http::fake([
            'https://wa.mock-server.com/instance/status*' => Http::response([
                'message' => 'Unauthorized Apikey',
            ], 401),
            'https://wa.mock-server.com/instance/connectionState/*' => Http::response([
                'message' => 'Unauthorized Apikey',
            ], 401),
        ]);

        $failResponse = $this->actingAs($this->adminUser)->getJson(route('app.whatsapp.settings.status', ['branch_id' => $this->branch->id]));
        $failResponse->assertStatus(400);
        $failResponse->assertJson([
            'success' => false,
            'state'   => 'error',
        ]);
    }

    /**
     * 9. استخراج QR Code بنجاح
     */
    public function test_fetch_qr_code_success(): void
    {
        $this->branch->update([
            'whatsapp_base_url'    => 'https://wa.mock-server.com',
            'whatsapp_instance_id' => 'mock_inst',
            'whatsapp_api_key'     => 'mock_token',
        ]);

        Http::fake([
            'https://wa.mock-server.com/instance/qr*' => Http::response([
                'base64' => 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            ], 200),
            'https://wa.mock-server.com/instance/connect/*' => Http::response([
                'base64' => 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            ], 200),
        ]);

        $response = $this->actingAs($this->adminUser)->getJson(route('app.whatsapp.settings.qr', ['branch_id' => $this->branch->id]));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'qrcode'  => 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);
    }

    /**
     * 10. طلب كود الاقتران السريع برقم الهاتف
     */
    public function test_pairing_code_generation(): void
    {
        $this->branch->update([
            'whatsapp_base_url'    => 'https://wa.mock-server.com',
            'whatsapp_instance_id' => 'mock_inst',
            'whatsapp_api_key'     => 'mock_token',
        ]);

        Http::fake([
            'https://wa.mock-server.com/instance/pair*' => Http::response([
                'code' => 'ABCD-1234',
            ], 200),
            'https://wa.mock-server.com/instance/connect/*' => Http::response([
                'code' => 'ABCD-1234',
            ], 200),
        ]);

        $response = $this->actingAs($this->adminUser)->postJson(route('app.whatsapp.settings.pair'), [
            'branch_id' => $this->branch->id,
            'phone'     => '771234567',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'code'    => 'ABCD-1234',
        ]);
    }

    /**
     * 11. تنفيذ أوامر الجلسة (Connect, Reconnect, Disconnect, Logout)
     */
    public function test_session_lifecycle_actions(): void
    {
        $this->branch->update([
            'whatsapp_base_url'    => 'https://wa.mock-server.com',
            'whatsapp_instance_id' => 'mock_inst',
            'whatsapp_api_key'     => 'mock_token',
        ]);

        Http::fake([
            'https://wa.mock-server.com/instance/connect*'    => Http::response(['status' => 'connecting'], 200),
            'https://wa.mock-server.com/instance/reconnect*'  => Http::response(['status' => 'reconnected'], 200),
            'https://wa.mock-server.com/instance/restart*'    => Http::response(['status' => 'reconnected'], 200),
            'https://wa.mock-server.com/instance/disconnect*' => Http::response(['status' => 'disconnected'], 200),
            'https://wa.mock-server.com/instance/logout*'     => Http::response(['status' => 'logged_out'], 200),
            'https://wa.mock-server.com/instance/delete*'     => Http::response(['status' => 'logged_out'], 200),
        ]);

        // Connect
        $connRes = $this->actingAs($this->adminUser)->postJson(route('app.whatsapp.settings.connect'), ['branch_id' => $this->branch->id]);
        $connRes->assertStatus(200)->assertJson(['success' => true]);

        // Reconnect
        $recRes = $this->actingAs($this->adminUser)->postJson(route('app.whatsapp.settings.reconnect'), ['branch_id' => $this->branch->id]);
        $recRes->assertStatus(200)->assertJson(['success' => true]);

        // Disconnect
        $disRes = $this->actingAs($this->adminUser)->postJson(route('app.whatsapp.settings.disconnect'), ['branch_id' => $this->branch->id]);
        $disRes->assertStatus(200)->assertJson(['success' => true]);

        // Logout
        $logRes = $this->actingAs($this->adminUser)->postJson(route('app.whatsapp.settings.logout'), ['branch_id' => $this->branch->id]);
        $logRes->assertStatus(200)->assertJson(['success' => true]);
    }

    /**
     * 12. إرسال رسالة تجريبية بنجاح
     */
    public function test_send_test_message_success(): void
    {
        $this->branch->update([
            'whatsapp_base_url'    => 'https://wa.mock-server.com',
            'whatsapp_instance_id' => 'mock_inst',
            'whatsapp_api_key'     => 'mock_token',
        ]);

        Http::fake([
            'https://wa.mock-server.com/message/sendText*' => Http::response([
                'key' => ['id' => 'MSG_12345'],
                'message' => 'Sent successfully'
            ], 200),
            'https://wa.mock-server.com/send/text*' => Http::response([
                'key' => ['id' => 'MSG_12345'],
                'message' => 'Sent successfully'
            ], 200),
        ]);

        $successRes = $this->actingAs($this->adminUser)->postJson(route('app.whatsapp.settings.test'), [
            'branch_id' => $this->branch->id,
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
        $this->branch->update([
            'whatsapp_base_url'    => 'https://wa.mock-server.com',
            'whatsapp_instance_id' => 'mock_inst',
            'whatsapp_api_key'     => 'mock_token',
        ]);

        Http::fake([
            'https://wa.mock-server.com/message/sendText*' => Http::response([
                'error' => 'Connection closed or instance not ready',
            ], 400),
            'https://wa.mock-server.com/send/text*' => Http::response([
                'error' => 'Connection closed or instance not ready',
            ], 400),
        ]);

        $failRes = $this->actingAs($this->adminUser)->postJson(route('app.whatsapp.settings.test'), [
            'branch_id' => $this->branch->id,
            'recipient' => '771234567',
            'message'   => 'رسالة تجريبية أخرى',
        ]);

        $failRes->assertStatus(400);
        $failRes->assertJson(['success' => false]);
    }

    /**
     * 14. التحقق من تنسيق الأرقام المحلية والدولية دون تكرار
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
     * 15. استمرار الوظائف السابقة ودوال توليد الروابط
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
     * 16. فتح صفحة إعدادات WhatsApp عبر الموبايل
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
        $response->assertSee('فرع صنعاء الرئيسي');
    }
}
