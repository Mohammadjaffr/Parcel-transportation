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
     * إرسال إشعار المرسل (رسالة نصية + رابط PDF) مباشرة عبر واتساب API
     * باستخدام sendText بدون فتح المتصفح
     */
    public function sendSenderPdf($id)
    {
        try {
            $shipment = Shipment::with(['senderCustomer', 'receiverCustomer', 'senderBranch', 'receiverBranch'])->findOrFail($id);

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

            // 1. توليد رابط السند (PDF)
            $receiptUrl = route('receipt.generate', [
                'type' => 'sender',
                'id'   => $shipment->uuid ?? $shipment->id,
            ]);

            // 2. تجهيز نص الرسالة (نفس محتوى SenderMessage)
            $senderMessage = new \App\Services\WhatsApp\Messages\SenderMessage();
            $message = $senderMessage->getMessageBody($shipment, $receiptUrl);

            // 3. إرسال الرسالة النصية عبر واتساب API باستخدام sendText
            $result = $this->whatsAppService->sendText($phone, $message);

            // 4. التحقق من نتيجة الإرسال
            if (isset($result['error']) && $result['error']) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل إرسال الرسالة عبر واتساب: ' . ($result['message'] ?? 'خطأ غير معروف'),
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال إشعار المرسل بنجاح ✅',
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("sendSenderPdf Error for shipment #{$id}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء الإرسال: ' . $e->getMessage(),
            ], 500);
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
