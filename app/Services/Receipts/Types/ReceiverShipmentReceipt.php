<?php

namespace App\Services\Receipts\Types;

use App\Models\Shipment;
use App\Interfaces\ReceiptStrategyInterface;
use Carbon\Carbon;

class ReceiverShipmentReceipt implements ReceiptStrategyInterface
{
    public function sizepage(): string|array
    {
        return 'A4'; // Default size
    }

    public function fetchData(string $referenceId): array
    {
        // 1. جلب الشحنة مع جميع العلاقات الأساسية (بما فيها المكاتب الخارجية 🎯)
        $shipment = Shipment::with([
            'senderCustomer',
            'receiverCustomer',
            'senderBranch.app',
            'receiverBranch.app',
            'senderOfficeBranch.office',   // 👈 إصلاح: جلب المكتب الخارجي المرسل وشركته
            'receiverOfficeBranch.office', // 👈 إصلاح: جلب شركة المكتب الخارجي المستلم
            'creator.app',
            'creator.branch'
        ])->where('uuid', $referenceId)->firstOrFail();

        // 2. تحديد الفرع الذي سيطبع السند (الفرع المُستلم أو المكتب الخارجي المُستلم)
        $printBranch = $shipment->receiverBranch ?? $shipment->receiverOfficeBranch ?? $shipment->senderBranch ?? $shipment->senderOfficeBranch;
        
        // جلب بيانات التطبيق (الشركة) بناءً على الفرع المُستلم
        // ملاحظة: المكاتب الخارجية تتبع لـ office وليس app، لذلك نأخذ app الخاص بمنشئ الشحنة كبديل آمن
        $app = $printBranch?->app ?? $shipment->creator?->app;

        // 3. تجهيز مسار الشعار الديناميكي - آمن
        $imagePath = $app?->logo
            ? public_path('storage/' . $app->logo)
            : public_path('assets/image/icon_without_bg.png');

        $logoBase64 = null;
        if (file_exists($imagePath)) {
            $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
            $data = file_get_contents($imagePath);
            $logoBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($data);
        }

        // 4. إعداد ترويسة السند (Header) لتكون باسم الفرع المُستلم
        $mainBranchData = null;
        if ($printBranch) {
            $mainBranchData = [
                'title' => 'فرع / ' . $printBranch->name . ($printBranch->address ? ' - ' . $printBranch->address : ''),
                'phones' => implode(' - ', array_filter(array_map('trim', preg_split('/[\s,\-]+/', $printBranch->phone ?? ''))))
            ];
        }

        $otherPhonesList = [];
        $headquartersData = null;
        
        if ($app) {
            if ($app->phone) {
                $hqPhoneArray = array_filter(array_map('trim', preg_split('/[\s,\-]+/', $app->phone)));
                if (!empty($hqPhoneArray)) {
                    $headquartersData = [
                        'title' => 'الفرع الرئيسي' . ($app->address ? ' - ' . $app->address : ''),
                        'phones' => implode(' - ', $hqPhoneArray)
                    ];
                }
            }

            $allBranches = $app->branches()->get();
            foreach($allBranches as $b) {
                if ($printBranch && $b->id === $printBranch->id) {
                    continue;
                }
                $phonesArray = array_filter(array_map('trim', preg_split('/[\s,\-]+/', $b->phone ?? '')));
                $otherPhonesList = array_merge($otherPhonesList, $phonesArray);
            }
        }
        $otherPhonesStr = !empty($otherPhonesList) ? implode(' - ', array_unique($otherPhonesList)) : null;

        // 5. القواميس والمتغيرات
        $paymentMethods = [
            'prepaid'         => 'مدفوع مقدماً',
            'cod'             => 'الدفع عند الاستلام',
            'partial_payment' => 'دفع جزئي',
            'customer_credit' => 'آجل (ذمة)'
        ];

        // 🎯 إصلاح: تحديد وجهة الإرسال والاستلام بدقة (يدعم المكاتب الخارجية)
        $senderSource = $shipment->senderBranch?->name
            ?? $shipment->senderOfficeBranch?->name
            ?? 'الفرع الرئيسي';

        $receiverDestination = $shipment->receiverBranch?->name
            ?? $shipment->receiverOfficeBranch?->name
            ?? 'الوجهة غير محددة';

        $honeyDetails = ($shipment->no_gallons_honey || $shipment->no_honey_jars)
            ? "جوالين: " . ($shipment->no_gallons_honey ?? 0) . " | قروف: " . ($shipment->no_honey_jars ?? 0)
            : null;

        $theme = $app?->theme ?? [
            'primary'   => '#ea580c',
            'secondary' => '#1e293b',
            'bg_light'  => '#fffaf5',
        ];

        return [
            'company' => [
                'name'         => $app?->name ?? 'اسم الشركة غير محدد',
                'logo'         => $logoBase64,
                'main_branch'  => $mainBranchData,
                'headquarters' => $headquartersData,
                'other_phones' => $otherPhonesStr,
            ],

            'title'             => 'سند تسليم طرد',
            'bond_number'       => $shipment->id ?? 'غير متوفر',
            'tracking_code'     => $shipment->code ?? 'بدون تتبع',
            // 🎯 دمج وتنسيق احترافي مرة واحدة فقط
'date' => ($shipment->created_at ?? now())->timezone('Asia/Aden')->locale('ar')->translatedFormat('l | Y-m-d | h:i A'),

            // --- بيانات المرسل والمستلم ---
            'sender_name'       => $shipment->senderCustomer?->name ?? 'عميل نقدي (غير مسجل)',
            'sender_phone'      => $shipment->senderCustomer?->phone ?? '---',
            
            // 🎯 إصلاح: استخدام المتغيرات الذكية التي تتعرف على المكتب الخارجي
            'sender_branch'     => $senderSource,
            'sender_office'     => $shipment->senderBranch?->app?->name ?? $shipment->senderOfficeBranch?->office?->name ?? $app?->name ?? 'الفرع الرئيسي',
            'sender_branch_phone'=> $shipment->senderBranch?->phone ?? $shipment->senderOfficeBranch?->phone ?? '---',
            
            'receiver_name'     => $shipment->receiverCustomer?->name ?? 'مستلم غير محدد',
            'receiver_phone'    => $shipment->receiverCustomer?->phone ?? '---',
            'receiver_branch'   => $receiverDestination,
            'receiver_office'   => $shipment->receiverBranch?->app?->name ?? $shipment->receiverOfficeBranch?->office?->name ?? $app?->name ?? 'الفرع الرئيسي',

            // --- تفاصيل الطرد ---
            'package_type'      => $shipment->package_type ?? 'طرد عادي',
            'weight'            => $shipment->weight ? $shipment->weight . ' كجم' : null,
            'honey_details'     => $honeyDetails,
            'notes'             => $shipment->notes ?? 'لا توجد ملاحظات إضافية',

            // --- الحسابات والمبالغ ---
            'payment_key'       => $shipment->payment_method ?? 'prepaid',
            'payment_method'    => $paymentMethods[$shipment->payment_method ?? 'prepaid'] ?? 'غير محدد',
            'total_amount'      => number_format($shipment->total_amount, 0),
            'partial_amount'    => number_format($shipment->total_amount - ($shipment->amount_to_collect_from_receiver ?? 0), 0),
            'remaining_amount'  => number_format($shipment->amount_to_collect_from_receiver ?? 0, 0),

            'creator_name'      => $shipment->creator?->name ?? 'مسؤول النظام',
            'print_date'        => now()->timezone('Asia/Aden')->format('Y-m-d h:i A'),
            'user_branch'       => $shipment->creator?->branch?->name ?? $shipment->senderBranch?->name ?? 'الفرع الرئيسي',
            'terms_and_conditions' => (is_array($app?->terms_and_conditions) && count($app->terms_and_conditions) > 0) 
                ? $app->terms_and_conditions 
                : ['نحن غير مسؤولين عن الإجراءات الأمنية الخارجة عن إرادتنا.', 'يرجى مراجعة الطرد قبل مغادرة الفرع.'],
            
            'design' => [
                'primary_color'   => $theme['primary'] ?? '#ea580c',
                'secondary_color' => $theme['secondary'] ?? '#1e293b',
                'bg_color'        => $theme['bg_light'] ?? '#fffaf5',
                'font_family'     => "'aealarabiya', 'dejavusans', sans-serif",
                'paper_size'      => 'a4',
            ]
        ];
    }

    public function getTemplatePath(): string
    {
        return 'receipts.templates.ReceiverShipmentReceipt';
    }

    public function getFileName(array $data): string
    {
        return 'Receiver_Receipt_' . $data['bond_number'] . '.pdf';
    }
}