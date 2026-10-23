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
use App\Services\WhatsApp\Messages\ReceiverMessage;
use App\Models\Customer;
use App\Models\Passengers;
use App\Services\WhatsApp\WhatsAppLinkService;
use App\Services\WhatsApp\Messages\PassengerBookingMessage;
use App\Services\WhatsApp\Messages\PassengerCancelMessage;

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

        // 2. تحديد الفرع المرسل لتحميل إعدادات واتساب الخاصة به
        $branch = $shipment->senderBranch
            ?? $shipment->receiverBranch
            ?? auth()->user()?->branch;

        if (!$branch) {
            $branch = \App\Models\Branch::whereNotNull('whatsapp_api_key')
                ->where('whatsapp_api_key', '!=', '')
                ->first();
        }

        if ($branch) {
            $this->whatsAppService->loadBranchConfig($branch);
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
        $cacheKey = 'whatsapp_status_branch_' . ($branch?->id ?? 'default');
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
            $errorMsg = (string) ($result['message'] ?? '');
            $isNotRegistered = str_contains(strtolower($errorMsg), 'not registered');
            $isNetworkOrServerDown = !$isNotRegistered && (
                (isset($result['status']) && in_array((int)$result['status'], [401, 403, 404, 502, 503, 504]))
                || str_contains(strtolower($errorMsg), 'curl')
                || str_contains(strtolower($errorMsg), 'connection')
                || str_contains(strtolower($errorMsg), 'timeout')
            );

            if ($isNetworkOrServerDown) {
                Cache::put($cacheKey, 'offline', now()->addMinutes(10));
            }

            Log::warning("WhatsApp sendText (sender) failed for shipment #{$id}: " . $errorMsg);

            $userMessage = $isNotRegistered
                ? 'رقم هاتف المرسل غير مسجل في الواتساب، جاري فتح الواتساب للتحقق...'
                : 'خدمة الواتساب غير متصلة حالياً، جاري فتح الواتساب كرسالة...';

            return response()->json([
                'success'       => false,
                'fallback'      => true,
                'whatsapp_link' => $fallbackLink,
                'message'       => $userMessage,
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

    /**
     * إرسال إشعار المستلم لتسعير الطرد (ديناميكي)
     */
    public function sendPriceAgreementWhatsapp($id)
    {
        try {
            $shipment = Shipment::with([
                'senderCustomer' => fn($q) => $q->withoutGlobalScopes(),
                'receiverCustomer' => fn($q) => $q->withoutGlobalScopes(),
                'senderBranch.app',
                'receiverBranch.app',
                'receiverOfficeBranch.office'
            ])->findOrFail($id);

            $fallbackLink = WhatsAppLinkService::generate($shipment, 'price_agreement') ?? $shipment->price_agreement_whatsapp_link;

            $branch = $shipment->senderBranch
                ?? $shipment->receiverBranch
                ?? auth()->user()?->branch;

            if (!$branch) {
                $branch = \App\Models\Branch::whereNotNull('whatsapp_api_key')
                    ->where('whatsapp_api_key', '!=', '')
                    ->first();
            }

            if ($branch) {
                $this->whatsAppService->loadBranchConfig($branch);
            }

            $phone = $shipment->receiverCustomer?->phone ?? $shipment->senderCustomer?->phone ?? null;
            if (!$phone) {
                return response()->json([
                    'success' => false,
                    'message' => 'رقم هاتف العميل غير متوفر.',
                ], 422);
            }

            if (!$this->whatsAppService->isConfigured()) {
                return response()->json([
                    'success'       => false,
                    'fallback'      => true,
                    'whatsapp_link' => $fallbackLink,
                    'message'       => 'خدمة الواتساب التلقائية غير مهيأة، جاري التحويل للمحادثة المباشرة...',
                ]);
            }

            $cacheKey = 'whatsapp_status_branch_' . ($branch?->id ?? 'default');
            $serviceStatus = Cache::get($cacheKey, 'online');

            if ($serviceStatus === 'offline') {
                return response()->json([
                    'success'       => false,
                    'fallback'      => true,
                    'whatsapp_link' => $fallbackLink,
                    'message'       => 'خدمة الواتساب غير متصلة حالياً، جاري فتح الواتساب كرسالة...',
                ]);
            }

            $message = $shipment->getPriceAgreementMessage();

            $result = $this->whatsAppService->sendText($phone, $message);

            if (isset($result['error']) && $result['error']) {
                $errorMsg = (string) ($result['message'] ?? '');
                $isNotRegistered = str_contains(strtolower($errorMsg), 'not registered');
                $isNetworkOrServerDown = !$isNotRegistered && (
                    (isset($result['status']) && in_array((int)$result['status'], [401, 403, 404, 502, 503, 504]))
                    || str_contains(strtolower($errorMsg), 'curl')
                    || str_contains(strtolower($errorMsg), 'connection')
                    || str_contains(strtolower($errorMsg), 'timeout')
                );

                if ($isNetworkOrServerDown) {
                    Cache::put($cacheKey, 'offline', now()->addMinutes(10));
                }

                Log::warning("WhatsApp sendText (price agreement) failed for shipment #{$id}: " . $errorMsg);

                $userMessage = $isNotRegistered
                    ? 'رقم هاتف العميل غير مسجل في الواتساب، جاري فتح الواتساب للتحقق...'
                    : 'خدمة الواتساب غير متصلة حالياً، جاري فتح الواتساب كرسالة...';

                return response()->json([
                    'success'       => false,
                    'fallback'      => true,
                    'whatsapp_link' => $fallbackLink,
                    'message'       => $userMessage,
                ]);
            }

            Cache::put($cacheKey, 'online', now()->addMinutes(15));

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال إشعار تسعير الطرد بنجاح عبر الواتساب ✅',
            ]);

        } catch (\Exception $e) {
            Log::error("sendPriceAgreementWhatsapp Error for shipment #{$id}: " . $e->getMessage());

            $fallbackLink = isset($shipment) ? (WhatsAppLinkService::generate($shipment, 'price_agreement') ?? $shipment->price_agreement_whatsapp_link) : null;

            return response()->json([
                'success'       => false,
                'fallback'      => !empty($fallbackLink),
                'whatsapp_link' => $fallbackLink,
                'message'       => 'حدث خطأ، جاري فتح الواتساب للإرسال كرسالة...',
            ]);
        }
    }

    /**
     * إرسال إشعار المستلم (رسالة مع رابط السند وموقع الفرع)
     */
    public function sendReceiverPdf($id)
    {
        try {
            $shipment = Shipment::with([
                'senderCustomer' => fn($q) => $q->withoutGlobalScopes(),
                'receiverCustomer' => fn($q) => $q->withoutGlobalScopes(),
                'senderBranch.app',
                'receiverBranch.app',
                'receiverOfficeBranch.office'
            ])->findOrFail($id);

            $fallbackLink = WhatsAppLinkService::generate($shipment, 'receiver') ?? $shipment->receiver_whatsapp_link;

            $branch = $shipment->senderBranch
                ?? $shipment->receiverBranch
                ?? auth()->user()?->branch;

            if (!$branch) {
                $branch = \App\Models\Branch::whereNotNull('whatsapp_api_key')
                    ->where('whatsapp_api_key', '!=', '')
                    ->first();
            }

            if ($branch) {
                $this->whatsAppService->loadBranchConfig($branch);
            }

            $phone = $shipment->receiverCustomer?->phone ?? null;
            if (!$phone) {
                return response()->json([
                    'success' => false,
                    'message' => 'رقم هاتف المستلم غير متوفر.',
                ], 422);
            }

            if (!$this->whatsAppService->isConfigured()) {
                return response()->json([
                    'success'       => false,
                    'fallback'      => true,
                    'whatsapp_link' => $fallbackLink,
                    'message'       => 'خدمة الواتساب التلقائية غير مهيأة، جاري التحويل للمحادثة المباشرة...',
                ]);
            }

            $cacheKey = 'whatsapp_status_branch_' . ($branch?->id ?? 'default');
            $serviceStatus = Cache::get($cacheKey, 'online');

            if ($serviceStatus === 'offline') {
                return response()->json([
                    'success'       => false,
                    'fallback'      => true,
                    'whatsapp_link' => $fallbackLink,
                    'message'       => 'خدمة الواتساب غير متصلة حالياً، جاري فتح الواتساب كرسالة...',
                ]);
            }

            $receiptUrl = route('receipt.generate', [
                'type' => 'receiver',
                'id'   => $shipment->uuid ?? $shipment->id,
            ]);

            $receiverMessage = new ReceiverMessage();
            $message = $receiverMessage->getMessageBody($shipment, $receiptUrl);

            $result = $this->whatsAppService->sendText($phone, $message);

            if (isset($result['error']) && $result['error']) {
                $errorMsg = (string) ($result['message'] ?? '');
                $isNotRegistered = str_contains(strtolower($errorMsg), 'not registered');
                $isNetworkOrServerDown = !$isNotRegistered && (
                    (isset($result['status']) && in_array((int)$result['status'], [401, 403, 404, 502, 503, 504]))
                    || str_contains(strtolower($errorMsg), 'curl')
                    || str_contains(strtolower($errorMsg), 'connection')
                    || str_contains(strtolower($errorMsg), 'timeout')
                );

                if ($isNetworkOrServerDown) {
                    Cache::put($cacheKey, 'offline', now()->addMinutes(10));
                }

                Log::warning("WhatsApp sendText (receiver) failed for shipment #{$id}: " . $errorMsg);

                $userMessage = $isNotRegistered
                    ? 'رقم هاتف المستلم غير مسجل في الواتساب، جاري فتح الواتساب للتحقق...'
                    : 'خدمة الواتساب غير متصلة حالياً، جاري فتح الواتساب كرسالة...';

                return response()->json([
                    'success'       => false,
                    'fallback'      => true,
                    'whatsapp_link' => $fallbackLink,
                    'message'       => $userMessage,
                ]);
            }

            Cache::put($cacheKey, 'online', now()->addMinutes(15));

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال إشعار المستلم بنجاح عبر الواتساب ✅',
            ]);

        } catch (\Exception $e) {
            Log::error("sendReceiverPdf Error for shipment #{$id}: " . $e->getMessage());

            $fallbackLink = isset($shipment) ? (WhatsAppLinkService::generate($shipment, 'receiver') ?? $shipment->receiver_whatsapp_link) : null;

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

    /**
     * إرسال تفاصيل حجز الراكب مباشرة عبر واتساب (النهج المتبع في الطرود)
     */
    public function sendPassengerBooking($id)
    {
        try {
            $passenger = Passengers::with(['broker', 'driver', 'branch.app'])->findOrFail($id);

            $fallbackLink = WhatsAppLinkService::generate($passenger, 'passengerBooking');

            $branch = $passenger->branch ?? auth()->user()?->branch;
            if (!$branch) {
                $branch = \App\Models\Branch::whereNotNull('whatsapp_api_key')
                    ->where('whatsapp_api_key', '!=', '')
                    ->first();
            }

            if ($branch) {
                $this->whatsAppService->loadBranchConfig($branch);
            }

            $phone = $passenger->passenger_number;
            if (!$phone) {
                return response()->json([
                    'success' => false,
                    'message' => 'رقم هاتف الراكب غير متوفر.',
                ], 422);
            }

            if (!$this->whatsAppService->isConfigured()) {
                return response()->json([
                    'success'       => false,
                    'fallback'      => true,
                    'whatsapp_link' => $fallbackLink,
                    'message'       => 'خدمة الواتساب التلقائية غير مهيأة، جاري التحويل للمحادثة المباشرة...',
                ]);
            }

            $cacheKey = 'whatsapp_status_branch_' . ($branch?->id ?? 'default');
            $serviceStatus = Cache::get($cacheKey, 'online');

            if ($serviceStatus === 'offline') {
                $checkLive = $this->whatsAppService->getStatus();
                if (!empty($checkLive['data']['Connected'])) {
                    $serviceStatus = 'online';
                    Cache::put($cacheKey, 'online', now()->addMinutes(15));
                } else {
                    return response()->json([
                        'success'       => false,
                        'fallback'      => true,
                        'whatsapp_link' => $fallbackLink,
                        'message'       => 'خدمة الواتساب غير متصلة حالياً، جاري فتح الواتساب كرسالة...',
                    ]);
                }
            }

            $bookingMessage = new PassengerBookingMessage();
            $message = $bookingMessage->getMessageBody($passenger);

            $result = $this->whatsAppService->sendText($phone, $message);

            if (isset($result['error']) && $result['error']) {
                $errorMsg = (string) ($result['message'] ?? '');
                
                $serverStatus = $this->whatsAppService->getStatus();
                $isServerOnline = !empty($serverStatus['data']['Connected']);

                if (!$isServerOnline) {
                    Cache::put($cacheKey, 'offline', now()->addMinute());
                    $userMessage = 'خدمة الواتساب غير متصلة حالياً، جاري فتح الواتساب كرسالة...';
                } else {
                    Cache::put($cacheKey, 'online', now()->addMinutes(15));
                    $userMessage = 'رقم هاتف الراكب غير مسجل في الواتساب أو غير صحيح، جاري فتح الواتساب للتحقق...';
                }

                Log::warning("WhatsApp sendText (passengerBooking) failed for passenger #{$id}: " . $errorMsg);

                return response()->json([
                    'success'       => false,
                    'fallback'      => true,
                    'whatsapp_link' => $fallbackLink,
                    'message'       => $userMessage,
                ]);
            }

            Cache::put($cacheKey, 'online', now()->addMinutes(15));

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال تفاصيل الحجز للراكب بنجاح عبر الواتساب ✅',
            ]);

        } catch (\Exception $e) {
            Log::error("sendPassengerBooking Error for passenger #{$id}: " . $e->getMessage());

            $fallbackLink = isset($passenger) ? WhatsAppLinkService::generate($passenger, 'passengerBooking') : null;

            return response()->json([
                'success'       => false,
                'fallback'      => !empty($fallbackLink),
                'whatsapp_link' => $fallbackLink,
                'message'       => 'حدث خطأ، جاري فتح الواتساب للإرسال كرسالة...',
            ]);
        }
    }

    /**
     * إرسال إشعار إلغاء حجز الراكب للوسيط مباشرة عبر واتساب (النهج المتبع في الطرود)
     */
    public function sendPassengerCancel($id)
    {
        try {
            $passenger = Passengers::with(['broker', 'driver', 'branch.app'])->findOrFail($id);

            $fallbackLink = WhatsAppLinkService::generate($passenger, 'passengerCancel');

            $branch = $passenger->branch ?? auth()->user()?->branch;
            if (!$branch) {
                $branch = \App\Models\Branch::whereNotNull('whatsapp_api_key')
                    ->where('whatsapp_api_key', '!=', '')
                    ->first();
            }

            if ($branch) {
                $this->whatsAppService->loadBranchConfig($branch);
            }

            $phone = $passenger->broker?->phone;
            if (!$phone) {
                return response()->json([
                    'success' => false,
                    'message' => 'رقم هاتف أو واتساب الوسيط غير متوفر في السند.',
                ], 422);
            }

            if (!$this->whatsAppService->isConfigured()) {
                return response()->json([
                    'success'       => false,
                    'fallback'      => true,
                    'whatsapp_link' => $fallbackLink,
                    'message'       => 'خدمة الواتساب التلقائية غير مهيأة، جاري التحويل للمحادثة المباشرة...',
                ]);
            }

            $cacheKey = 'whatsapp_status_branch_' . ($branch?->id ?? 'default');
            $serviceStatus = Cache::get($cacheKey, 'online');

            if ($serviceStatus === 'offline') {
                $checkLive = $this->whatsAppService->getStatus();
                if (!empty($checkLive['data']['Connected'])) {
                    $serviceStatus = 'online';
                    Cache::put($cacheKey, 'online', now()->addMinutes(15));
                } else {
                    return response()->json([
                        'success'       => false,
                        'fallback'      => true,
                        'whatsapp_link' => $fallbackLink,
                        'message'       => 'خدمة الواتساب غير متصلة حالياً، جاري فتح الواتساب كرسالة...',
                    ]);
                }
            }

            $cancelMessage = new PassengerCancelMessage();
            $message = $cancelMessage->getMessageBody($passenger);

            $result = $this->whatsAppService->sendText($phone, $message);

            if (isset($result['error']) && $result['error']) {
                $errorMsg = (string) ($result['message'] ?? '');
                
                $serverStatus = $this->whatsAppService->getStatus();
                $isServerOnline = !empty($serverStatus['data']['Connected']);

                if (!$isServerOnline) {
                    Cache::put($cacheKey, 'offline', now()->addMinute());
                    $userMessage = 'خدمة الواتساب غير متصلة حالياً، جاري فتح الواتساب كرسالة...';
                } else {
                    Cache::put($cacheKey, 'online', now()->addMinutes(15));
                    $userMessage = 'رقم هاتف الوسيط غير مسجل في الواتساب أو غير صحيح، جاري فتح الواتساب للإرسال اليدوي...';
                }

                Log::warning("WhatsApp sendText (passengerCancel) failed for passenger #{$id}: " . $errorMsg);

                return response()->json([
                    'success'       => false,
                    'fallback'      => true,
                    'whatsapp_link' => $fallbackLink,
                    'message'       => $userMessage,
                ]);
            }

            Cache::put($cacheKey, 'online', now()->addMinutes(15));

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال إشعار الإلغاء للوسيط بنجاح عبر الواتساب ✅',
            ]);

        } catch (\Exception $e) {
            Log::error("sendPassengerCancel Error for passenger #{$id}: " . $e->getMessage());

            $fallbackLink = isset($passenger) ? WhatsAppLinkService::generate($passenger, 'passengerCancel') : null;

            return response()->json([
                'success'       => false,
                'fallback'      => !empty($fallbackLink),
                'whatsapp_link' => $fallbackLink,
                'message'       => 'حدث خطأ، جاري فتح الواتساب للإرسال كرسالة...',
            ]);
        }
    }

    /**
     * إرسال إشعار تأكيد / إعادة حجز الراكب للوسيط مباشرة عبر واتساب
     */
    public function sendPassengerRebook($id)
    {
        try {
            $passenger = Passengers::with(['broker', 'driver', 'branch.app'])->findOrFail($id);

            $fallbackLink = WhatsAppLinkService::generate($passenger, 'passengerRebook');

            $branch = $passenger->branch ?? auth()->user()?->branch;
            if (!$branch) {
                $branch = \App\Models\Branch::whereNotNull('whatsapp_api_key')
                    ->where('whatsapp_api_key', '!=', '')
                    ->first();
            }

            if ($branch) {
                $this->whatsAppService->loadBranchConfig($branch);
            }

            $phone = $passenger->broker?->phone;
            if (!$phone) {
                return response()->json([
                    'success' => false,
                    'message' => 'رقم هاتف أو واتساب الوسيط غير متوفر.',
                ], 422);
            }

            if (!$this->whatsAppService->isConfigured()) {
                return response()->json([
                    'success'       => false,
                    'fallback'      => true,
                    'whatsapp_link' => $fallbackLink,
                    'message'       => 'خدمة الواتساب التلقائية غير مهيأة، جاري التحويل للمحادثة المباشرة...',
                ]);
            }

            $cacheKey = 'whatsapp_status_branch_' . ($branch?->id ?? 'default');
            $serviceStatus = Cache::get($cacheKey, 'online');

            if ($serviceStatus === 'offline') {
                $checkLive = $this->whatsAppService->getStatus();
                if (!empty($checkLive['data']['Connected'])) {
                    $serviceStatus = 'online';
                    Cache::put($cacheKey, 'online', now()->addMinutes(15));
                } else {
                    return response()->json([
                        'success'       => false,
                        'fallback'      => true,
                        'whatsapp_link' => $fallbackLink,
                        'message'       => 'خدمة الواتساب غير متصلة حالياً، جاري فتح الواتساب كرسالة...',
                    ]);
                }
            }

            $rebookMessage = new \App\Services\WhatsApp\Messages\PassengerRebookMessage();
            $message = $rebookMessage->getMessageBody($passenger);

            $result = $this->whatsAppService->sendText($phone, $message);

            if (isset($result['error']) && $result['error']) {
                $errorMsg = (string) ($result['message'] ?? '');
                
                $serverStatus = $this->whatsAppService->getStatus();
                $isServerOnline = !empty($serverStatus['data']['Connected']);

                if (!$isServerOnline) {
                    Cache::put($cacheKey, 'offline', now()->addMinute());
                    $userMessage = 'خدمة الواتساب غير متصلة حالياً، جاري فتح الواتساب كرسالة...';
                } else {
                    Cache::put($cacheKey, 'online', now()->addMinutes(15));
                    $userMessage = 'رقم هاتف الوسيط غير مسجل في الواتساب أو غير صحيح، جاري فتح الواتساب للإرسال اليدوي...';
                }

                Log::warning("WhatsApp sendText (passengerRebook) failed for passenger #{$id}: " . $errorMsg);

                return response()->json([
                    'success'       => false,
                    'fallback'      => true,
                    'whatsapp_link' => $fallbackLink,
                    'message'       => $userMessage,
                ]);
            }

            Cache::put($cacheKey, 'online', now()->addMinutes(15));

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال إشعار تأكيد الحجز للوسيط بنجاح عبر الواتساب ✅',
            ]);

        } catch (\Exception $e) {
            Log::error("sendPassengerRebook Error for passenger #{$id}: " . $e->getMessage());

            $fallbackLink = isset($passenger) ? WhatsAppLinkService::generate($passenger, 'passengerRebook') : null;

            return response()->json([
                'success'       => false,
                'fallback'      => !empty($fallbackLink),
                'whatsapp_link' => $fallbackLink,
                'message'       => 'حدث خطأ، جاري فتح الواتساب للإرسال كرسالة...',
            ]);
        }
    }
}

