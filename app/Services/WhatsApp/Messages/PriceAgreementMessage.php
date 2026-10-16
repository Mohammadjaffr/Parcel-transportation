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
        return $entity->receiverCustomer?->phone ?? $entity->receiver_phone ?? $entity->senderCustomer?->phone ?? $entity->sender_phone;
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

        // 👤 بيانات المرسل والمستلم والسند (عرض الأرقام بدون رمز الدولة)
        $name             = $entity->receiverCustomer?->name ?? 'عميلنا العزيز';
        $rawReceiverPhone = $entity->receiverCustomer?->phone ?? $entity->receiver_phone ?? null;
        $receiverPhone    = $this->formatPhoneWithoutCountryCode($rawReceiverPhone);

        $senderName       = $entity->senderCustomer?->name ?? 'غير محدد';
        $rawSenderPhone   = $entity->senderCustomer?->phone ?? $entity->sender_phone ?? null;
        $senderPhone      = $this->formatPhoneWithoutCountryCode($rawSenderPhone);

        $bondNumber       = $entity->bond_number ?? $entity->code ?? $entity->id ?? 'غير متوفر';

        // 📦 نوع الطرد والكمية
        $packageType = $entity->package_type ?: 'طرد عادي';
        if ($entity->no_gallons_honey || $entity->no_honey_jars) {
            $honey = [];
            if ($entity->no_gallons_honey) {
                $honey[] = "جوالين ({$entity->no_gallons_honey})";
            }
            if ($entity->no_honey_jars) {
                $honey[] = "قروف ({$entity->no_honey_jars})";
            }
            if ($entity->package_type && $entity->package_type !== 'عسل') {
                $packageType .= ' + عسل: ' . implode(' - ', $honey);
            } else {
                $packageType = 'عسل (' . implode(' - ', $honey) . ')';
            }
        }
        $parcelsCount = $entity->parcels_quantity ?? 1;

        // 🏢 جلب اسم مكتب الإرسال
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

        // 🏢 جلب اسم مكتب التسليم
        $receiverOffice = null;
        if ($entity->receiverBranch) {
            $rawName = trim(str_replace(['مكتب عالم القمة', 'مكتب'], '', $entity->receiverBranch->name));
            $branchTitle = ($entity->receiverBranch->is_main ? 'المركز الرئيسي/ ' : '') . $rawName;
            $receiverOffice = 'مكتب عالم القمة ' . $branchTitle;
        } elseif ($entity->receiverOfficeBranch) {
            $rawOffice = $entity->receiverOfficeBranch->office?->name ?? 'عالم القمة';
            $receiverOffice = 'مكتب ' . $rawOffice . ' ' . $entity->receiverOfficeBranch->name;
        }
        $branchName = $receiverOffice ?? $entity->receiverBranch?->name ?? $entity->receiverOfficeBranch?->name ?? 'مكتب عالم القمة المركز الرئيسي/ سيئون';
        $branchName = rtrim($branchName, '.') . '.';

        // ✅ حالة الطرد
        $statusText = match ($entity->status) {
            'received_at_branch' => 'وصل الفرع وجاهز للاستلام.',
            'delivered'          => 'تم التسليم بنجاح.',
            'cancelled'          => 'تم إلغاء الشحنة.',
            'returned'           => 'تم إرجاع الطرد.',
            default              => 'قيد العمل على معالجتة وإرسالة.',
        };

        // 💸 طريقة الدفع وتكلفة النقل
        $paymentMethods = [
            'prepaid'         => 'مقدم',
            'cod'             => 'عند الاستلام (على المستلم)',
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

        // 🔗 رابط السند الإلكتروني (PDF)
        if (!$receiptUrl) {
            try {
                $receiptUrl = route('receipt.generate', ['type' => 'sender', 'id' => $entity->uuid ?? $entity->id]);
            } catch (\Throwable $e) {
                $receiptUrl = null;
            }
        }

        // تعريف الأيقونات بترميز Unicode القياسي لضمان التوافق وتفادي أي أخطاء
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

        // بناء نص الرسالة بحسب تعديل1 بالكامل
        $msg  = "السلام عليكم ورحمة الله وبركاته\n\n";
        $msg .= "الأخ/ *{$name}*       المحترم.\n";
        $msg .= "تحية طيبة وبعد:\n\n";
        $msg .= "       {$star} *مرحباً بك في عالم القمة للنقل السريع* {$star}\n";
        $msg .= "{$line}\n";
        $msg .= "                                 >{$box} *إشعار إستلام طرد*<\n";
        $msg .= "{$calendar}*اليوم:* ~{$dayName}~ | *~التاريخ~*:{$dateFormatted} | *~الوقت~:* {$timeFormatted}\n";
        $msg .= "{$numbers} *رقم السند:*  {$bondNumber}\n";

        if ($receiptUrl) {
            $msg .= "{$linkIcon} *رابط السند (PDF):*\n{$receiptUrl}\n";
        }

        $msg .= "{$userIcon} *المرسل:* {$senderName}\n";
        $msg .= "{$phone} *جوال المرسل:* {$senderPhone}\n";
        $msg .= "{$userIcon} *المستلم:* {$name}\n";
        $msg .= "{$phone} *جوال المستلم:* {$receiverPhone}\n";
        $msg .= "{$chart} *نوع الطرد:* {$packageType}\n";
        $msg .= "{$numbers} *عدد الطرد*: {$parcelsCount}\n";
        $msg .= "*مكتب الإرسال*: {$senderPlace}\n";
        $msg .= "*مكتب التسليم*: {$branchName}\n";
        $msg .= "{$check} *حالة الطرد:* {$statusText}\n";
        $msg .= "{$money} *طريقة الدفع:*   {$paymentMethod}\n";
        $msg .= "*تكلفة النقل*:  {$shippingCost}\n";
        $msg .= "*~تواصل معنا لتسعيرها بالاتصال اوعلى خدمة الواتساب على الارقام مكتب عالم القمة فرع عدن~*\n";
        $msg .= "*موبايل1:*{$mobile}782947557{$mobile}\n";
        $msg .= "*موبايل2:*{$mobile}778132137{$mobile}\n";
        $msg .= "*الهاتف:*  {$tel}02383444{$tel}\n";
        $msg .= "~*اوسنتواصل معك في اقرب وقت.*~\n";
        $msg .= "⏱️ *التوصيل المتوقع:* خلال 24-48 ساعة عمل\n";
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

    /**
     * تنسيق رقم الهاتف وعرضه بدون رمز الدولة
     */
    public function formatPhoneWithoutCountryCode(?string $phone): string
    {
        if (empty($phone) || trim($phone) === '' || trim($phone) === '---' || trim($phone) === 'غير متوفر') {
            return 'غير متوفر';
        }

        $parts = preg_split('/[\/]+/', trim($phone));
        $formattedParts = [];

        $countries = config('countries', []);
        $dialCodes = [];
        if (is_array($countries)) {
            foreach ($countries as $country) {
                $rawCode = ltrim($country['dial_code'] ?? '', '+');
                if ($rawCode) {
                    $dialCodes[] = $rawCode;
                }
            }
        }
        $dialCodes = array_unique(array_merge($dialCodes, ['967', '966', '968', '971']));
        usort($dialCodes, fn($a, $b) => strlen($b) <=> strlen($a));

        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '') continue;

            $cleaned = preg_replace('/[^\d\+]/', '', $part);
            if ($cleaned === '') continue;

            if (str_starts_with($cleaned, '967967')) {
                $cleaned = substr($cleaned, 3);
            } elseif (str_starts_with($cleaned, '966966')) {
                $cleaned = substr($cleaned, 3);
            }

            foreach ($dialCodes as $rawCode) {
                if (preg_match('/^(\+|00)?' . preg_quote($rawCode, '/') . '/', $cleaned)) {
                    $cleaned = preg_replace('/^(\+|00)?' . preg_quote($rawCode, '/') . '/', '', $cleaned);
                    break;
                }
            }

            if (preg_match('/^0(7\d{8})$/', $cleaned, $matches)) {
                $cleaned = $matches[1];
            }

            $formattedParts[] = $cleaned;
        }

        return !empty($formattedParts) ? implode(' - ', $formattedParts) : 'غير متوفر';
    }
}
