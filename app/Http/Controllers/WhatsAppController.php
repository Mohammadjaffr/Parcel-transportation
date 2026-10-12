<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\ShipmentPackage;
use App\Services\WhatsAppService;
use App\Services\SendPDFService;
use App\Classes\WebResponseClass;
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
            $shipment = Shipment::with(['senderCustomer', 'receiverCustomer', 'senderBranch.app', 'receiverBranch.app', 'receiverOfficeBranch.office'])->findOrFail($id);

            // تجهيز رابط الواتساب البديل (wa.me)
            $fallbackLink = WhatsAppLinkService::generate($shipment, 'sender');

            // تحميل إعدادات الواتساب الخاصة بالتطبيق أو الفرع
            if ($shipment->app) {
                $this->whatsAppService->loadAppConfig($shipment->app);
            } elseif (auth()->user()?->app) {
                $this->whatsAppService->loadAppConfig(auth()->user()->app);
            } else {
                $configuredApp = \App\Models\App::whereNotNull('whatsapp_api_key')->where('whatsapp_api_key', '!=', '')->first();
                if ($configuredApp) {
                    $this->whatsAppService->loadAppConfig($configuredApp);
                }
            }

            // التحقق من وجود رقم هاتف المرسل
            $phone = $shipment->senderCustomer->phone ?? null;
            if (!$phone) {
                return response()->json([
                    'success' => false,
                    'message' => 'رقم هاتف المرسل غير متوفر.',
                ], 422);
            }

            // التحقق من تهيئة خدمة الواتساب
            if (!$this->whatsAppService->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'fallback' => true,
                    'whatsapp_link' => $fallbackLink,
                    'message' => 'خدمة الواتساب التلقائية غير مهيأة، جاري التحويل للمحادثة المباشرة...',
                ]);
            }

            // 1. توليد رابط السند وتجهيز نص الرسالة
            $receiptUrl = route('receipt.generate', [
                'type' => 'sender',
                'id'   => $shipment->uuid ?? $shipment->id,
            ]);

            $senderMessage = new \App\Services\WhatsApp\Messages\SenderMessage();
            $message = $senderMessage->getMessageBody($shipment, $receiptUrl);

            // 2. إرسال الرسالة النصية المباشرة عبر واتساب API
            $result = $this->whatsAppService->sendText($phone, $message);

            // 3. التحقق من نتيجة الإرسال - إذا فشل يتم التحويل للواتساب اليدوي
            if (isset($result['error']) && $result['error']) {
                \Illuminate\Support\Facades\Log::warning("WhatsApp sendText failed for shipment #{$id}, falling back to wa.me: " . ($result['message'] ?? ''));

                return response()->json([
                    'success' => false,
                    'fallback' => true,
                    'whatsapp_link' => $fallbackLink,
                    'message' => 'خدمة الواتساب غير متصلة حالياً، جاري فتح الواتساب كرسالة...',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال إشعار المرسل بنجاح عبر الواتساب ✅',
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("sendSenderPdf Error for shipment #{$id}: " . $e->getMessage());

            $fallbackLink = isset($shipment) ? WhatsAppLinkService::generate($shipment, 'sender') : null;

            return response()->json([
                'success' => false,
                'fallback' => !empty($fallbackLink),
                'whatsapp_link' => $fallbackLink,
                'message' => 'حدث خطأ، جاري فتح الواتساب للإرسال كرسالة...',
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
