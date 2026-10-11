<?php

namespace Tests\Feature;

use App\Models\App;
use App\Models\Branch;
use App\Models\Package;
use App\Models\Subscription;
use App\Models\User;
use App\Services\HttpSmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SmsSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected App $appModel;
    protected User $adminUser;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        // إنشاء اشتراك نشط وتطبيق ومستخدم مدير ومستخدم عادي
        $package = Package::firstOrCreate(
            ['name' => 'الباقة الأساسية'],
            [
                'price' => 0,
                'duration_in_days' => 30,
                'max_branches' => 5,
                'max_drivers' => 5,
                'max_shipments' => 100,
                'max_packages' => 100,
            ]
        );

        $this->appModel = App::create([
            'name' => 'شركة الأمان للنقل',
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
            'is_active' => true,
        ]);

        $branch = Branch::create([
            'app_id'  => $this->appModel->id,
            'name'    => 'الفرع الرئيسي',
            'code'    => 'MAIN',
            'city'    => 'صنعاء',
            'address' => 'شارع الزبيري',
            'phone'   => '771234567',
            'is_main' => true,
        ]);

        $this->adminUser = User::create([
            'name' => 'مدير النظام',
            'phone' => '967771112233',
            'password' => bcrypt('password'),
            'type' => 'admin',
            'app_id' => $this->appModel->id,
            'branch_id' => $branch->id,
            'is_phone_verified' => true,
        ]);

        $this->regularUser = User::create([
            'name' => 'موظف عادي',
            'phone' => '967774445566',
            'password' => bcrypt('password'),
            'type' => 'user',
            'app_id' => $this->appModel->id,
            'branch_id' => $branch->id,
            'is_phone_verified' => true,
        ]);
    }

    /**
     * اختبار: فتح صفحة إعدادات SMS بنجاح للمدير
     */
    public function test_admin_can_open_sms_settings_page(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('app.sms.settings'));

        $response->assertStatus(200);
        $response->assertSee('إعدادات الرسائل النصية SMS');
        $response->assertSee('شركة الأمان للنقل');
        $response->assertSee('https://abdaa.tiyar.cc/v1');
    }

    /**
     * اختبار: رفض وصول الموظف العادي إلى صفحة إعدادات SMS
     */
    public function test_regular_user_cannot_access_sms_settings(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('app.sms.settings'));

        $response->assertStatus(403);
    }

    /**
     * اختبار: حفظ وتحديث إعدادات SMS للمكتب
     */
    public function test_admin_can_save_sms_settings(): void
    {
        $response = $this->actingAs($this->adminUser)->put(route('app.sms.settings.update'), [
            'sms_api_key'    => 'test_api_key_secret_12345678',
            'sms_from_phone' => '+967779522898',
        ]);

        $response->assertRedirect(route('app.sms.settings'));

        $this->appModel->refresh();
        $this->assertEquals('test_api_key_secret_12345678', $this->appModel->sms_api_key);
        $this->assertEquals('+967779522898', $this->appModel->sms_from_phone);
        $this->assertTrue($this->appModel->hasSmsConfigured());
    }

    /**
     * اختبار: عدم استبدال مفتاح API عند تركه فارغاً في التعديل
     */
    public function test_blank_api_key_does_not_overwrite_existing_key(): void
    {
        $this->appModel->update([
            'sms_api_key'    => 'existing_secret_api_key',
            'sms_from_phone' => '771234567',
        ]);

        $response = $this->actingAs($this->adminUser)->put(route('app.sms.settings.update'), [
            'sms_api_key'    => '',
            'sms_from_phone' => '779998877',
        ]);

        $this->appModel->refresh();
        // المفتاح القديم لم يتغير
        $this->assertEquals('existing_secret_api_key', $this->appModel->sms_api_key);
        // تم تحديث رقم الهاتف الجديد
        $this->assertEquals('779998877', $this->appModel->sms_from_phone);
    }

    /**
     * اختبار: حماية المفتاح من الظهور الكامل في واجهة المستخدم
     */
    public function test_api_key_is_masked_in_view(): void
    {
        $this->appModel->update([
            'sms_api_key'    => 'my_secret_token_123456789',
            'sms_from_phone' => '+967779522898',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('app.sms.settings'));

        $response->assertStatus(200);
        // المفتاح الكامل الحساس لا يظهر في HTML
        $response->assertDontSee('my_secret_token_123456789');
        // القناع يظهر
        $response->assertSee($this->appModel->masked_sms_api_key);
    }

    /**
     * اختبار: نجاح إرسال رسالة تجريبية باستخدام HttpSmsService
     */
    public function test_send_test_sms_success(): void
    {
        $this->appModel->update([
            'sms_api_key'    => 'valid_api_key',
            'sms_from_phone' => '+967779522898',
        ]);

        Http::fake([
            'https://abdaa.tiyar.cc/v1/messages/send' => Http::response([
                'status' => 'success',
                'data'   => ['id' => 'msg_123456'],
            ], 200),
        ]);

        $response = $this->actingAs($this->adminUser)->postJson(route('app.sms.settings.test'), [
            'recipient' => '771234567',
            'message'   => 'رسالة تجريبية للتأكد من نجاح الخدمة',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
    }

    /**
     * اختبار: تعامل النظام عند فشل البوابة في الإرسال
     */
    public function test_send_test_sms_failure_handled_gracefully(): void
    {
        $this->appModel->update([
            'sms_api_key'    => 'invalid_api_key',
            'sms_from_phone' => '+967779522898',
        ]);

        Http::fake([
            'https://abdaa.tiyar.cc/v1/messages/send' => Http::response([
                'error' => 'Unauthorized or invalid key',
            ], 401),
        ]);

        $response = $this->actingAs($this->adminUser)->postJson(route('app.sms.settings.test'), [
            'recipient' => '771234567',
            'message'   => 'رسالة تجريبية',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
        ]);
    }

    /**
     * اختبار: منع إرسال رسالة تجريبية إذا لم تكن إعدادات المكتب مكتملة
     */
    public function test_cannot_send_test_without_configured_settings(): void
    {
        $this->appModel->update([
            'sms_api_key'    => null,
            'sms_from_phone' => null,
        ]);

        $response = $this->actingAs($this->adminUser)->postJson(route('app.sms.settings.test'), [
            'recipient' => '771234567',
            'message'   => 'رسالة تجريبية',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
    }

    /**
     * اختبار: التحقق من صحة مدخلات اختبار الإرسال (Validation)
     */
    public function test_send_test_sms_validation(): void
    {
        $this->appModel->update([
            'sms_api_key'    => 'some_key',
            'sms_from_phone' => '771234567',
        ]);

        $response = $this->actingAs($this->adminUser)->postJson(route('app.sms.settings.test'), [
            'recipient' => '',
            'message'   => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['recipient', 'message']);
    }

    /**
     * اختبار: فتح صفحة إعدادات SMS على أجهزة الموبايل
     */
    public function test_admin_can_open_mobile_sms_settings_page(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148',
            ])
            ->get(route('app.sms.settings'));

        $response->assertStatus(200);
        $response->assertSee('إعدادات رسائل SMS');
        $response->assertSee('شركة الأمان للنقل');
    }

    /**
     * اختبار: إرسال الرسالة التجريبية عبر نموذج الويب التقليدي
     */
    public function test_admin_can_send_test_via_standard_form(): void
    {
        $this->appModel->update([
            'sms_api_key'    => 'valid_api_key',
            'sms_from_phone' => '+967779522898',
        ]);

        Http::fake([
            'https://abdaa.tiyar.cc/v1/messages/send' => Http::response([
                'status' => 'success',
                'data'   => ['id' => 'msg_789'],
            ], 200),
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('app.sms.settings.test'), [
            'recipient' => '771234567',
            'message'   => 'رسالة تجريبية نموذجية',
        ]);

        $response->assertRedirect(route('app.sms.settings'));
        $response->assertSessionHas('success', true);
    }
}

