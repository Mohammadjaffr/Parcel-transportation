<?php

namespace App\Services\Receipts\Types;

use App\Models\ShipmentPackage;
use App\Interfaces\ReceiptStrategyInterface;
use Carbon\Carbon;
use App\Models\OfficeBranch;

class ExternalOfficeDetection implements ReceiptStrategyInterface
{
    public function sizepage(): string|array
    {
        return array(300, 300);
    }

    public function fetchData(string $referenceId): array
    {
        $officeBranchId = request('office_branch_id');
        $officeBranch = null;
        if ($officeBranchId) {
            $officeBranch = OfficeBranch::with('office')->find($officeBranchId);
        }

        // 1. جلب الإرسالية المجمعة (الرحلة) مع طرودها وعلاقة الشركة (.app)
        $package = ShipmentPackage::with([
            'senderBranch.app', // 👈 إضافة .app
            'driver',
            'creator.app',      // 👈 إضافة .app
            'shipments' => function($q) use ($officeBranchId) {
                if ($officeBranchId) {
                    $q->where('receiver_office_branch_id', $officeBranchId);
                }
            },
            'shipments.senderCustomer',
            'shipments.receiverCustomer',
            'shipments.senderBranch',
            'shipments.receiverBranch',
            'shipments.receiverOfficeBranch',
        ])->where('uuid', $referenceId)->firstOrFail();

        // 2. جلب بيانات التطبيق (الشركة) من الرحلة - آمن جداً
        $app = $package->senderBranch?->app ?? $package->creator?->app;

        // 3. تجهيز مسار الشعار الديناميكي - محمي
        $imagePath = $app?->logo
            ? public_path('storage/' . $app->logo)
            : public_path('assets/image/icon_without_bg.png');

        $logoBase64 = null;
        if (file_exists($imagePath)) {
            $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
            $data = file_get_contents($imagePath);
            $logoBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($data);
        }

        // 4. إعداد أرقام الفروع
        $senderBranch = $package->senderBranch;

        $mainBranchData = null;
        if ($senderBranch) {
            $mainBranchData = [
                'title' => 'فرع / ' . $senderBranch->name . ($senderBranch->address ? ' - ' . $senderBranch->address : ''),
                'phones' => implode(' - ', array_filter(array_map('trim', preg_split('/[\s,\-]+/', $senderBranch->phone ?? ''))))
            ];
        }

        $otherPhonesList = [];
        $headquartersData = null;

        // 🛡️ حماية: لن يدخل هنا إلا لو كان $app موجوداً
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
            foreach ($allBranches as $b) {
                if ($senderBranch && $b->id === $senderBranch->id) {
                    continue;
                }
                $phonesArray = array_filter(array_map('trim', preg_split('/[\s,\-]+/', $b->phone ?? '')));
                $otherPhonesList = array_merge($otherPhonesList, $phonesArray);
            }
        }
        $otherPhonesStr = !empty($otherPhonesList) ? implode(' - ', array_unique($otherPhonesList)) : null;

        // 5. القواميس
        $paymentMethods = [
            'prepaid'         => 'مدفوع مقدماً',
            'cod'             => 'الدفع عند الاستلام',
            'partial_payment' => 'دفع جزئي',
            'customer_credit' => 'آجل (ذمة)'
        ];

        // 6. تجهيز الطرود
        $shipmentsData = [];
        $totalShipmentsCount = 0;
        foreach ($package->shipments as $shipment) {
            $receiverDestination = $shipment->receiverBranch?->name
                ?? $shipment->receiverOfficeBranch?->name
                ?? 'الوجهة غير محددة';

            $honeyDetails = ($shipment->no_gallons_honey || $shipment->no_honey_jars)
                ? "جوالين: " . ($shipment->no_gallons_honey ?? 0) . " | قروف: " . ($shipment->no_honey_jars ?? 0)
                : null;

            $paymentMethod = $shipment->payment_method ?? 'prepaid';
            $totalAmount = $shipment->total_amount ?? 0;
            $partialAmount = $shipment->partial_amount ?? 0;

            if ($paymentMethod === 'prepaid') {
                $paidAmount = $totalAmount;
                $remainingAmount = 0;
            } elseif ($paymentMethod === 'cod') {
                $paidAmount = 0;
                $remainingAmount = $totalAmount;
            } elseif ($paymentMethod === 'partial_payment') {
                $paidAmount = $partialAmount;
                $remainingAmount = $totalAmount - $partialAmount;
            } elseif ($paymentMethod === 'customer_credit') {
                $paidAmount = 0;
                $remainingAmount = 0;
            } else {
                $paidAmount = 0;
                $remainingAmount = 0;
            }

            $shipmentsData[] = [
                'bond_number'          => $shipment->bond_number ?? '---',
                'tracking_code'        => $shipment->code ?? '---',
                'sender_name'          => $shipment->senderCustomer?->name ?? 'عميل نقدي',
                'sender_phone'         => $shipment->senderCustomer?->phone ?? '---',
                'receiver_name'        => $shipment->receiverCustomer?->name ?? 'مستلم غير محدد',
                'receiver_phone'       => $shipment->receiverCustomer?->phone ?? '---',
                'sender_branch'        => $shipment->senderBranch?->name ?? '---',
                'receiver_branch'      => $receiverDestination,
                'package_type'         => $shipment->package_type ?? 'طرد',
                'weight'               => $shipment->weight ? $shipment->weight . ' كجم' : null,
                'payment_key'          => $paymentMethod,
                'payment_method'       => $paymentMethods[$paymentMethod] ?? 'غير محدد',
                'total_amount'         => number_format($totalAmount, 0),
                'paid_amount'          => number_format($paidAmount, 0),
                'remaining_amount'     => $paymentMethod === 'customer_credit' ? 'على حساب المرسل' : number_format($remainingAmount, 0),
                'raw_total_amount'     => $totalAmount,
                'raw_paid_amount'      => $paidAmount,
                'raw_remaining_amount' => $remainingAmount,
                'notes'                => $shipment->notes,
                'honey_details'        => $honeyDetails,
            ];
            $totalShipmentsCount++;
        }

        // 7. الثيم والألوان - آمن
        $theme = $app?->theme ?? [
            'primary'   => '#fb6514',
            'secondary' => '#333333',
            'bg_light'  => '#fcfcfc',
        ];

        $officeTitle = $officeBranch 
            ? 'مكتب ' . ($officeBranch->office->name ?? '') . ' - ' . $officeBranch->name 
            : 'مكتب خارجي';

        // 8. إرجاع البيانات المنظمة
        return [
            'company' => [
                'name'         => $app?->name ?? 'اسم الشركة غير محدد',
                'logo'         => $logoBase64,
                'main_branch'  => $mainBranchData,
                'headquarters' => $headquartersData,
                'other_phones' => $otherPhonesStr,
            ],

            'title'             => 'كشف إرسالية - ' . $officeTitle,
            'package_number'    => $package->tracking_number ?? 'غير متوفر',
            'date'              => $package->created_at ? $package->created_at->format('Y-m-d h:i A') : now()->format('Y-m-d h:i A'),

            'driver_name'       => $package->driver?->name ?? 'غير محدد',
            'driver_phone'      => $package->driver?->phone ?? '---',
            'package_sender_branch' => $package->senderBranch?->name ?? '---',
            'package_receiver_office' => $officeBranch ? 'مكتب ' . ($officeBranch->office->name ?? '') . ' - ' . $officeBranch->name : 'غير محدد',
            'package_receiver_phone' => $officeBranch->phone ?? '---',
            'total_shipments'   => $totalShipmentsCount,

            'shipments'         => $shipmentsData,

            'creator_name'      => $package->creator?->name ?? 'مسؤول النظام',
            'print_date'        => Carbon::now()->locale('ar')->translatedFormat('l Y-m-d H:i'),
                
            'design' => [
                'primary_color'   => $theme['primary'],
                'secondary_color' => $theme['secondary'],
                'bg_color'        => $theme['bg_light'],
                'font_family'     => "'aealarabiya', 'dejavusans', sans-serif",
                'paper_size'      => 'a4',
            ]
        ];
    }

    public function getTemplatePath(): string
    {
        return 'receipts.templates.ExternalOfficeDetection'; // Same template is fine since we passed the new title
    }

    public function getFileName(array $data): string
    {
        return 'ExternalOffice_Manifest_' . $data['package_number'] . '.pdf';
    }
}
