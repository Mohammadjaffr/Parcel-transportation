<?php

namespace App\Services\Receipts\Types;

use App\Models\Shipment;
use App\Interfaces\ReceiptStrategyInterface;
use Carbon\Carbon;

class ThermalShipmentReceipt implements ReceiptStrategyInterface
{
    public function sizepage(): string|array
    {
        return [70, 120]; // Default size
    }

    public function fetchData(string $referenceId): array
    {
        // 1. جلب الشحنة مع العلاقات الأساسية و .app
        $shipment = Shipment::with([
            'senderCustomer',
            'receiverCustomer',
            'senderBranch.app', // 👈 تم إضافة .app هنا
            'receiverBranch',
            'receiverOfficeBranch',
            'creator.app'       // 👈 وتمت إضافتها هنا
        ])->where('uuid', $referenceId)->firstOrFail();

        // 2. جلب بيانات التطبيق (الشركة) من الشحنة - آمن جداً للروابط العامة
        $app = $shipment->senderBranch?->app ?? $shipment->creator?->app;

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
        
        // 🛡️ حماية: لن يتم تنفيذ هذا الكود إلا لو كان $app موجوداً
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

        // 6. الثيم والألوان - آمن
        $theme = $app?->theme ?? [
            'primary'   => '#ea580c',
            'secondary' => '#1e293b',
            'bg_light'  => '#fffaf5',
        ];

        // 7. الحسابات المالية الدقيقة بناءً على منطق الموديل وطريقة الدفع (العمليات السابقة كما هي)
        $totalAmount = (float) ($shipment->total_amount ?? 0);
        $remainingAmount = (float) ($shipment->amount_to_collect_from_receiver ?? 0);

        $paidAmount = match ($shipment->payment_method) {
            'prepaid'         => $totalAmount,
            'partial_payment' => (float) ($shipment->partial_amount ?? 0),
            'cod'             => 0,
            default           => 0,
        };

        // الأجور الأخرى والإجمالي الجامع للصف فقط
        $otherFee = (float) ($shipment->other_fee ?? 0);
        $showOtherFee = (bool) ($shipment->show_other_fee_in_receipt ?? false) && $otherFee > 0;
        $transportFee = $totalAmount;
        $grandTotal = $showOtherFee ? ($transportFee + $otherFee) : $totalAmount;

        // 8. إرجاع البيانات المنظمة
        return [
            // --- معلومات الشركة والفرع ---
            'company' => [
                'name'         => $app?->name ?? 'اسم الشركة غير محدد',
                'logo'         => $logoBase64,
                'main_branch'  => $mainBranchData,
                'headquarters' => $headquartersData,
                'other_phones' => $otherPhonesStr,
                'branch_addresses' => $app?->effective_branch_addresses ?? [],
            ],
            'branch_addresses'  => $app?->effective_branch_addresses ?? [],

            // --- معلومات السند ---
            'title'             => 'سند استلام طرد',
            'bond_number'       => $shipment->bond_number ?? 'غير متوفر',
            'tracking_code'     => $shipment->bond_number ?? 'بدون تتبع',
            
            // 🛡️ تم إصلاح الثغرة المخفية في التاريخ
            'date'              => ($shipment->created_at ? $shipment->created_at->format('Y-m-d h:i A') : now()->format('Y-m-d h:i A'))
                                   . ' - ' . Carbon::now()->locale('ar')->translatedFormat('l Y-m-d H:i'),

            // --- بيانات المرسل والمستلم ---
            'sender_name'       => $shipment->senderCustomer?->name ?? 'عميل نقدي (غير مسجل)',
            'sender_phone'      => $shipment->senderCustomer?->phone ?? '---',
            'sender_branch'     => $shipment->senderBranch?->name ?? 'الفرع الرئيسي',
            'receiver_name'     => $shipment->receiverCustomer?->name ?? 'مستلم غير محدد',
            'receiver_phone'    => $shipment->receiverCustomer?->phone ?? '---',
            'receiver_branch'   => $receiverDestination,
            'receiver_office'   => $shipment->receiverOfficeBranch?->name, // 👈 إضافة اسم المكتب
            'receiver_app'      => $shipment->receiverOfficeBranch->name ?? 'الفرع الرئيسي',
            'sender_office'     => $shipment->senderOfficeBranch?->name, // 👈 إضافة اسم المكتب

            // --- تفاصيل الطرد ---
            'package_type'      => $shipment->package_type ?? 'طرد عادي',
            'parcels_quantity'  => $shipment->parcels_quantity,
            'weight'            => $shipment->weight ? $shipment->weight . ' كجم' : null,
            'honey_details'     => $honeyDetails,
            'notes'             => $shipment->notes ?? 'لا توجد ملاحظات إضافية',

            // --- الحسابات والمبالغ ---
            'is_price_agreed'   => (bool) ($shipment->is_price_agreed ?? true),
            'payment_key'       => $shipment->payment_method ?? 'prepaid',
            'payment_method'    => !($shipment->is_price_agreed ?? true)
                ? 'سيتم الاتفاق عليه لاحقاً'
                : ($paymentMethods[$shipment->payment_method ?? 'prepaid'] ?? 'غير محدد'),
            'transport_fee'     => number_format($transportFee, 0),
            'total_amount'      => number_format($grandTotal, 0),
            'partial_amount'    => number_format($paidAmount, 0),
            'remaining_amount'  => number_format($remainingAmount, 0),
            'other_fee'         => $otherFee,
            'other_fee_description' => $shipment->other_fee_description,
            'show_other_fee_in_receipt' => $showOtherFee,

            // --- معلومات النظام والتصميم ---
            'creator_name'      => $shipment->creator?->name ?? 'مسؤول النظام',
            'print_date'        => now()->format('Y-m-d H:i'),
            'terms_and_conditions' => (is_array($app?->terms_and_conditions) && count($app->terms_and_conditions) > 0) 
                ? $app->terms_and_conditions 
                : ['لا توجد شروط وأحكام'],
            'design' => [
                'primary_color'   => $theme['primary'] ?? '#ea580c',
                'secondary_color' => $theme['secondary'] ?? '#1e293b',
                'bg_color'        => $theme['bg_light'] ?? '#fffaf5',
                'font_family'     => "'aealarabiya', 'dejavusans', sans-serif",
                'paper_size'      => 'thermal',
            ]
        ];
    }

    public function getTemplatePath(): string
    {
        return 'receipts.templates.ThermalShipmentReceipt';
    }

    public function getFileName(array $data): string
    {
        return 'Thermal_Receipt_' . $data['bond_number'] . '.pdf';
    }
}