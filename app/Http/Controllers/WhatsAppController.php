<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\ShipmentPackage;
use App\Services\WhatsAppService;
use App\Classes\WebResponseClass;

class WhatsAppController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
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
}
