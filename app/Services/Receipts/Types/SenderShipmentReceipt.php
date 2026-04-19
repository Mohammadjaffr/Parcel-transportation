<?php

namespace App\Services\Receipts\Types;

use App\Models\Shipment;
use App\Interfaces\ReceiptStrategyInterface;

class SenderShipmentReceipt implements ReceiptStrategyInterface
{
    public function sizepage(): string|array
    {
        return 'A4'; // Default size
    }
    public function fetchData(string $referenceId): array
    {
        // 1. جلب الشحنة مع العلاقات
        $shipment = Shipment::with([
            'senderCustomer',
            'receiverCustomer',
            'senderBranch',
            'receiverBranch',
            'receiverOfficeBranch',
            'creator'
        ])->where('uuid', $referenceId)->firstOrFail();

        // 2. جلب بيانات التطبيق (الشركة) من المستخدم الحالي
        $app = auth()->user()->app;

        // 3. تجهيز مسار الشعار الديناميكي
        $logoPath = $app?->logo
            ? public_path('storage/' . $app->logo)
            : public_path('images/new.svg');

        // 4. إعداد فرع السند وأرقام الفروع
        $senderBranch = $shipment->senderBranch;
        
        $mainBranchData = null;
        if ($senderBranch) {
            $mainBranchData = [
                'title' => 'فرع / ' . $senderBranch->name . ($senderBranch->address ? ' - ' . $senderBranch->address : ''),
                'phones' => implode(' - ', array_filter(array_map('trim', preg_split('/[\s,\-]+/', $senderBranch->phone ?? ''))))
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
                if ($senderBranch && $b->id === $senderBranch->id) {
                    continue;
                }
                $phonesArray = array_filter(array_map('trim', preg_split('/[\s,\-]+/', $b->phone ?? '')));
                $otherPhonesList = array_merge($otherPhonesList, $phonesArray);
            }
        }
        $otherPhonesStr = !empty($otherPhonesList) ? implode(' - ', array_unique($otherPhonesList)) : null;

        // 5. تجهيز القواميس والمتغيرات المساعدة
        $paymentMethods = [
            'prepaid'         => 'مدفوع مقدماً',
            'cod'             => 'الدفع عند الاستلام',
            'partial_payment' => 'دفع جزئي',
            'customer_credit' => 'آجل (ذمة)'
        ];

        $receiverDestination = $shipment->receiverBranch?->name
            ?? $shipment->receiverOfficeBranch?->name
            ?? 'الوجهة غير محددة';

        $honeyDetails = ($shipment->no_gallons_honey || $shipment->no_honey_jars)
            ? "جوالين: " . ($shipment->no_gallons_honey ?? 0) . " | دبات: " . ($shipment->no_honey_jars ?? 0)
            : null;
        

        // 6. إرجاع البيانات المنظمة
        return [
            // --- معلومات الشركة والفرع ---
            'company' => [
                'name'        => $app?->name ?? 'اسم الشركة غير محدد',
                'logo'        => $logoPath,
                'main_branch' => $mainBranchData,
                'headquarters'=> $headquartersData,
                'other_phones'=> $otherPhonesStr,
            ],

            // --- معلومات السند ---
            'title'             => 'سند استلام طرد',
            'bond_number'       => $shipment->bond_number ?? 'غير متوفر',
            'tracking_code'     => $shipment->code ?? 'بدون تتبع',
            'date'              => $shipment->created_at ? $shipment->created_at->format('Y-m-d h:i A') : now()->format('Y-m-d h:i A'),

            // --- بيانات المرسل والمستلم ---
            'sender_name'       => $shipment->senderCustomer?->name ?? 'عميل نقدي (غير مسجل)',
            'sender_phone'      => $shipment->senderCustomer?->phone ?? '---',
            'sender_branch'     => $shipment->senderBranch?->name ?? 'الفرع الرئيسي',
            'receiver_name'     => $shipment->receiverCustomer?->name ?? 'مستلم غير محدد',
            'receiver_phone'    => $shipment->receiverCustomer?->phone ?? '---',
            'receiver_branch'   => $receiverDestination,

            // --- تفاصيل الطرد ---
            'package_type'      => $shipment->package_type ?? 'طرد عادي',
            'weight'            => $shipment->weight ? $shipment->weight . ' كجم' : null,
            'honey_details'     => $honeyDetails,
            'notes'             => $shipment->notes ?? 'لا توجد ملاحظات إضافية',

            // --- الحسابات والمبالغ ---
            'payment_key'       => $shipment->payment_method ?? 'prepaid',
            'payment_method'    => $paymentMethods[$shipment->payment_method ?? 'prepaid'] ?? 'غير محدد',
            'total_amount'      => number_format($shipment->total_amount ?? 0, 0),
            'partial_amount'    => number_format($shipment->partial_amount ?? 0, 0),
            'remaining_amount'  => number_format(($shipment->total_amount ?? 0) - ($shipment->partial_amount ?? 0), 0),

            // --- معلومات النظام والتصميم ---
            'creator_name'      => $shipment->creator?->name ?? 'مسؤول النظام',
            'print_date'        => now()->format('Y-m-d H:i'),
            'terms_and_conditions' => is_array($app?->terms_and_conditions) && count($app->terms_and_conditions) > 0 
                ? $app->terms_and_conditions 
                : ['لا توجد شروط وأحكام'],
            'design' => [
                'primary_color'   => $app->theme['primary'],
                'secondary_color' => $app->theme['secondary'],
                'bg_color'        => $app->theme['bg_light'],
                'font_family'     => "'aealarabiya', 'dejavusans', sans-serif",
                'paper_size'      => 'a4',
            ]
        ];
    }
    public function getTemplatePath(): string
    {
        return 'receipts.templates.SenderShipmentReceipt';
    }

    public function getFileName(array $data): string
    {
        return 'Sender_Receipt_' . $data['bond_number'] . '.pdf';
    }
}
