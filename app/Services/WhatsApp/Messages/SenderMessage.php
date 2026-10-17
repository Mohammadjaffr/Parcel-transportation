<?php

namespace App\Services\WhatsApp\Messages;

use App\Interfaces\WhatsAppMessageInterface;
use App\Models\App;
use App\Models\Shipment;
use Illuminate\Database\Eloquent\Model;

class SenderMessage implements WhatsAppMessageInterface
{
    public function getPhoneNumber(Model $entity): ?string
    {
        if (!$entity instanceof Shipment) {
            return null;
        }
        return $entity->senderCustomer?->phone ?? $entity->sender_phone ?? null;
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

        // 👤 بيانات المرسل والمستلم والسند (مع رمز الدولة 967 كما في التصميم)
        $senderName       = $entity->senderCustomer?->name ?? 'عميلنا العزيز';
        $rawSenderPhone   = $entity->senderCustomer?->phone ?? $entity->sender_phone ?? null;
        $senderPhone      = $this->formatPhoneWithoutCountryCode($rawSenderPhone);

        $receiverName     = $entity->receiverCustomer?->name ?? 'غير محدد';
        $rawReceiverPhone = $entity->receiverCustomer?->phone ?? $entity->receiver_phone ?? null;
        $receiverPhone    = $this->formatPhoneWithoutCountryCode($rawReceiverPhone);

        $bondNumber       = $entity->bond_number ?? $entity->code ?? $entity->id ?? 'غير متوفر';

        // 🔗 رابط السند الإلكتروني (PDF)
        if (!$receiptUrl) {
            try {
                $receiptUrl = route('receipt.generate', ['type' => 'sender', 'id' => $entity->uuid ?? $entity->id]);
            } catch (\Throwable $e) {
                $receiptUrl = null;
            }
        }

        // 📦 نوع الطرد والكمية (شامل تفاصيل العسل من الموديل)
        $packageType = $entity->package_type ?: 'طرد عادي';
        if ($entity->no_gallons_honey || $entity->no_honey_jars) {
            $honey = [];
            if ($entity->no_gallons_honey) {
                $honey[] = "جوالين ({$entity->no_gallons_honey})";
            }
            if ($entity->no_honey_jars) {
                $honey[] = "قروف ({$entity->no_honey_jars})";
            }
            $honeyText = implode(' ، ', $honey);
            if ($entity->package_type && $entity->package_type !== 'عسل') {
                $packageType .= ' + عسل: ' . $honeyText;
            } else {
                $packageType = 'عسل (' . $honeyText . ')';
            }
        }
        // استخدام الـ Accessor الموجود في الموديل getParcelsCountAttribute
        $parcelsCount = $entity->parcels_count ?? $entity->parcels_quantity ?? 1;

        // 🏢 جلب اسم مكتب الإرسال ديناميكياً
        $senderOffice = null;
        if ($entity->senderBranch) {
            $rawName = trim(str_replace(['مكتب عالم القمة', 'مكتب', 'فرع'], '', $entity->senderBranch->name));
            $branchTitle = ($entity->senderBranch->is_main ? '/ ' : '/ ') . $rawName;
            $senderOffice = 'مكتب عالم القمة ' . $branchTitle;
        } elseif ($entity->senderOfficeBranch) {
            $rawOffice = $entity->senderOfficeBranch->office?->name ?? 'عالم القمة';
            $rawName = trim(str_replace(['مكتب', 'فرع'], '', $entity->senderOfficeBranch->name));
            $senderOffice = 'مكتب ' . $rawOffice . ' / ' . $rawName;
        }
        $senderPlace = $senderOffice ?? $entity->sender?->name ?? 'مكتب عالم القمة / عدن';
        $senderPlace = rtrim(preg_replace('/\s+/', ' ', $senderPlace), '. ');

        // 🏢 جلب اسم مكتب التسليم ديناميكياً
        $receiverOffice = null;
        if ($entity->receiverBranch) {
            $rawName = trim(str_replace(['مكتب عالم القمة', 'مكتب', 'فرع'], '', $entity->receiverBranch->name));
            $branchTitle = ($entity->receiverBranch->is_main ? '/ المركز الرئيسي/ ' : '/ ') . $rawName;
            $receiverOffice = 'مكتب عالم القمة ' . $branchTitle;
        } elseif ($entity->receiverOfficeBranch) {
            $rawOffice = $entity->receiverOfficeBranch->office?->name ?? 'عالم القمة';
            $rawName = trim(str_replace(['مكتب', 'فرع'], '', $entity->receiverOfficeBranch->name));
            $receiverOffice = 'مكتب ' . $rawOffice . ' / ' . $rawName;
        }
        $branchName = $receiverOffice ?? $entity->receiver?->name ?? 'مكتب عالم القمة / سيئون';
        $branchName = rtrim(preg_replace('/\s+/', ' ', $branchName), '. ');

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

        // 📍 جلب مكان التسليم مع أرقام التواصل الخاصة بفرع التسليم ديناميكياً
        $receiverEntity = $entity->receiver; // uses getReceiverAttribute() from Shipment model
        $defaultSeiyunPlace = "شارع الجزائر مقابل شركة الثور للصرافة بجانب الزبيدي اخوان للكهربائيات للتواصل/ {$mobile}782806886{$mobile} {$mobile}770098841{$mobile} {$tel}105488070{$tel}";

        if ($receiverEntity && !empty($receiverEntity->address)) {
            $deliveryPlace = rtrim($receiverEntity->address, '. ');
            // إذا كان العنوان في قاعدة البيانات لا يحتوي على أرقام التواصل، يتم إلحاقها ديناميكياً إن وجدت
            if (!str_contains($deliveryPlace, 'للتواصل') && !empty($receiverEntity->phone)) {
                $deliveryPlace .= " للتواصل/ {$mobile}{$receiverEntity->phone}{$mobile}";
            }
        } else {
            $deliveryPlace = $defaultSeiyunPlace;
        }

        // ✅ تحديد حالة الطرد
        $statusText = match ($entity->status) {
            'received_at_branch' => 'وصل الفرع وجاهز للاستلام.',
            'delivered'          => 'تم التسليم بنجاح.',
            'cancelled'          => 'تم إلغاء الشحنة.',
            'returned'           => 'تم إرجاع الطرد.',
            default              => 'قيد العمل على معالجتة وإرسالة.',
        };

        if ($entity->is_returned) {
            $statusText = 'تم إرجاع الطرد.';
        }

        // 💸 طريقة الدفع وتكلفة النقل بناءً على منطق الموديل
        $totalAmount = (float) ($entity->total_amount ?? 0);

        if (!$entity->is_price_agreed) {
            $paymentMethod = 'حسب السند مدفوع مقدماً او اجل على المستلم';
            $costText      = 'بيتم الاتفاق عليها ';
        } else {
            $paymentMethod = match ($entity->payment_method) {
                'prepaid'         => 'مدفوع مقدماً',
                'cod'             => 'الدفع عند الاستلام (على المستلم)',
                'partial_payment' => 'دفع جزئي (مقدم: ' . number_format((float)$entity->partial_amount) . ' ريال | متبقي على المستلم: ' . number_format($entity->amount_to_collect_from_receiver) . ' ريال)',
                'customer_credit' => 'آجل (على حساب العميل)',
                default           => 'حسب السند مدفوع مقدماً او اجل على المستلم',
            };
            $costText = $totalAmount > 0 ? number_format($totalAmount) . ' ريال' : ' كلفة النقل بيتم الاتفاق عليها ';
        }

        // 📝 الملاحظات من قاعدة البيانات أو النص الافتراضي
        $notesText = !empty(trim((string) $entity->notes)) ? trim($entity->notes) : 'لاتوجد ';

        // بناء نص الرسالة مطابقاً للتصميم المطلوب بالضبط
        $msg  = "السلام عليكم ورحمة الله وبركاته\n\n";
        $msg .= "الأخ العميل/ *{$senderName}*       المحترم.\n";
        $msg .= "تحية طيبة وبعد:\n\n";
        $msg .= "{$star} *مرحباً بك في عالم القمة للنقل السريع* {$star}\n";
        $msg .= "{$line}\n";
        $msg .= ">{$box} *إشعار إستلام طرد للمرسل*<\n";
        $msg .= "{$calendar}*اليوم:* ~{$dayName}~ | *~التاريخ~*:{$dateFormatted} | *~الوقت~:* {$timeFormatted}\n";
        $msg .= "{$numbers} *رقم السند:*  {$bondNumber} .\n";

        if ($receiptUrl) {
            $msg .= "{$linkIcon} *رابط السند (PDF):*\n{$receiptUrl}\n";
        }

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
        $msg .= "{$money} *طريقة الدفع:* {$paymentMethod} .\n";
        $msg .= "*تكلفة النقل*: {$costText}.\n";
        $msg .= "{$money} *ملاحظة:*  {$notesText}.\n";
        $msg .= "»تواصل معنا في حالة عدم استكمال البيانات«\n";
        $msg .= "*~للاستفسار الاتصال بنا او مراسلتنا على خدمة الواتساب على الارقام مكتب عالم القمة فرع عدن~*\n";
        $msg .= "*موبايل1:*{$mobile}782947557{$mobile}\n";
        $msg .= "*موبايل2:*{$mobile}778132137{$mobile}\n";
        $msg .= "*الهاتف:*  {$tel}02383444{$tel}\n";
        $msg .= "{$timer} *التوصيل المتوقع:* خلال 14-24 ساعة\n";
        $msg .= "{$line}\n";
        $msg .= "{$pin} *مكتب عالم القمة للنقل السريع*{$pin}\n";
        $msg .= "المركز الرئيسي/ سيئون:شارع الجزائر مقابل شركة الثور للصرافة بجانب الزبيدي اخوان للكهربائيات للتواصل/ {$mobile}782806886{$mobile} {$mobile}770098841{$mobile}\n";
        $msg .= " {$tel}105488070{$tel}\n";
        $msg .= " *فروعنا*:\n";
        $msg .= "*فرع عدن* الشيخ عثمان-القاهرة قبل بنك التضامن في الجهة المقابلة على دخلة سور مدرسة إدريس حنبلة بجانب بقالة القمة للتواصل/\n";
        $msg .= "{$mobile}782947557{$mobile}\n";
        $msg .= "{$mobile}778132137{$mobile}\n";
        $msg .= "{$tel}02383444{$tel}\n";
        $msg .= "{$search} *لمتابعة طردك:* برقم السند او تواصل معنا على الأرقام أعلاه.\n";
        // 📜 جلب الشروط والتنبيهات ديناميكياً من إعدادات الشركة (App)
        $app = $entity->senderBranch?->app 
            ?? $entity->creator?->app 
            ?? $entity->receiverBranch?->app;

        if (!$app && auth()->check()) {
            $app = auth()->user()->app ?? auth()->user()->App;
        }

        if (!$app) {
            $app = App::first();
        }

        $defaultTerms = [
            'اي سند لايحمل ختم المكتب غير مقبول.',
            'المكتب غير مسؤول عن محتويات الطرد ولا تقبل إي مراجعة بعد(1)يوم من تاريخ إستلامه.',
            'المكتب غير مسؤول عن الإجراءت الأمنية والجمركية الخارجة عن إرادتنا.',
            'المكتب غير مسؤول عن الطرد بعد شهر من وصوله إلى مكان إستلامة',
            'نحن غير مسؤولين عن الحريق وحوادث السير.',
            'الرجاء التأكد من بيانات السند قبل الإرسال.',
            'لايعتبر هذا السند إثبات لإستلام الإرسالية لذا يجب إحضار البطاقة الشخصية أو جواز السفر.',
        ];

        $terms = [];
        if (!empty($app?->terms_and_conditions) && is_array($app->terms_and_conditions)) {
            $terms = array_values(array_filter(array_map('trim', $app->terms_and_conditions)));
        }

        if (empty($terms)) {
            $terms = $defaultTerms;
        }

        if (!empty($terms)) {
            $msg .= "{$warning} *تنبيهات هامة:*\n";
            foreach ($terms as $index => $term) {
                $termText = trim($term);
                if (!preg_match('/^\d+[\s\.\-\)\:]+/', $termText)) {
                    $termText = ($index + 1) . '- ' . $termText;
                }
                $msg .= "{$termText}\n";
            }
            $msg .= "\n";
        }
        $msg .= "{$heart}نتمنى لك يوماً سعيداً! {$heart}";

        return $msg;
    }

    /**
     * تنسيق رقم الهاتف مع ضمان وجود رمز الدولة (مثلاً 967771027082) كما في التصميم
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