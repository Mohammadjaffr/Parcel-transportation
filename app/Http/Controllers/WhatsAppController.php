<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\ShipmentPackage;
use App\Services\WhatsAppService;
use App\Services\SendPDFService;
use App\Classes\WebResponseClass;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\WhatsApp\Messages\SenderMessage;
use App\Models\Customer;
use App\Services\WhatsApp\WhatsAppLinkService;

class WhatsAppController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * إرسال إشعار المرسل (رسالة مع رابط السند):
     * - في حال كان إيفولوشن واتساب مشبوك وحالته تمام: يتم الإرسال من خلاله تلقائياً بدون فتح المتصفح.
     * - في حال كان غير متصل أو فشل الإرسال: يتم إرجاع رابط الواتساب (wa.me) ليتم تحويل المستخدم للإرسال كرسالة مباشرة.
     */
    public function sendSenderPdf($id)
{
    try {
        // 1. جلب الشحنة مع جميع العلاقات المطلوبة وبدون Global Scopes للعملاء
        $shipment = Shipment::with([
            'senderCustomer' => fn($q) => $q->withoutGlobalScopes(),
            'receiverCustomer' => fn($q) => $q->withoutGlobalScopes(),
            'senderBranch.app',
            'receiverBranch.app',
            'receiverOfficeBranch.office'
        ])->findOrFail($id);

        // تجهيز رابط الواتساب اليدوي البديل (wa.me) مسبقاً
        $fallbackLink = WhatsAppLinkService::generate($shipment, 'sender');

        // 2. تحديد التطبيق (App) المرتبط بالشحنة لتحميل إعداداته وفحص الكاش الخاص به
        $app = $shipment->senderBranch?->app 
            ?? $shipment->receiverBranch?->app 
            ?? auth()->user()?->app;

        if (!$app) {
            $app = \App\Models\App::whereNotNull('whatsapp_api_key')
                ->where('whatsapp_api_key', '!=', '')
                ->first();
        }

        if ($app) {
            $this->whatsAppService->loadAppConfig($app);
        }

        // 3. التحقق من وجود رقم هاتف المرسل
        $phone = $shipment->senderCustomer?->phone ?? null;
        if (!$phone) {
            return response()->json([
                'success' => false,
                'message' => 'رقم هاتف المرسل غير متوفر.',
            ], 422);
        }

        // 4. التحقق من تهيئة بيانات الـ API الخاصة بالواتساب
        if (!$this->whatsAppService->isConfigured()) {
            return response()->json([
                'success'       => false,
                'fallback'      => true,
                'whatsapp_link' => $fallbackLink,
                'message'       => 'خدمة الواتساب التلقائية غير مهيأة، جاري التحويل للمحادثة المباشرة...',
            ]);
        }

        // ---------------------------------------------------------------------
        // ⚡ 5. فحص حالة الاتصال من الكاش (الذي يتم تحديثه عبر الـ Cron Job كل 10 دقائق)
        // ---------------------------------------------------------------------
        $cacheKey = 'whatsapp_status_' . ($app?->id ?? 'default');
        $serviceStatus = Cache::get($cacheKey, 'online'); // القيمة الافتراضية online في حال لم يتم الفحص بعد

        if ($serviceStatus === 'offline') {
            // تحويل فوري ومباشر إلى wa.me خلال أجزاء من الثانية دون أي تعليق أو انتظار
            return response()->json([
                'success'       => false,
                'fallback'      => true,
                'whatsapp_link' => $fallbackLink,
                'message'       => 'خدمة الواتساب غير متصلة حالياً، جاري فتح الواتساب كرسالة...',
            ]);
        }

        // 6. تجهيز رابط السند ونص الرسالة
        $receiptUrl = route('receipt.generate', [
            'type' => 'sender',
            'id'   => $shipment->uuid ?? $shipment->id,
        ]);

        $senderMessage = new SenderMessage();
        $message = $senderMessage->getMessageBody($shipment, $receiptUrl);

        // 7. إرسال الرسالة عبر WhatsApp API
        $result = $this->whatsAppService->sendText($phone, $message);

        // 8. التحقق من نتيجة الإرسال
        if (isset($result['error']) && $result['error']) {
            // في حال فشل الإرسال أو انقطاع مفاجئ، نحدث الكاش فوراً إلى offline لمدة 10 دقائق حتى لا يتأخر بقية الموظفين
            Cache::put($cacheKey, 'offline', now()->addMinutes(10));

            Log::warning("WhatsApp sendText failed for shipment #{$id}, marked cache as offline: " . ($result['message'] ?? ''));

            return response()->json([
                'success'       => false,
                'fallback'      => true,
                'whatsapp_link' => $fallbackLink,
                'message'       => 'خدمة الواتساب غير متصلة حالياً، جاري فتح الواتساب كرسالة...',
            ]);
        }

        // في حال نجاح الإرسال نتأكد من بقاء الكاش online
        Cache::put($cacheKey, 'online', now()->addMinutes(15));

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال إشعار المرسل بنجاح عبر الواتساب ✅',
        ]);

    } catch (\Exception $e) {
        Log::error("sendSenderPdf Error for shipment #{$id}: " . $e->getMessage());

        $fallbackLink = isset($shipment) ? WhatsAppLinkService::generate($shipment, 'sender') : null;

        return response()->json([
            'success'       => false,
            'fallback'      => !empty($fallbackLink),
            'whatsapp_link' => $fallbackLink,
            'message'       => 'حدث خطأ، جاري فتح الواتساب للإرسال كرسالة...',
        ]);
    }
}

    public function openForSender($id)
    {
        $shipment = Shipment::findOrFail($id);
        $link = $this->whatsAppService->getSenderLink($shipment);

        return redirect()->away($link);
    }

    public function openForReceiver($id)
    {
        $shipment = Shipment::findOrFail($id);
        $link = $this->whatsAppService->getReceiverLink($shipment);

        return redirect()->away($link);
    }

    public function openForBranch($id)
    {
        //
    }

    /**
     * Send Branch Manifest via WhatsApp
     *
     * @param  int  $id  ShipmentPackage ID
     * @param  string|null  $branchCode  Optional branch code to filter
     */
    public function sendBranchManifest($id, $branchCode = null)
    {
        $package = ShipmentPackage::with(['shipments.receiverBranch'])->findOrFail($id);
        $link = $this->whatsAppService->getBranchManifestLink($package, $branchCode);

        if (! $link) {
         
            return WebResponseClass::sendValidationError('فشل في إنشاء الرابط: تأكد من وجود رقم الفرع أو المحاولة مرة أخرى.');
        }

        return redirect()->away($link);
    }

    /**
     * Send Driver Manifest via WhatsApp
     */
    public function sendDriverManifest($id)
    {
        $package = ShipmentPackage::with(['shipments.senderCustomer', 'shipments.receiverCustomer', 'shipments.receiverBranch', 'shipments.senderBranch'])->findOrFail($id);

        if (!$package->driver_phone) {
            return WebResponseClass::sendValidationError('رقم هاتف السائق غير متوفر');
        }

        $link = $this->whatsAppService->getDriverManifestLink($package);

        if (! $link) {
            return WebResponseClass::sendValidationError('فشل إنشاء ملف PDF أو رفعه. المحاولة مرة أخرى.');
        }

        return redirect()->away($link);
    }
    public function sendCustomerAccountStatement($id)
{
    $user = auth()->user();

    $customer = Customer::where('app_id', $user->app_id)
        ->where('branch_id', $user->branch_id)
        ->findOrFail($id);

    $link = WhatsAppLinkService::generate($customer, 'CustomerAccountStatement');

    if (!$link) {
        return WebResponseClass::sendValidationError('فشل في إنشاء رابط واتساب: تأكد من وجود رقم العميل.');
    }

    return redirect()->away($link);
}
}
