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
        $currentDateTime = now()->format('Y-m-d | h:i A');

        $message = "🌟 *مرحباً بك في الزاجل للنقل السريع* 🌟
━━━━━━━━━━━━━━━━━━━━━━━━

📦 *تأكيد استلام طردك*

🔢 *رقم التتبع:* {$shipment->tracking_number}";

        // إضافة المرسل إذا كان موجود
        if ($shipment->senderCustomer && $shipment->senderCustomer->name) {
            $message .= "\n\n👤 *المرسل:* {$shipment->senderCustomer->name}";
            if ($shipment->senderCustomer->phone) {
                $message .= "\n📞 *جوال المرسل:* {$shipment->senderCustomer->phone}";
            }
        }

        // إضافة مدينة المرسل إذا كانت موجودة
        if ($shipment->senderBranch && $shipment->senderBranch->name) {
            $message .= "\n📍 *من فرع:* {$shipment->senderBranch->name}";
        }

        // إضافة المستلم إذا كان موجود
        if ($shipment->receiverCustomer && $shipment->receiverCustomer->name) {
            $message .= "\n\n👤 *المستلم:* {$shipment->receiverCustomer->name}";
            if ($shipment->receiverCustomer->phone) {
                $message .= "\n📞 *جوال المستلم:* {$shipment->receiverCustomer->phone}";
            }
        }

        // إضافة مدينة المستلم إذا كانت موجودة
        if ($shipment->receiverBranch && $shipment->receiverBranch->name) {
            $message .= "\n📍 *إلى فرع:* {$shipment->receiverBranch->name}";
        }

        // إضافة نوع الطرد إذا كان موجود
        if ($shipment->package_type) {
            $message .= "\n\n� *نوع الطرد:* {$shipment->package_type}";
        }

        // إضافة الوزن إذا كان موجود
        if ($shipment->weight) {
            $message .= "\n⚖️ *الوزن:* {$shipment->weight} كجم";
        }

        // إضافة مبلغ الدفع عند الاستلام إذا وجد
        if ($shipment->payment_method === 'cod' && $shipment->cod_amount > 0) {
            $message .= "\n💰 *مبلغ الدفع عند الاستلام:* ".number_format($shipment->cod_amount, 2).' ر.ي';
        }

        $message .= "\n\n📅 *تاريخ الشحن:* {$currentDateTime}
✅ *الحالة:* تم استلام الطرد وجاري التجهيز
━━━━━━━━━━━━━━━━━━━━━━━━

🏢 *مكتب الزاجل - فرع المكلا*
الموقع: أربعين شقة - بجانب بنك الإمجاد
📞 للتواصل: 774996316 | 772038561 | 735637947

🔍 *للتتبع:* تواصل معنا على الأرقام أعلاه

⚠️ *تنبيهات هامة:*
• غير مسؤولين عن الإجراءات الأمنية
• داخل الطرد غير مسؤولين عن الأشياء الثمينة
• غير مسؤولين عن بقاء الطرود اكثر من شهر
• غير مسؤولين عن الحريق وحوادث السير
• يرجى التأكد من بيانات السند

شكراً لثقتك بنا! 💚";

        return $message;
    }

    public function createReceiverMessage(Shipment $shipment)
    {
        $currentDateTime = now()->format('Y-m-d | h:i A');

        $message = '🌟 *مرحباً بك في الزاجل للنقل السريع* 🌟
━━━━━━━━━━━━━━━━━━━━━━━━

📦 *إشعار وصول طرد*';

        // اسم المستلم في التحية
        if ($shipment->receiverCustomer && $shipment->receiverCustomer->name) {
            $message .= "\n\nعزيزي/عزيزتي *{$shipment->receiverCustomer->name}* 👋";
        }

        $message .= "\n\n🔢 *رقم التتبع:* {$shipment->tracking_number}";

        // معلومات المرسل
        if ($shipment->senderCustomer && $shipment->senderCustomer->name) {
            $message .= "\n\n👤 *المرسل:* {$shipment->senderCustomer->name}";
            if ($shipment->senderCustomer->phone) {
                $message .= "\n📞 *جوال المرسل:* {$shipment->senderCustomer->phone}";
            }
        }

        if ($shipment->senderBranch && $shipment->senderBranch->name) {
            $message .= "\n📍 *من فرع:* {$shipment->senderBranch->name}";
        }

        // معلومات المستلم
        if ($shipment->receiverCustomer && $shipment->receiverCustomer->name) {
            $message .= "\n\n👤 *المستلم:* {$shipment->receiverCustomer->name}";
            if ($shipment->receiverCustomer->phone) {
                $message .= "\n📞 *جوال المستلم:* {$shipment->receiverCustomer->phone}";
            }
        }

        if ($shipment->receiverBranch && $shipment->receiverBranch->name) {
            $message .= "\n📍 *إلى فرع:* {$shipment->receiverBranch->name}";
        }

        // نوع الطرد والوزن
        if ($shipment->package_type) {
            $message .= "\n\n📊 *نوع الطرد:* {$shipment->package_type}";
        }

        if ($shipment->weight) {
            $message .= "\n⚖️ *الوزن:* {$shipment->weight} كجم";
        }

        // طريقة الدفع
        $paymentMethodText = [
            'cash' => '💳 مدفوع مسبقاً',
            'cod' => '💵 دفع عند الاستلام',
            'online' => '💻 مدفوع إلكترونياً',
        ][$shipment->payment_method] ?? '💳 مدفوع';

        $message .= "\n💸 *طريقة الدفع:* {$paymentMethodText}";

        // مبلغ الدفع عند الاستلام
        if ($shipment->payment_method === 'cod' && $shipment->cod_amount > 0) {
            $message .= "\n💰 *المبلغ المطلوب:* ".number_format($shipment->cod_amount, 2).' ر.ي';
        }

        $message .= "\n\n📅 *تاريخ الشحن:* {$currentDateTime}
✅ *الحالة:* في الطريق إليك
⏱️ *التوصيل المتوقع:* خلال 24-48 ساعة عمل
━━━━━━━━━━━━━━━━━━━━━━━━

📍 *مكتب الزاجل - فرع المكلا*
الموقع: أربعين شقة - بجانب بنك الإمجاد
📞 للتواصل: 774996316 | 772038561 | 735637947

🔍 *للتتبع:* تواصل معنا على الأرقام أعلاه

⚠️ *تنبيهات هامة:*
• يرجى التواجد لاستلام الطرد
• غير مسؤولين عن الإجراءات الأمنية
•  داخل الطرد غير مسؤولين عن الأشياء الثمينة
• غير مسؤولين عن بقاء الطرودأكثر من شهر
• غير مسؤولين عن الحريق وحوادث السير
• يرجى التأكد من بيانات السند

نتمنى لك يوماً سعيداً! 💚";

        return $message;
    }

    /**
     * Get WhatsApp link for Branch Manifest
     *
     * @param  mixed  $package  ShipmentPackage instance
     * @param  string|null  $branchCode  Optional branch code to filter shipments
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

        $pdfLink = asset('storage/manifests/Manifest-Branch-'.$package->tracking_number.'.pdf');
        $currentDateTime = now()->format('Y-m-d | h:i A');

        $message = "🌟 *الزاجل للنقل السريع* 🌟
━━━━━━━━━━━━━━━━━━━━━━━━

📋 *كشف الحمولة للفرع*

🆔 *رقم الرحلة:* {$package->tracking_number}
🏢 *المكتب:* {$branch->name}
📦 *عدد الطرود:* {$shipmentCount} طرد
💰 *إجمالي المبالغ:* ".number_format($totalAmount)." ر.ي

� *التاريخ والوقت:* {$currentDateTime}
✅ *الحالة:* جاهز للمراجعة
━━━━━━━━━━━━━━━━━━━━━━━━

�🔗 *رابط الكشف (PDF):*
{$pdfLink}

📍 *مكتب الزاجل - فرع المكلا*
الموقع: أربعين شقة - بجانب بنك الإمجاد
📞 للتواصل: 774996316 | 772038561 | 735637947

⚠️ *يرجى:*
• مراجعة الكشف والتأكد من كافة التفاصيل
• التأكد من بيانات السند قبل المغادرة
• التواصل فوراً في حال وجود أي استفسار

شكراً لتعاونكم 💚";

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

        $pdfLink = asset('storage/manifests/Manifest-Driver-'.$package->tracking_number.'.pdf');
        $currentDateTime = now()->format('Y-m-d | h:i A');
        $totalParcels = $package->shipments->count();
        $totalAmount = $package->shipments->sum('total_amount');

        $message = "🌟 *الزاجل للنقل السريع* 🌟
━━━━━━━━━━━━━━━━━━━━━━━━

🚚 *كشف الحمولة للسائق*

🆔 *رقم الرحلة:* {$package->tracking_number}
👤 *السائق:* {$package->driver_name}
📦 *عدد الطرود:* {$totalParcels} طرد
💰 *إجمالي المبالغ:* ".number_format($totalAmount)." ر.ي

� *التاريخ والوقت:* {$currentDateTime}
✅ *الحالة:* جاهز للتوصيل
━━━━━━━━━━━━━━━━━━━━━━━━

�🔗 *رابط الكشف (PDF):*
{$pdfLink}

📍 *مكتب الزاجل - فرع المكلا*
الموقع: أربعين شقة - بجانب بنك الإمجاد
📞 للتواصل: 774996316 | 772038561 | 735637947

⚠️ *تعليمات مهمة:*
• التحقق من كافة الطرود قبل البدء
• التأكد من بيانات كل طرد
• الالتزام بمواعيد التوصيل
• التواصل فوراً في حال أي طارئ

بالتوفيق في رحلتك! 🚛💚";

        return $this->createWhatsAppLink($phone, $message);
    }
}
