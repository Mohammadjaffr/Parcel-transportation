<?php

namespace App\Services\Receipts\Types;

use App\Models\Shipment;
use App\Interfaces\ReceiptStrategyInterface;
use App\Services\Receipts\Concerns\FormatsReceiptHeaderData;
use Carbon\Carbon;

class ReceiverShipmentReceipt implements ReceiptStrategyInterface
{
    use FormatsReceiptHeaderData;

    public function sizepage(): string|array
    {
        return 'A5 landscape'; // Default size
    }

    public function fetchData(string $referenceId): array
    {
        // 1. جلب الشحنة مع جميع العلاقات الأساسية (بما فيها المكاتب الخارجية)
        $shipment = Shipment::with([
            'senderCustomer' => fn($q) => $q->withoutGlobalScopes(),
            'receiverCustomer' => fn($q) => $q->withoutGlobalScopes(),
            'senderBranch.app',
            'receiverBranch.app',
            'senderOfficeBranch.office',
            'receiverOfficeBranch.office',
            'creator.app',
            'creator.branch'
        ])->where(function ($q) use ($referenceId) {
            $q->where('uuid', $referenceId)
              ->orWhere('id', $referenceId);
        })->firstOrFail();

        // 2. تحديد الفرع الذي سيطبع السند (الفرع المُستلم أو المكتب الخارجي المُستلم)
        $printBranch = $shipment->receiverBranch ?? $shipment->receiverOfficeBranch ?? $shipment->senderBranch ?? $shipment->senderOfficeBranch;

        // جلب بيانات التطبيق (الشركة) بناءً على الفرع المُستلم أو منشئ الشحنة
        $app = $printBranch?->app ?? $shipment->creator?->app;
        if (!$app && auth()->check()) {
            $app = auth()->user()->App ?? auth()->user()->app;
        }

        // 3. تجهيز الشعار وقائمة الفروع ديناميكياً
        $logoBase64 = $this->getLogoBase64($app);
        $branchAddresses = $this->getBranchAddresses($app);

        // 4. إعداد ترويسة السند (Header)
        $mainBranchData = null;
        if ($printBranch) {
            $mainBranchData  = [
                'title' => 'فرع / ' . $printBranch->name . ($printBranch->address ? ' - ' . $printBranch->address : ''),
                'phones' => $this->formatPhoneNumber($printBranch->phone)
            ];
        }

        $otherPhonesList = [];
        $headquartersData = null;

        if ($app) {
            if ($app->phone) {
                $headquartersData = [
                    'title' => 'الفرع الرئيسي' . ($app->address ? ' - ' . $app->address : ''),
                    'phones' => $this->formatPhoneNumber($app->phone)
                ];
            }

            $allBranches = $app->branches()->get();
            foreach ($allBranches as $b) {
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

        // 7. الحسابات المالية الدقيقة بناءً على منطق الموديل وطريقة الدفع
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
        $showOtherFee = (bool) ($shipment->show_other_fee_in_receipt ?? false);
        $transportFee = $totalAmount;
        $grandTotal = $otherFee > 0 ? ($transportFee + $otherFee) : $totalAmount;

        $companyPhone = !empty($app?->phone) ? $this->formatPhoneNumber($app->phone) : null;

        // تفكيك التاريخ واليوم بدقة
        $dateInfo = $this->prepareDateInfo($shipment->created_at);

        return [
            'company' => [
                'name'         => $app?->name ?? 'اسم الشركة غير محدد',
                'phone'        => $companyPhone,
                'logo'         => $logoBase64,
                'main_branch'  => $mainBranchData,
                'headquarters' => $headquartersData,
                'other_phones' => $otherPhonesStr,
                'branch_addresses' => $branchAddresses,
            ],
            'branch_addresses'  => $branchAddresses,

            'day_name'          => $dateInfo['day_name'],
            'formatted_date'    => $dateInfo['formatted_date'],
            'formatted_time'    => $dateInfo['formatted_time'],

            'title'             => 'سند استلام طرد',
            'bond_number'       => isset($shipment->bond_number) ? (explode('-', $shipment->bond_number)[1] ?? $shipment->bond_number) : 'غير متوفر',
            'tracking_code'     => isset($shipment->bond_number) ? (explode('-', $shipment->bond_number)[1] ?? $shipment->bond_number) : 'غير متوفر',
            
            'date'              => $dateInfo['date_full'],

            // --- بيانات المرسل والمستلم ---
            'sender_name'       => $shipment->senderCustomer?->name ?? 'عميل نقدي (غير مسجل)',
            'sender_phone'      => $this->formatPhoneNumber($shipment->senderCustomer?->phone),

            'sender_branch'     => $senderSource,
            'sender_office'     => $shipment->senderBranch?->app?->name ?? $shipment->senderOfficeBranch?->office?->name ?? $app?->name ?? 'الفرع الرئيسي',
            'sender_branch_phone' => $this->formatPhoneNumber($shipment->senderBranch?->phone ?? $shipment->senderOfficeBranch?->phone),

            'receiver_name'     => $shipment->receiverCustomer?->name ?? 'مستلم غير محدد',
            'receiver_phone'    => $this->formatPhoneNumber($shipment->receiverCustomer?->phone),
            'receiver_branch'   => $receiverDestination,
            'receiver_office'   => $shipment->receiverBranch?->app?->name ?? $shipment->receiverOfficeBranch?->office?->name ?? $app?->name ?? 'الفرع الرئيسي',

            // --- تفاصيل الطرد ---
            'package_type'      => $shipment->package_type ?? ($honeyDetails ? null : 'طرد عادي'),
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

            // المبالغ المالية
            'transport_fee'     => number_format($transportFee, 0),     // تكلفة النقل
            'total_amount'      => number_format($grandTotal, 0),       // المبلغ الإجمالي
            'partial_amount'    => number_format($paidAmount, 0),       // المبلغ المسدد
            'remaining_amount'  => number_format($remainingAmount, 0),  // المبلغ المتبقي
            'other_fee'         => $otherFee,
            'other_fee_description' => $shipment->other_fee_description,
            'show_other_fee_in_receipt' => $showOtherFee,

            'creator_name'      => $shipment->creator?->name ?? 'مسؤول النظام',
            'print_date'        => now()->timezone('Asia/Aden')->format('Y-m-d h:i A'),
            'user_branch'       => $shipment->creator?->branch?->name ?? $shipment->receiverBranch?->name ?? 'الفرع الرئيسي',
            'terms_and_conditions' => (is_array($app?->terms_and_conditions) && count($app->terms_and_conditions) > 0)
                ? $app->terms_and_conditions
                : ['نحن غير مسؤولين عن الإجراءات الأمنية الخارجة عن إرادتنا.', 'يرجى مراجعة الطرد قبل مغادرة الفرع.'],

            'design' => [
                'primary_color'   => $theme['primary'] ?? '#ea580c',
                'secondary_color' => $theme['secondary'] ?? '#1e293b',
                'bg_color'        => $theme['bg_light'] ?? '#fffaf5',
                'font_family'     => "'aealarabiya', 'dejavusans', sans-serif",
                'paper_size'      => 'A5 landscape',
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
