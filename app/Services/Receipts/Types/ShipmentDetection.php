<?php

namespace App\Services\Receipts\Types;

use App\Models\ShipmentPackage;
use App\Interfaces\ReceiptStrategyInterface;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ShipmentDetection implements ReceiptStrategyInterface
{
    public function sizepage(): string|array
    {
        return array(300, 300);
    }

    public function fetchData(string $referenceId): array
    {
        // 1. جلب الإرسالية المجمعة (الرحلة) مع طرودها
        $package = ShipmentPackage::with([
            'senderBranch.app',
            'driver',
            'creator.app',
            'shipments.senderCustomer',
            'shipments.receiverCustomer',
            'shipments.senderBranch',
            'shipments.receiverBranch',
            'shipments.receiverOfficeBranch',
        ])->where('uuid', $referenceId)->firstOrFail();

        $app = $package->senderBranch?->app ?? $package->creator?->app;

        $imagePath = $app?->logo
            ? public_path('storage/' . $app->logo)
            : public_path('assets/image/icon_without_bg.png');

        $logoBase64 = null;
        if (file_exists($imagePath)) {
            $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
            $data = file_get_contents($imagePath);
            $logoBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($data);
        }

        // 2. إعداد أرقام الفروع
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

        // 3. القواميس
        $paymentMethods = [
            'prepaid'         => 'مدفوع مقدماً',
            'cod'             => 'الدفع عند الاستلام',
            'partial_payment' => 'دفع جزئي',
            'customer_credit' => 'آجل (ذمة)'
        ];

        // 4. تجهيز الطرود
        $shipmentsData = [];
        $totalShipmentsCount = 0;
        $totalPackageComm = 0;
        $totalHoneyComm = 0;

        foreach ($package->shipments as $shipment) {
            $receiverDestination = $shipment->receiverBranch?->name
                ?? $shipment->receiverOfficeBranch?->name
                ?? 'الوجهة غير محددة';

            $honeyDetails = ($shipment->no_gallons_honey || $shipment->no_honey_jars)
                ? "جوالين: " . ($shipment->no_gallons_honey ?? 0) . " | دبات: " . ($shipment->no_honey_jars ?? 0)
                : null;

            // 💡 الحسبة المالية الصحيحة بناءً على نوع الدفع
            $totalAmount = (float) ($shipment->total_amount ?? 0);
            $paymentKey = $shipment->payment_method ?? 'prepaid';

            if ($paymentKey === 'prepaid' || $paymentKey === 'customer_credit') {
                $paidAmount = $totalAmount;
                $remainingAmount = 0;
            } elseif ($paymentKey === 'partial_payment') {
                $paidAmount = (float) ($shipment->partial_amount ?? 0);
                $remainingAmount = max(0, $totalAmount - $paidAmount);
            } else {
                // cod أو 
                $paidAmount = 0;
                $remainingAmount = $totalAmount;
            }
            $totalPackageComm += (float) ($shipment->package_commission_amount ?? 0);
            $totalHoneyComm   += (float) ($shipment->honey_commission_amount ?? 0);

            $shipmentsData[] = [
                'bond_number'       => $shipment->id ?? '---',
                'tracking_code'     => $shipment->code ?? '---',
                'sender_name'       => $shipment->senderCustomer?->name ?? 'عميل نقدي',
                'sender_phone'      => $shipment->senderCustomer?->phone ?? '---',
                'receiver_name'     => $shipment->receiverCustomer?->name ?? 'مستلم غير محدد',
                'receiver_phone'    => $shipment->receiverCustomer?->phone ?? '---',
                'sender_branch'     => $shipment->senderBranch?->name ?? '---',
                'receiver_branch'   => $receiverDestination,
                'package_type'      => $shipment->package_type ?? 'طرد',
                'weight'            => $shipment->weight ? $shipment->weight . ' كجم' : null,
                'payment_key'       => $paymentKey,
                'payment_method'    => $paymentMethods[$paymentKey] ?? 'غير محدد',

                // 💡 المتغيرات بعد الحسبة الدقيقة
                'total_amount'      => number_format($totalAmount, 0),
                'partial_amount'    => number_format($paidAmount, 0),
                'remaining_amount'  => number_format($remainingAmount, 0),

                'notes'             => $shipment->notes,
                'honey_details'     => $honeyDetails,
            ];
            $totalShipmentsCount++;
        }

        // 5. الثيم والألوان
        $theme = $app ? $app->theme : [
            'primary'   => '#fb6514',
            'secondary' => '#333333',
            'bg_light'  => '#fff4ee',
        ];

        // 6. إرجاع البيانات المنظمة
        return [
            'company' => [
                'name'        => $app?->name ?? 'اسم الشركة غير محدد',
                'logo'        => $logoBase64,
                'main_branch' => $mainBranchData,
                'headquarters' => $headquartersData,
                'other_phones' => $otherPhonesStr,
            ],

            'title'             => 'كشف حمولة الرسائل',
            'package_number'    => $package->tracking_number ?? 'غير متوفر',

            // إصلاح التوقيت للـ AM/PM والمنطقة الزمنية
            'date'              => str_replace(
                ['AM', 'PM'],
                ['صباحاً', 'مساءً'],
                ($package->created_at
                    ? $package->created_at->timezone('Asia/Aden')->format('Y-m-d h:i A')
                    : now()->timezone('Asia/Aden')->format('Y-m-d h:i A'))
            ),

            'driver_name'       => $package->driver?->name ?? 'غير محدد',
            'driver_phone'      => $package->driver?->phone ?? '---',
            'package_sender_branch' => $package->senderBranch?->name ?? '---',
            'total_shipments'   => $totalShipmentsCount,

            'shipments'         => $shipmentsData,

            'creator_name'      => $package->creator?->name ?? 'مسؤول النظام',

            // إصلاح توقيت الطباعة
            'print_date'        => str_replace(['AM', 'PM'], ['صباحاً', 'مساءً'], now()->timezone('Asia/Aden')->format('Y-m-d h:i A')),
            'totals' => [
                'package_commission' => $totalPackageComm,
                'honey_commission'   => $totalHoneyComm,
                'grand_commission'   => $totalPackageComm + $totalHoneyComm,
            ],
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
        return 'receipts.templates.ShipmentDetection';
    }

    public function getFileName(array $data): string
    {
        return 'Manifest_' . $data['package_number'] . '.pdf';
    }
}
