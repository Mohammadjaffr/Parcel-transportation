<?php

namespace App\Services;

use App\Models\App;
use App\Models\Shipment;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class WhatsAppService
{
    protected ?App $currentApp = null;
    protected string $baseUrl;
    protected ?string $instanceId = null;
    protected ?string $token = null;
    protected ?string $webhookUrl = null;
    protected int $timeout = 30;
    protected ?SendPDFService $sendPDFService = null;

    /**
     * التهيئة الديناميكية مع دعم تعدد المكاتب والـ Multi-Tenancy
     */
    public function __construct(?App $app = null, ?SendPDFService $sendPDFService = null)
    {
        $this->sendPDFService = $sendPDFService;

        if ($app) {
            $this->loadAppConfig($app);
        } elseif (Auth::check() && Auth::user()?->app) {
            $this->loadAppConfig(Auth::user()->app);
        } else {
            $this->loadDefaultConfig();
        }
    }

    /**
     * تحميل إعدادات مكتب / شركة محددة
     */
    public function loadAppConfig(App $app): self
    {
        $this->currentApp = $app;
        $this->baseUrl    = $app->whatsapp_base_url ?: rtrim(config('whatsapp.base_url', 'http://195.35.24.73:4000'), '/');
        $this->instanceId = $app->whatsapp_instance_id ?: config('whatsapp.instance_id');
        $this->token      = $app->whatsapp_api_key ?: config('whatsapp.global_api_key');
        $this->webhookUrl = $app->whatsapp_webhook_url ?: config('whatsapp.webhook_url');
        $this->timeout    = $app->whatsapp_timeout ?: (int) config('whatsapp.timeout', 30);

        return $this;
    }

    /**
     * تحميل الإعدادات الافتراضية العامة للنظام من ملف config
     */
    public function loadDefaultConfig(): self
    {
        $this->currentApp = null;
        $this->baseUrl    = rtrim(config('whatsapp.base_url', 'http://195.35.24.73:4000'), '/');
        $this->instanceId = config('whatsapp.instance_id');
        $this->token      = config('whatsapp.global_api_key');
        $this->webhookUrl = config('whatsapp.webhook_url');
        $this->timeout    = (int) config('whatsapp.timeout', 30);

        return $this;
    }

    /**
     * إرجاع نسخة معزولة خاصة بمكتب محدد لمنع تسرب الإعدادات بين الطلبات والمهام الخلفية (Queue/Jobs)
     */
    public function forApp(App|int|null $app): self
    {
        $clone = clone $this;

        if ($app instanceof App) {
            $clone->loadAppConfig($app);
        } elseif (is_numeric($app)) {
            $appModel = App::find($app);
            if ($appModel) {
                $clone->loadAppConfig($appModel);
            }
        } elseif ($app === null) {
            $clone->loadDefaultConfig();
        }

        return $clone;
    }

    /**
     * إرجاع نسخة معزولة بإعدادات مخصصة
     */
    public function withConfig(array $config): self
    {
        $clone = clone $this;

        if (isset($config['base_url'])) {
            $clone->baseUrl = rtrim($config['base_url'], '/');
        }
        if (isset($config['instance_id'])) {
            $clone->instanceId = $config['instance_id'];
        }
        if (isset($config['instance_token'])) {
            $clone->token = $config['instance_token'];
        }
        if (isset($config['webhook_url'])) {
            $clone->webhookUrl = $config['webhook_url'];
        }
        if (isset($config['timeout'])) {
            $clone->timeout = (int) $config['timeout'];
        }

        return $clone;
    }

    /**
     * التحقق مما إذا كانت الخدمة مهيأة ببيانات الاتصال الأساسية
     */
    public function isConfigured(): bool
    {
        return !empty($this->baseUrl) && !empty($this->token);
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getInstanceId(): ?string
    {
        return $this->instanceId;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getWebhookUrl(): ?string
    {
        return $this->webhookUrl;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function getCurrentApp(): ?App
    {
        return $this->currentApp;
    }

    /**
     * إعداد عميل الـ HTTP بالـ Headers الأساسية
     */
    protected function client()
    {
        return Http::baseUrl($this->baseUrl)
            ->timeout($this->timeout)
            ->withHeaders([
                'Apikey'       => $this->token ?? '',
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ]);
    }

    /**
     * معالجة استجابة الـ HTTP
     */
    protected function handleResponse(Response $response): array
    {
        if ($response->failed()) {
            return [
                'error'   => true,
                'message' => data_get($response->json(), 'message', data_get($response->json(), 'error', 'HTTP Error ' . $response->status())),
                'status'  => $response->status(),
                'data'    => $response->json(),
            ];
        }
        return $response->json() ?? [];
    }

    /* =========================================================================
     * 1. دوال التحكم بالإنستانس وإدارة الجلسة (Instance & Session Management)
     * ========================================================================= */

    /**
     * فحص حالة الاتصال الحالية للإنستانس
     * GET /instance/status أو GET /instance/connectionState/{instance}
     */
    public function getStatus(): array
    {
        try {
            $response = $this->client()->get('/instance/status');

            if ($response->status() === 404 && !empty($this->instanceId)) {
                $fallback = $this->client()->get("/instance/connectionState/{$this->instanceId}");
                if ($fallback->successful()) {
                    return $this->handleResponse($fallback);
                }
            }

            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('WhatsAppService getStatus Error: ' . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * بدء الاتصال وتحديد الـ Webhook
     * POST /instance/connect
     */
    public function connect(?string $webhookUrl = null, array $events = ['MESSAGES_UPSERT', 'CONNECTION_UPDATE', 'QRCODE_UPDATED']): array
    {
        try {
            $payload = [
                'webhookUrl' => $webhookUrl ?? $this->webhookUrl,
                'subscribe'  => $events,
                'immediate'  => true,
            ];

            $response = $this->client()->post('/instance/connect', $payload);
            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('WhatsAppService connect Error: ' . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * جلب رمز الـ QR Code لربط الواتساب
     * GET /instance/qr
     */
    public function getQrCode(): array
    {
        try {
            $response = $this->client()->get('/instance/qr');
            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('WhatsAppService getQrCode Error: ' . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * طلب كود الاقتران عبر رقم الهاتف (بديل الـ QR Code)
     * POST /instance/pair
     */
    public function getPairingCode(string $phone): array
    {
        try {
            $response = $this->client()->post('/instance/pair', [
                'phone' => $this->formatNumber($phone),
            ]);
            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('WhatsAppService getPairingCode Error: ' . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * فصل الاتصال مؤقتاً
     * POST /instance/disconnect
     */
    public function disconnect(): array
    {
        try {
            $response = $this->client()->post('/instance/disconnect');
            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('WhatsAppService disconnect Error: ' . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * إعادة الاتصال
     * POST /instance/reconnect
     */
    public function reconnect(): array
    {
        try {
            $response = $this->client()->post('/instance/reconnect');
            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('WhatsAppService reconnect Error: ' . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * تسجيل الخروج ومسح الجلسة لربط رقم جديد
     * DELETE /instance/logout
     */
    public function logout(): array
    {
        try {
            $response = $this->client()->delete('/instance/logout');
            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('WhatsAppService logout Error: ' . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * جلب معلومات وتفاصيل الإنستانس
     * GET /instance/info/{instanceId}
     */
    public function getInstanceInfo(): array
    {
        try {
            $url = $this->instanceId ? "/instance/info/{$this->instanceId}" : '/instance/status';
            $response = $this->client()->get($url);
            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('WhatsAppService getInstanceInfo Error: ' . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * تحديث الإعدادات المتقدمة
     * PUT /instance/{instanceId}/advanced-settings
     */
    public function updateAdvancedSettings(array $settings): array
    {
        try {
            $url = $this->instanceId ? "/instance/{$this->instanceId}/advanced-settings" : '/instance/advanced-settings';
            $response = $this->client()->put($url, $settings);
            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('WhatsAppService updateAdvancedSettings Error: ' . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /* =========================================================================
     * 2. دوال إرسال الرسائل (Messaging Methods)
     * ========================================================================= */

    /**
     * إرسال رسالة نصية مباشرة
     * POST /send/text
     */
    public function sendText(string $phone, string $text, ?int $delay = null): array
    {
        try {
            $payload = [
                'number' => $this->formatNumber($phone),
                'text'   => $text,
            ];

            if ($delay) {
                $payload['delay'] = $delay;
            }

            $response = $this->client()->post('/send/text', $payload);
            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error("WhatsAppService sendText to {$phone} Error: " . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * إرسال ملف، شهادة أو تقرير PDF / صورة
     * POST /send/media
     */
    public function sendMedia(string $phone, string $fileUrl, string $type = 'document', ?string $caption = null, ?string $filename = null): array
    {
        try {
            $payload = [
                'number' => $this->formatNumber($phone),
                'type'   => $type,
                'url'    => $fileUrl,
            ];

            if ($caption) {
                $payload['caption'] = $caption;
            }

            if ($filename) {
                $payload['filename'] = $filename;
            }

            $response = $this->client()->post('/send/media', $payload);
            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error("WhatsAppService sendMedia to {$phone} Error: " . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * إرسال رسالة بأزرار تفاعلية (Interactive Buttons)
     * POST /send/button
     */
    public function sendButton(string $phone, string $title, string $description, string $footer, array $buttons): array
    {
        try {
            $payload = [
                'number'      => $this->formatNumber($phone),
                'title'       => $title,
                'description' => $description,
                'footer'      => $footer,
                'buttons'     => $buttons,
            ];

            $response = $this->client()->post('/send/button', $payload);
            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error("WhatsAppService sendButton to {$phone} Error: " . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * فحص ما إذا كان الرقم مسجلاً على واتساب
     * POST /user/check
     */
    public function checkNumber(string $phone): array
    {
        try {
            $response = $this->client()->post('/user/check', [
                'number' => [$this->formatNumber($phone)],
            ]);
            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error("WhatsAppService checkNumber {$phone} Error: " . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /* =========================================================================
     * 3. دوال تنسيق ومعالجة أرقام الهواتف (Phone Formatting)
     * ========================================================================= */

    /**
     * تنظيف وتنسيق رقم الهاتف الدولي بذكاء مع دعم اليمن (967) والأرقام الدولية دون تكرار
     */
    public function formatNumber(string $phone): string
    {
        // إزالة كافة الرموز غير الرقمية
        $cleanPhone = preg_replace('/[^\d]/', '', $phone);

        if (empty($cleanPhone)) {
            return '';
        }

        // إزالة بادئة الصفرين الدوليين 00
        if (str_starts_with($cleanPhone, '00')) {
            $cleanPhone = substr($cleanPhone, 2);
        }

        // إزالة تكرار المفتاح اليمني 967967 إن وجد
        if (str_starts_with($cleanPhone, '967967')) {
            $cleanPhone = substr($cleanPhone, 3);
        }

        // إزالة تكرار المفتاح السعودي 966966 إن وجد
        if (str_starts_with($cleanPhone, '966966')) {
            $cleanPhone = substr($cleanPhone, 3);
        }

        // معالجة الأرقام المحلية التي تبدأ بصفر (مثلاً 077... تصبح 96777...)
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '967' . substr($cleanPhone, 1);
        }
        // إذا كان الرقم بطول 9 أرقام ويبدأ بـ 7 فهو رقم يمني محلي
        elseif (strlen($cleanPhone) === 9 && str_starts_with($cleanPhone, '7')) {
            $cleanPhone = '967' . $cleanPhone;
        }
        // إذا كان الرقم لا يبدأ بـ 967 ولا 966 وطوله أقل أو يساوي 9
        elseif (!str_starts_with($cleanPhone, '967') && !str_starts_with($cleanPhone, '966') && strlen($cleanPhone) <= 9) {
            $cleanPhone = '967' . $cleanPhone;
        }

        return $cleanPhone;
    }

    /**
     * الاسم البديل المستخدم في الكود السابق للتوافق
     */
    public function formatPhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        return $this->formatNumber($phone);
    }

    /* =========================================================================
     * 4. دوال توليد الروابط والكشوفات (Legacy & Manifest Helpers)
     * ========================================================================= */

    /**
     * توليد رابط مباشر للمحادثة على واتساب
     */
    public function createWhatsAppLink(?string $phone, ?string $message): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $formattedPhone = $this->formatNumber($phone);
        $encodedMessage = urlencode($message ?? '');

        return "https://api.whatsapp.com/send?phone={$formattedPhone}&text={$encodedMessage}";
    }

    public function getSenderLink(Shipment $shipment): ?string
    {
        $message = $this->createSenderMessage($shipment);
        if (!$message || !$shipment->senderCustomer?->phone) {
            return null;
        }

        return $this->createWhatsAppLink($shipment->senderCustomer->phone, $message);
    }

    public function getReceiverLink(Shipment $shipment): ?string
    {
        $message = $this->createReceiverMessage($shipment);
        if (!$message || !$shipment->receiverCustomer?->phone) {
            return null;
        }

        return $this->createWhatsAppLink($shipment->receiverCustomer->phone, $message);
    }

    public function createSenderMessage(Shipment $shipment): ?string
    {
        $currentDateTime = now()->format('Y-m-d | h:i A');

        $message = "🌟 *مرحباً بك في الزاجل للنقل السريع* 🌟\n"
            . "━━━━━━━━━━━━━━━━━━━━━━━━\n\n"
            . "📦 *تأكيد استلام طردك*\n\n"
            . "🔢 *رقم التتبع:* {$shipment->tracking_number}";

        if ($shipment->senderCustomer && $shipment->senderCustomer->name) {
            $message .= "\n\n👤 *المرسل:* {$shipment->senderCustomer->name}";
            if ($shipment->senderCustomer->phone) {
                $message .= "\n📞 *جوال المرسل:* {$shipment->senderCustomer->phone}";
            }
        }

        if ($shipment->senderBranch && $shipment->senderBranch->name) {
            $message .= "\n📍 *من فرع:* {$shipment->senderBranch->name}";
        }

        if ($shipment->receiverCustomer && $shipment->receiverCustomer->name) {
            $message .= "\n\n👤 *المستلم:* {$shipment->receiverCustomer->name}";
            if ($shipment->receiverCustomer->phone) {
                $message .= "\n📞 *جوال المستلم:* {$shipment->receiverCustomer->phone}";
            }
        }

        if ($shipment->receiverBranch && $shipment->receiverBranch->name) {
            $message .= "\n📍 *إلى فرع:* {$shipment->receiverBranch->name}";
        }

        if ($shipment->package_type) {
            $message .= "\n\n📦 *نوع الطرد:* {$shipment->package_type}";
        }

        if ($shipment->weight) {
            $message .= "\n⚖️ *الوزن:* {$shipment->weight} كجم";
        }

        if ($shipment->payment_method === 'cod' && $shipment->cod_amount > 0) {
            $message .= "\n💰 *مبلغ الدفع عند الاستلام:* " . number_format($shipment->cod_amount, 2) . ' ر.ي';
        }

        $pdfService = $this->getSendPDFService();
        $pdfLink = $pdfService ? $pdfService->generateAndUploadManifest($shipment, 'pages.shipment.invoice', 'shipment', 'P') : null;

        if (!$pdfLink) {
            return null;
        }

        $message .= "\n\n📅 *تاريخ الشحن:* {$currentDateTime}\n"
            . "✅ *الحالة:* تم استلام الطرد وجاري التجهيز\n"
            . "🔗 *رابط السند (PDF):*\n{$pdfLink}\n"
            . "━━━━━━━━━━━━━━━━━━━━━━━━\n\n"
            . "🏢 *مكتب الزاجل - فرع المكلا*\n"
            . "الموقع: أربعين شقة - بجانب بنك الإمجاد\n"
            . "📞 للتواصل: 774996316 | 772038561 | 735637947\n\n"
            . "🔍 *للتتبع:* تواصل معنا على الأرقام أعلاه\n\n"
            . "⚠️ *تنبيهات هامة:*\n"
            . "• غير مسؤولين عن الإجراءات الأمنية\n"
            . "• داخل الطرد غير مسؤولين عن الأشياء الثمينة\n"
            . "• غير مسؤولين عن بقاء الطرود اكثر من شهر\n"
            . "• غير مسؤولين عن الحريق وحوادث السير\n"
            . "• يرجى التأكد من بيانات السند\n\n"
            . "شكراً لثقتك بنا! 💚";

        return $message;
    }

    public function createReceiverMessage(Shipment $shipment): ?string
    {
        $currentDateTime = now()->format('Y-m-d | h:i A');

        $message = "🌟 *مرحباً بك في الزاجل للنقل السريع* 🌟\n"
            . "━━━━━━━━━━━━━━━━━━━━━━━━\n\n"
            . "📦 *إشعار وصول طرد*";

        if ($shipment->receiverCustomer && $shipment->receiverCustomer->name) {
            $message .= "\n\nعزيزي/عزيزتي *{$shipment->receiverCustomer->name}* 👋";
        }

        $message .= "\n\n🔢 *رقم التتبع:* {$shipment->tracking_number}";

        if ($shipment->senderCustomer && $shipment->senderCustomer->name) {
            $message .= "\n\n👤 *المرسل:* {$shipment->senderCustomer->name}";
            if ($shipment->senderCustomer->phone) {
                $message .= "\n📞 *جوال المرسل:* {$shipment->senderCustomer->phone}";
            }
        }

        if ($shipment->senderBranch && $shipment->senderBranch->name) {
            $message .= "\n📍 *من فرع:* {$shipment->senderBranch->name}";
        }

        if ($shipment->receiverCustomer && $shipment->receiverCustomer->name) {
            $message .= "\n\n👤 *المستلم:* {$shipment->receiverCustomer->name}";
            if ($shipment->receiverCustomer->phone) {
                $message .= "\n📞 *جوال المستلم:* {$shipment->receiverCustomer->phone}";
            }
        }

        if ($shipment->receiverBranch && $shipment->receiverBranch->name) {
            $message .= "\n📍 *إلى فرع:* {$shipment->receiverBranch->name}";
        }

        if ($shipment->package_type) {
            $message .= "\n\n📊 *نوع الطرد:* {$shipment->package_type}";
        }

        if ($shipment->weight) {
            $message .= "\n⚖️ *الوزن:* {$shipment->weight} كجم";
        }

        $paymentMethodText = [
            'cash'   => '💳 مدفوع مسبقاً',
            'cod'    => '💵 دفع عند الاستلام',
            'online' => '💻 مدفوع إلكترونياً',
        ][$shipment->payment_method] ?? '💳 مدفوع';

        $message .= "\n💸 *طريقة الدفع:* {$paymentMethodText}";

        if ($shipment->payment_method === 'cod' && $shipment->cod_amount > 0) {
            $message .= "\n💰 *المبلغ المطلوب:* " . number_format($shipment->cod_amount) . ' ر.ي';
        }

        $pdfService = $this->getSendPDFService();
        $pdfLink = $pdfService ? $pdfService->generateAndUploadManifest($shipment, 'pages.shipment.invoice', 'shipment', 'P') : null;

        if (!$pdfLink) {
            return null;
        }

        $message .= "\n\n📅 *تاريخ الشحن:* {$currentDateTime}\n"
            . "✅ *الحالة:* في الطريق إليك\n\n"
            . "🔗 *رابط السند (PDF):*\n{$pdfLink}\n"
            . "⏱️ *التوصيل المتوقع:* خلال 24-48 ساعة عمل\n"
            . "━━━━━━━━━━━━━━━━━━━━━━━━\n\n"
            . "📍 *مكتب الزاجل - فرع المكلا*\n"
            . "الموقع: أربعين شقة - بجانب بنك الإمجاد\n"
            . "📞 للتواصل: 774996316 | 772038561 | 735637947\n\n"
            . "🔍 *للتتبع:* تواصل معنا على الأرقام أعلاه\n\n"
            . "⚠️ *تنبيهات هامة:*\n"
            . "• يرجى التواجد لاستلام الطرد\n"
            . "• غير مسؤولين عن الإجراءات الأمنية\n"
            . "• داخل الطرد غير مسؤولين عن الأشياء الثمينة\n"
            . "• غير مسؤولين عن بقاء الطرودأكثر من شهر\n"
            . "• غير مسؤولين عن الحريق وحوادث السير\n"
            . "• يرجى التأكد من بيانات السند\n\n"
            . "نتمنى لك يوماً سعيداً! 💚";

        return $message;
    }

    public function getBranchManifestLink($package, $branchCode = null): ?string
    {
        if ($branchCode) {
            $branchShipments = $package->shipments->where('receiver_branch_code', $branchCode);
            $branch = $branchShipments->first()?->receiverBranch;
            $shipmentCount = $branchShipments->count();
            $totalAmount = $branchShipments->sum('total_amount');
        } else {
            $branch = $package->shipments->first()?->receiverBranch;
            $shipmentCount = $package->shipments->count();
            $totalAmount = $package->shipments->sum('total_amount');
        }

        $phone = $branch?->phone ?? null;

        if (!$phone || !$branch) {
            return null;
        }

        $pdfService = $this->getSendPDFService();
        $pdfLink = $pdfService ? $pdfService->generateAndUploadManifest($package, 'pages.shipmentpackage.manifest_pdf') : null;

        if (!$pdfLink) {
            return null;
        }

        $currentDateTime = now()->format('Y-m-d | h:i A');

        $message = "🌟 *الزاجل للنقل السريع* 🌟\n"
            . "━━━━━━━━━━━━━━━━━━━━━━━━\n\n"
            . "📋 *كشف الحمولة للفرع*\n\n"
            . "🆔 *رقم الرحلة:* {$package->tracking_number}\n"
            . "🏢 *المكتب:* {$branch->name}\n"
            . "📦 *عدد الطرود:* {$shipmentCount} طرد\n"
            . "💰 *إجمالي المبالغ:* " . number_format($totalAmount) . " ر.ي\n\n"
            . "📅 *التاريخ والوقت:* {$currentDateTime}\n"
            . "✅ *الحالة:* جاهز للمراجعة\n"
            . "━━━━━━━━━━━━━━━━━━━━━━━━\n\n"
            . "🔗 *رابط الكشف (PDF):*\n{$pdfLink}\n\n"
            . "📍 *مكتب الزاجل - فرع المكلا*\n"
            . "الموقع: أربعين شقة - بجانب بنك الإمجاد\n"
            . "📞 للتواصل: 774996316 | 772038561 | 735637947\n\n"
            . "⚠️ *يرجى:*\n"
            . "• مراجعة الكشف والتأكد من كافة التفاصيل\n"
            . "• التأكد من بيانات السند قبل المغادرة\n"
            . "• التواصل فوراً في حال وجود أي استفسار\n\n"
            . "شكراً لتعاونكم 💚";

        return $this->createWhatsAppLink($phone, $message);
    }

    public function getDriverManifestLink($package): ?string
    {
        $phone = $package->driver_phone ?? null;

        if (!$phone) {
            return null;
        }

        $pdfService = $this->getSendPDFService();
        $pdfLink = $pdfService ? $pdfService->generateAndUploadManifest($package, 'pages.shipmentpackage.manifest_driver_pdf') : null;

        if (!$pdfLink) {
            return null;
        }

        $currentDateTime = now()->format('Y-m-d | h:i A');
        $totalParcels = $package->shipments->count();
        $totalAmount = $package->shipments->sum('total_amount');

        $message = "🌟 *الزاجل للنقل السريع* 🌟\n"
            . "━━━━━━━━━━━━━━━━━━━━━━━━\n\n"
            . "🚚 *كشف الحمولة للسائق*\n\n"
            . "🆔 *رقم الرحلة:* {$package->tracking_number}\n"
            . "👤 *السائق:* {$package->driver_name}\n"
            . "📦 *عدد الطرود:* {$totalParcels} طرد\n"
            . "💰 *إجمالي المبالغ:* " . number_format($totalAmount) . " ر.ي\n\n"
            . "📅 *التاريخ والوقت:* {$currentDateTime}\n"
            . "✅ *الحالة:* جاهز للتوصيل\n"
            . "━━━━━━━━━━━━━━━━━━━━━━━━\n\n"
            . "🔗 *رابط الكشف (PDF):*\n{$pdfLink}\n\n"
            . "📍 *مكتب الزاجل - فرع المكلا*\n"
            . "الموقع: أربعين شقة - بجانب بنك الإمجاد\n"
            . "📞 للتواصل: 774996316 | 772038561 | 735637947\n\n"
            . "⚠️ *تعليمات مهمة:*\n"
            . "• التحقق من كافة الطرود قبل البدء\n"
            . "• التأكد من بيانات كل طرد\n"
            . "• الالتزام بمواعيد التوصيل\n"
            . "• التواصل فوراً في حال أي طارئ\n\n"
            . "بالتوفيق في رحلتك! 🚛💚";

        return $this->createWhatsAppLink($phone, $message);
    }

    protected function getSendPDFService(): ?SendPDFService
    {
        if ($this->sendPDFService) {
            return $this->sendPDFService;
        }

        try {
            return app(SendPDFService::class);
        } catch (\Throwable $e) {
            return null;
        }
    }
}