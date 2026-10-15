<?php

namespace App\Services\WhatsApp\Messages;

use App\Interfaces\WhatsAppMessageInterface;
use App\Models\Shipment;
use Illuminate\Database\Eloquent\Model;

class PriceAgreementMessage implements WhatsAppMessageInterface
{
    public function getPhoneNumber(Model $entity): ?string
    {
        if (!$entity instanceof Shipment) {
            return null;
        }

        // إشعار تسعير الطرد يوجه للمستلم بالدرجة الأولى للاتفاق على السعر، أو المرسل كبديل
        return $entity->receiverCustomer?->phone ?? $entity->senderCustomer?->phone;
    }

    public function getReceiptType(): ?string
    {
        return 'sender';
    }

    public function getMessageBody(Model $entity, ?string $receiptUrl): string
    {
        if (!$entity instanceof Shipment) {
            return "بيانات الشحنة غير صالحة.";
        }

        // 👤 بيانات المرسل والمستلم والسند
        $name          = $entity->receiverCustomer?->name ?? 'عميلنا العزيز';
        $receiverPhone = $entity->receiverCustomer?->phone ?? $entity->receiver_phone ?? 'غير متوفر';
        $senderName    = $entity->senderCustomer?->name ?? 'غير محدد';
        $senderPhone   = $entity->senderCustomer?->phone ?? $entity->sender_phone ?? 'غير متوفر';
        $bondNumber    = $entity->bond_number ?? $entity->code ?? $entity->id ?? 'غير متوفر';
        $packageType   = $entity->package_type ?? 'غير محدد';
        $parcelsCount  = $entity->parcels_quantity ?? 1;

        // 📅 تجهيز اليوم والتاريخ والوقت بالعربية بتوقيت اليمن
        $dateObj = $entity->created_at ?? now();
        if ($dateObj instanceof \Carbon\Carbon || $dateObj instanceof \Carbon\CarbonInterface) {
            $dateObj = $dateObj->copy()->timezone('Asia/Aden');
        } else {
            $dateObj = \Carbon\Carbon::parse($dateObj)->timezone('Asia/Aden');
        }

        $days = [
            'Sunday'    => 'الأحد',
            'Monday'    => 'الإثنين',
            'Tuesday'   => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday'  => 'الخميس',
            'Friday'    => 'الجمعة',
            'Saturday'  => 'السبت',
        ];
        $dayName       = $days[$dateObj->format('l')] ?? 'اليوم';
        $dateFormatted = $dateObj->format('d-m-Y');
        $timePeriod    = $dateObj->format('A') === 'AM' ? 'صباحاً' : 'مساءً';
        $timeFormatted = $dateObj->format('h:i') . ' ' . $timePeriod;

        // 🔗 رابط السند الإلكتروني
        if (!$receiptUrl) {
            try {
                $receiptUrl = route('receipt.generate', ['type' => 'sender', 'id' => $entity->uuid ?? $entity->id]);
            } catch (\Throwable $e) {
                $receiptUrl = null;
            }
        }

        // 💡 جلب اسم مكتب الإرسال
        $senderOffice = null;
        if ($entity->senderBranch) {
            $rawName = trim(str_replace(['مكتب عالم القمة', 'مكتب'], '', $entity->senderBranch->name));
            $branchTitle = ($entity->senderBranch->is_main ? 'المركز الرئيسي/ ' : '') . $rawName;
            $senderOffice = 'مكتب عالم القمة ' . $branchTitle;
        } elseif ($entity->senderOfficeBranch) {
            $rawOffice = $entity->senderOfficeBranch->office?->name ?? 'عالم القمة';
            $senderOffice = 'مكتب ' . $rawOffice . ' ' . $entity->senderOfficeBranch->name;
        }
        $senderPlace = $senderOffice ?? $entity->senderBranch?->name ?? $entity->senderOfficeBranch?->name ?? 'مكتب عالم القمة عدن';
        $senderPlace = rtrim($senderPlace, '.') . '.';

        // 💡 جلب اسم مكتب التسليم
        $receiverOffice = null;
        if ($entity->receiverBranch) {
            $rawName = trim(str_replace(['مكتب عالم القمة', 'مكتب'], '', $entity->receiverBranch->name));
            $branchTitle = ($entity->receiverBranch->is_main ? 'المركز الرئيسي/ ' : '') . $rawName;
            $receiverOffice = 'مكتب عالم القمة ' . $branchTitle;
        } elseif ($entity->receiverOfficeBranch) {
            $rawOffice = $entity->receiverOfficeBranch->office?->name ?? 'عالم القمة';
            $receiverOffice = 'مكتب ' . $rawOffice . ' ' . $entity->receiverOfficeBranch->name;
        }
        $deliveryPlace = $receiverOffice ?? $entity->receiverBranch?->name ?? $entity->receiverOfficeBranch?->name ?? 'مكتب عالم القمة المركز الرئيسي/ سيئون';
        $deliveryPlace = rtrim($deliveryPlace, '.') . '.';

        // ✅ حالة الطرد
        $statusText = match ($entity->status) {
            'received_at_branch' => 'وصل الفرع وجاهز للاستلام.',
            'delivered'          => 'تم التسليم بنجاح.',
            default              => 'قيد العمل على معالجتة وإرسالة.',
        };

        // 💸 طريقة الدفع وتكلفة النقل
        $paymentMethods = [
            'prepaid'         => 'مدفوع مقدماً',
            'cod'             => 'الدفع عند الاستلام',
            'partial_payment' => 'دفع جزئي',
            'customer_credit' => 'آجل (ذمة)'
        ];

        if (!$entity->is_price_agreed) {
            $shippingCost  = 'لم يتفق عليها بعد.';
            $paymentMethod = $paymentMethods[$entity->payment_method] ?? ($entity->payment_method ?: 'يتم الاتفاق لاحقاً');
        } elseif ($entity->total_amount > 0) {
            $shippingCost  = number_format($entity->total_amount, 0) . ' ريال';
            $paymentMethod = $paymentMethods[$entity->payment_method] ?? ($entity->payment_method ?: 'نقدي');
        } else {
            $shippingCost  = 'لم يتفق عليها بعد.';
            $paymentMethod = $paymentMethods[$entity->payment_method] ?? ($entity->payment_method ?: 'لم تحدد بعد');
        }

        // 📝 بناء نص الرسالة بحسب النموذج المعتمد
        $msg  = "السلام عليكم ورحمة الله وبركاته\n\n";
        $msg .= "الأخ/ *{$name}*       المحترم.\n";
        $msg .= "تحية طيبة وبعد:\n\n";
        $msg .= "       🌟 *مرحباً بك في عالم القمة للنقل السريع* 🌟\n";
        $msg .= "━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $msg .= "                                 >📦 *إشعار إستلام طرد*<\n";
        $msg .= "📅*اليوم:* ~{$dayName}~ | *~التاريخ~*:{$dateFormatted} | *~الوقت~:* {$timeFormatted}\n";
        $msg .= "🔢 *رقم السند:*  {$bondNumber}\n";

        if ($receiptUrl) {
            $msg .= "🔗 *رابط السند (PDF):*\n{$receiptUrl}\n";
        }

        $msg .= "👤 *المرسل:* {$senderName}\n";
        $msg .= "📞 *جوال المرسل:* {$senderPhone}\n";
        $msg .= "👤 *المستلم:* {$name}\n";
        $msg .= "📞 *جوال المستلم:* {$receiverPhone}\n";
        $msg .= "📊 *نوع الطرد:* {$packageType}\n";
        $msg .= "🔢 *عدد الطرد*: {$parcelsCount}\n";
        $msg .= "*مكتب الإرسال*: {$senderPlace}\n";
        $msg .= "*مكتب التسليم*: {$deliveryPlace}\n";
        $msg .= "✅ *حالة الطرد:* {$statusText}\n";
        $msg .= "💸 *طريقة الدفع:*   {$paymentMethod}\n";
        $msg .= "*تكلفة النقل*:  {$shippingCost}\n";
        $msg .= "*~تواصل معنا لتسعيرها بالاتصال اوعلى خدمة الواتساب على الارقام مكتب عالم القمة فرع عدن~*\n";
        $msg .= "*موبايل1:*📲782947557📲\n";
        $msg .= "*موبايل2:*📲778132137📲\n";
        $msg .= "*الهاتف:*  ☎️02383444☎️\n";
        $msg .= "~*اوسنتواصل معك في اقرب وقت.*~\n";
        $msg .= "⏱️ *التوصيل المتوقع:* خلال 24-48 ساعة عمل\n";
        $msg .= "━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $msg .= "📍 *مكتب عالم القمة للنقل السريع*📍\n";
        $msg .= "المركز الرئيسي/ سيئون:شارع الجزائر مقابل شركة الثور للصرافة بجانب الزبيدي اخوان للكهربائيات للتواصل/ 📲782806886📲 📲770098841📲\n";
        $msg .= " ☎️105488070☎️\n";
        $msg .= " *فروعنا*:\n";
        $msg .= "*فرع عدن* الشيخ عثمان-القاهرة قبل بنك التضامن في الجهة المقابلة على دخلة سور مدرسة إدريس حنبلة بجانب بقالة القمة للتواصل/\n";
        $msg .= "📲782947557📲\n";
        $msg .= "📲778132137📲\n";
        $msg .= "☎️02383444☎️\n";
        $msg .= "🔍 *لمتابعة طردك:* تواصل معنا على الأرقام أعلاه.\n";
        $msg .= "⚠️ *تنبيهات هامة:*\n";
        $msg .= "1- اي سند لايحمل ختم المكتب غير مقبول.\n";
        $msg .= "2- المكتب غير مسؤول عن محتويات الطرد ولا تقبل إي مراجعة بعد(1)يوم من تاريخ إستلامه.\n";
        $msg .= "3- المكتب غير مسؤول عن الإجراءت الأمنية والجمركية الخارجة عن إرادتنا.\n";
        $msg .= "4- المكتب غير مسؤول عن الطرد بعد شهر من وصوله إلى مكان إستلامة\n";
        $msg .= "5- نحن غير مسؤولين عن الحريق وحوادث السير.\n";
        $msg .= "6- الرجاء التأكد من بيانات السند قبل الإرسال.\n";
        $msg .= "7- لايعتبر هذا السند إثبات لإستلام الإرسالية لذا يجب إحضار البطاقة الشخصية أو جواز السفر.\n\n";
        $msg .= "💚نتمنى لك يوماً سعيداً! 💚";

        return $msg;
    }
}
