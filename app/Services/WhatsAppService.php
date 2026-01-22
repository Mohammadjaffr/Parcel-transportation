<?php

// app/Services/WhatsAppService.php

namespace App\Services;

use App\Models\Shipment;

class WhatsAppService
{
    public function createWhatsAppLink($phone, $message)
    {
        $formattedPhone = $this->formatPhone($phone);
        $encodedMessage = urlencode($message);

        return "https://api.whatsapp.com/send?phone={$formattedPhone}&text={$encodedMessage}";
    }

    private function formatPhone($phone)
    {
        // 1. تنظيف الرقم من أي رموز وحروف
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // 2. إذا كان الرقم فارغاً، نرجعه كما هو (أو نرجع null)
        if (empty($phone)) {
            return null;
        }

        // 3. معالجة حالات اليمن (أو دولتك)

        // إذا بدأ بـ 0 (مثال: 077) -> نحذف الصفر ونضيف 967
        if (str_starts_with($phone, '0')) {
            $phone = '967'.substr($phone, 1);
        }
        // إذا بدأ بـ 7 مباشرة وكان طوله 9 أرقام (مثال: 771234567) -> نضيف 967
        elseif (strlen($phone) === 9 && str_starts_with($phone, '7')) {
            $phone = '967'.$phone;
        }
        // إذا لم يبدأ بـ 967 (ولم تنطبق الشروط السابقة)، نضيفها احتياطاً
        // (تنبيه: هذا يعتمد على منطق نظامك، هل كل الأرقام يمنية؟)
        elseif (! str_starts_with($phone, '967')) {
            $phone = '967'.$phone;
        }

        return $phone;
    }

    public function getSenderLink(Shipment $shipment)
    {
        $message = $this->createSenderMessage($shipment);

        return $this->createWhatsAppLink($shipment->senderCustomer->phone, $message);
    }

    public function getReceiverLink(Shipment $shipment)
    {
        $message = $this->createReceiverMessage($shipment);

        return $this->createWhatsAppLink($shipment->receiverCustomer->phone, $message);
    }

    public function createSenderMessage(Shipment $shipment)
    {
        $codText = '';
        if ($shipment->payment_method === 'cod' && $shipment->cod_amount > 0) {
            $codText = "\n💰 مبلغ الدفع عند الاستلام: ".number_format($shipment->cod_amount, 2).' ريال';
        }

        $branchName = $shipment->branch ? $shipment->branch->name : 'المكتب الرئيسي';

        return "📦 *تأكيد شحن الطرد*

📮 *رقم التتبع:* {$shipment->id}
👤 *المستلم:* {$shipment->receiver_name}
📍 *مدينة المستلم:* {$shipment->to_city}
🏢 *المكتب:* {$branchName}
📊 *نوع الطرد:* {$shipment->package_type}
⚖️ *الوزن:* {$shipment->weight} كجم".
        ($shipment->notes ? "\n📝 *ملاحظات:* {$shipment->notes}" : '').'
🕒 *تاريخ الشحن:* '.now()->format('Y-m-d H:i')."
✅ *الحالة:* تم استلام الطرد{$codText}

شكراً لثقتك بنا! 🌟";
    }

    public function createReceiverMessage(Shipment $shipment)
    {
        $codText = '';
        if ($shipment->payment_method === 'cod' && $shipment->cod_amount > 0) {
            $codText = "\n💰 *مطلوب منك:* ".number_format($shipment->cod_amount, 2).' ريال (دفع عند الاستلام)';
        }

        $paymentMethodText = [
            'cash' => '💳 مدفوع',
            'cod' => '💵 دفع عند الاستلام',
            'online' => '💻 مدفوع إلكترونياً',
        ][$shipment->payment_method] ?? '💳 مدفوع';

        $branchName = $shipment->branch ? $shipment->branch->name : 'المكتب الرئيسي';

        return "📦 *إشعار استلام طرد*

مرحباً {$shipment->receiver_name} 👋

📮 *رقم التتبع:* {$shipment->tracking_number}
👤 *المرسل:* {$shipment->sender_name}
📍 *مدينة المرسل:* {$shipment->from_city}
🏢 *المكتب:* {$branchName}
📊 *نوع الطرد:* {$shipment->package_type}
⚖️ *الوزن:* {$shipment->weight} كجم
💸 *طريقة الدفع:* {$paymentMethodText}{$codText}".
        ($shipment->notes ? "\n📝 *ملاحظات:* {$shipment->notes}" : '').'
⏰ *التوقيت:* سيتم التوصيل خلال 24-48 ساعة عمل

يرجى التأكد من وجود شخص لاستلام الطرد.
يمكنك تتبع شحنتك عبر الرقم أعلاه. 🔍';
    }

    /**
     * Get WhatsApp link for Branch Manifest
     * @param mixed $package ShipmentPackage instance
     * @param string|null $branchCode Optional branch code to filter shipments
     */
    public function getBranchManifestLink($package, $branchCode = null)
    {
        // إذا تم تحديد branchCode، نفلتر الطرود حسب هذا الفرع
        if ($branchCode) {
            $branchShipments = $package->shipments->where('receiver_branch_code', $branchCode);
            $branch = $branchShipments->first()?->receiverBranch;
            $shipmentCount = $branchShipments->count();
            $totalAmount = $branchShipments->sum('total_amount');
        } else {
            // إذا لم يُحدد، نأخذ الفرع الأول (للتوافق مع الكود القديم)
            $branch = $package->shipments->first()?->receiverBranch;
            $shipmentCount = $package->shipments->count();
            $totalAmount = $package->shipments->sum('total_amount');
        }
        
        $phone = $branch?->phone ?? null;

        if (! $phone || ! $branch) {
            return null;
        }

        $pdfLink = route('shipmentpackage.printD', $package->id);

        $message = "📋 *كشف الحمولة للفرع*

🆔 *رقم الرحلة:* {$package->tracking_number}
🏢 *المكتب:* {$branch->name}
📦 *عدد الطرود:* {$shipmentCount}
💰 *إجمالي المبالغ:* ".number_format($totalAmount)." ر.ي

🔗 *رابط الكشف (PDF):*
{$pdfLink}

يرجى مراجعة الكشف والتأكد من كافة التفاصيل. ✅";

        return $this->createWhatsAppLink($phone, $message);
    }

    /**
     * Get WhatsApp link for Driver Manifest
     */
    public function getDriverManifestLink($package)
    {
        $phone = $package->driver_phone ?? null;

        if (! $phone) {
            return null;
        }

        $pdfLink = route('shipmentpackage.print', $package->id);

        $message = "🚚 *كشف الحمولة للسائق*

🆔 *رقم الرحلة:* {$package->tracking_number}
👤 *السائق:* {$package->driver_name}
📦 *عدد الطرود:* {$package->shipments->count()}
💰 *إجمالي المبالغ:* ".number_format($package->shipments->sum('total_amount'))." ر.ي

🔗 *رابط الكشف (PDF):*
{$pdfLink}

يرجى التحقق من كافة الطرود قبل البدء بالتوصيل. 🚛✅";

        return $this->createWhatsAppLink($phone, $message);
    }
}
