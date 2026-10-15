<?php

namespace App\Services\WhatsApp\Messages;

use App\Interfaces\WhatsAppMessageInterface;
use App\Models\Shipment;
use Illuminate\Database\Eloquent\Model;

class SenderMessage implements WhatsAppMessageInterface
{
    public function getPhoneNumber(Model $entity): ?string
    {
        if (!$entity instanceof Shipment) {
            return null;
        }
        return $entity->senderCustomer->phone ?? null;
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

        // بيانات المرسل والمستلم والسند
        $senderName    = $entity->senderCustomer?->name ?? 'عميلنا العزيز';
        $senderPhone   = $entity->senderCustomer?->phone ?? $entity->sender_phone ?? 'غير متوفر';
        $receiverName  = $entity->receiverCustomer?->name ?? 'غير محدد';
        $receiverPhone = $entity->receiverCustomer?->phone ?? $entity->receiver_phone ?? 'غير متوفر';
        $bondNumber    = $entity->bond_number ?? $entity->code ?? $entity->id ?? 'غير متوفر';
        $packageType   = $entity->package_type ?? 'غير محدد';
        $parcelsCount  = $entity->parcels_quantity ?? 1;

        // تجهيز اليوم والتاريخ والوقت بالعربية
        $dateObj = $entity->created_at ?? now();
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

        // جلب اسم مكتب الإرسال
        $senderOffice = null;
        if ($entity->senderBranch) {
            $appName = $entity->senderBranch->app?->name;
            $senderOffice = ($appName ? $appName . ' / ' : '') . $entity->senderBranch->name;
        } elseif ($entity->senderOfficeBranch) {
            $officeName = $entity->senderOfficeBranch->office?->name;
            $senderOffice = ($officeName ? $officeName . ' / ' : '') . $entity->senderOfficeBranch->name;
        }
        $senderPlace = $senderOffice ?? $entity->senderBranch?->name ?? $entity->senderOfficeBranch?->name ?? 'مكتب عالم القمة عدن';

        // جلب اسم مكتب التسليم
        $receiverOffice = null;
        if ($entity->receiverBranch) {
            $appName = $entity->receiverBranch->app?->name;
            $receiverOffice = ($appName ? $appName . ' / ' : '') . $entity->receiverBranch->name;
        } elseif ($entity->receiverOfficeBranch) {
            $officeName = $entity->receiverOfficeBranch->office?->name;
            $receiverOffice = ($officeName ? $officeName . ' / ' : '') . $entity->receiverOfficeBranch->name;
        }
        $branchName = $receiverOffice ?? $entity->receiverBranch?->name ?? $entity->receiverOfficeBranch?->name ?? 'مكتب عالم القمة المركز الرئيسي/ سيئون';

        // جلب مكان التسليم
        $deliveryPlace = $entity->delivery_place
            ?? $entity->delivery_address
            ?? $entity->receiver_address
            ?? $entity->destination
            ?? $entity->receiverBranch?->address
            ?? $entity->receiverBranch?->name
            ?? $branchName;

        // تحديد حالة الطرد
        $statusText = match ($entity->status) {
            'received_at_branch' => 'وصل الفرع وجاهز للاستلام.',
            'delivered'          => 'تم التسليم بنجاح.',
            default              => 'قيد العمل على معالجتة وإرسالة.',
        };

        // تعريب طريقة الدفع وتحديد تكلفة النقل
        $rawPaymentMethod = strtolower((string) ($entity->payment_method ?? ''));
        $translatedPayment = match ($rawPaymentMethod) {
            'prepaid', 'paid', 'cash'        => 'مقدم',
            'postpaid', 'cod', 'on_delivery' => 'عند الاستلام (على المستلم)',
            default                          => $entity->payment_method,
        };

        $totalAmount = (float) ($entity->total_amount ?? $entity->shipping_cost ?? $entity->price ?? 0);
        $amountDue   = (float) ($entity->amount_to_collect_from_receiver ?? 0);

        if (!$entity->is_price_agreed) {
            $paymentMethod = 'لم يتفق عليها بعد';
            $costText      = 'لم يتفق عليها بعد';
        } else {
            $paymentMethod = $translatedPayment ?: ($amountDue > 0 ? 'عند الاستلام (على المستلم)' : 'مقدم');
            $costText      = $totalAmount > 0 ? number_format($totalAmount) . ' ريال' : 'خالص';
        }

        // تعريف الأيقونات بترميز Unicode القياسي
        $star     = "\u{1F31F}"; // 🌟
        $box      = "\u{1F4E6}"; // 📦
        $calendar = "\u{1F4C5}"; // 📅
        $numbers  = "\u{1F522}"; // 🔢
        $linkIcon = "\u{1F517}"; // 🔗
        $userIcon = "\u{1F464}"; // 👤
        $phone    = "\u{1F4DE}"; // 📞
        $chart    = "\u{1F4CA}"; // 📊
        $check    = "\u{2705}";  // ✅
        $money    = "\u{1F4B8}"; // 💸
        $mobile   = "\u{1F4F2}"; // 📲
        $tel      = "\u{260E}\u{FE0F}"; // ☎️
        $timer    = "\u{23F1}\u{FE0F}"; // ⏱️
        $pin      = "\u{1F4CD}"; // 📍
        $search   = "\u{1F50D}"; // 🔍
        $warning  = "\u{26A0}\u{FE0F}"; // ⚠️
        $heart    = "\u{1F49A}"; // 💚
        $line     = str_repeat("\u{2501}", 24); // ━━━━━━━━━━━━━━━━━━━━━━━━

        // بناء نص الرسالة مطابقاً للتعديل 3
        $msg  = "السلام عليكم ورحمة الله وبركاته\n\n";
        $msg .= "الأخ/ *{$senderName}*       المحترم.\n";
        $msg .= "تحية طيبة وبعد:\n\n";
        $msg .= "       {$star} *مرحباً بك في عالم القمة للنقل السريع* {$star}\n";
        $msg .= "{$line}\n";
        $msg .= "                                 >{$box} *إشعار إستلام طرد*<\n";
        $msg .= "{$calendar}*اليوم:* ~{$dayName}~ | *~التاريخ~*:{$dateFormatted} | *~الوقت~:* {$timeFormatted}\n";
        $msg .= "{$numbers} *رقم السند:*  {$bondNumber} .\n";
        $msg .= "{$userIcon} *المرسل:* {$senderName} .\n";
        $msg .= "{$phone} *جوال المرسل:* {$senderPhone} .\n";
        $msg .= "{$userIcon} *المستلم:* {$receiverName} .\n";
        $msg .= "{$phone} *جوال المستلم:* {$receiverPhone} .\n";
        $msg .= "{$chart} *نوع الطرد:* {$packageType} .\n";
        $msg .= "{$numbers} *عدد الطرد*: {$parcelsCount} .\n";
        $msg .= "*مكتب الإرسال*: {$senderPlace}.\n";
        $msg .= "*مكتب التسليم*: {$branchName}.\n";
        $msg .= "*مكان التسليم*: {$deliveryPlace} ..\n";
        $msg .= "{$check} *حالة الطرد:* {$statusText}\n";
        $msg .= "{$money} *طريقة الدفع:*   {$paymentMethod} .\n";
        $msg .= "*تكلفة النقل*:  {$costText} .\n";
        $msg .= "*~للاستفسار الاتصال بنا او مراسلتنا على خدمة الواتساب على الارقام مكتب عالم القمة فرع عدن~*\n";
        $msg .= "*موبايل1:*{$mobile}782947557{$mobile}\n";
        $msg .= "*موبايل2:*{$mobile}778132137{$mobile}\n";
        $msg .= "*الهاتف:*  {$tel}02383444{$tel}\n";
        $msg .= "~*اوسنتواصل معك في اقرب وقت.*~\n";

        if ($receiptUrl) {
            $msg .= "\n{$linkIcon} *رابط السند (PDF):*\n{$receiptUrl}\n";
        }

        $msg .= "{$timer} *التوصيل المتوقع:* خلال 24-48 ساعة عمل\n";
        $msg .= "{$line}\n";
        $msg .= "{$pin} *مكتب عالم القمة للنقل السريع*{$pin}\n";
        $msg .= "المركز الرئيسي/ سيئون:شارع الجزائر مقابل شركة الثور للصرافة بجانب الزبيدي اخوان للكهربائيات للتواصل/ {$mobile}782806886{$mobile} {$mobile}770098841{$mobile}\n";
        $msg .= " {$tel}105488070{$tel}\n";
        $msg .= " *فروعنا*:\n";
        $msg .= "*فرع عدن* الشيخ عثمان-القاهرة قبل بنك التضامن في الجهة المقابلة على دخلة سور مدرسة إدريس حنبلة بجانب بقالة القمة للتواصل/\n";
        $msg .= "{$mobile}782947557{$mobile}\n";
        $msg .= "{$mobile}778132137{$mobile}\n";
        $msg .= "{$tel}02383444{$tel}\n";
        $msg .= "{$search} *لمتابعة طردك:* تواصل معنا على الأرقام أعلاه.\n";
        $msg .= "{$warning} *تنبيهات هامة:*\n";
        $msg .= "1- اي سند لايحمل ختم المكتب غير مقبول.\n";
        $msg .= "2- المكتب غير مسؤول عن محتويات الطرد ولا تقبل إي مراجعة بعد(1)يوم من تاريخ إستلامه.\n";
        $msg .= "3- المكتب غير مسؤول عن الإجراءت الأمنية والجمركية الخارجة عن إرادتنا.\n";
        $msg .= "4- المكتب غير مسؤول عن الطرد بعد شهر من وصوله إلى مكان إستلامة\n";
        $msg .= "5- نحن غير مسؤولين عن الحريق وحوادث السير.\n";
        $msg .= "6- الرجاء التأكد من بيانات السند قبل الإرسال.\n";
        $msg .= "7- لايعتبر هذا السند إثبات لإستلام الإرسالية لذا يجب إحضار البطاقة الشخصية أو جواز السفر.\n\n";
        $msg .= "{$heart}نتمنى لك يوماً سعيداً! {$heart}";

        return $msg;
    }
}

