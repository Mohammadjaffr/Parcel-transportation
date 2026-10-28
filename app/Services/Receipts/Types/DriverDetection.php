<?php

namespace App\Services\Receipts\Types;

use App\Models\ShipmentPackage;
use App\Interfaces\ReceiptStrategyInterface;
use App\Services\Receipts\Concerns\FormatsReceiptHeaderData;
use Carbon\Carbon;

class DriverDetection implements ReceiptStrategyInterface
{
    use FormatsReceiptHeaderData;

    public function sizepage(): string|array
    {
        return 'A4 landscape';
    }

    public function fetchData(string $referenceId): array
    {
        // 1. جلب الإرسالية المجمعة (الرحلة) مع طرودها وعلاقة الشركة (.app)
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

        // 2. جلب بيانات التطبيق (الشركة) من الرحلة
        $app = $package->senderBranch?->app ?? $package->creator?->app;
        if (!$app && auth()->check()) {
            $app = auth()->user()->App ?? auth()->user()->app;
        }

        // 3. تجهيز مسار الشعار والفروع
        $logoBase64 = $this->getLogoBase64($app);
        $branchAddresses = $this->getBranchAddresses($app);

        // 4. إعداد أرقام الفروع
        $senderBranch = $package->senderBranch;
        $mainBranchData = null;
        if ($senderBranch) {
            $mainBranchData = [
                'title' => 'فرع / ' . $senderBranch->name . ($senderBranch->address ? ' - ' . $senderBranch->address : ''),
                'phones' => $this->formatPhoneNumber($senderBranch->phone)
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

            $shipmentsData[] = [
                'id'                => $shipment->id,
                'bond_number'       => $shipment->bond_number ?? 'غير متوفر',
                'sender_name'       => $shipment->senderCustomer?->name ?? 'غير محدد',
                'sender_phone'      => $this->formatPhoneNumber($shipment->senderCustomer?->phone),
                'receiver_name'     => $shipment->receiverCustomer?->name ?? 'غير محدد',
                'receiver_phone'    => $this->formatPhoneNumber($shipment->receiverCustomer?->phone),
                'sender_branch'     => $shipment->senderBranch?->name ?? '---',
                'receiver_branch'   => $receiverDestination,
                'package_type'      => $shipment->package_type ?? 'طرد',
                'weight'            => $shipment->weight,
                'payment_method'    => $paymentMethods[$shipment->payment_method ?? 'prepaid'] ?? 'غير محدد',
                'total_amount'      => number_format($shipment->total_amount ?? 0, 0),
                'paid_amount'       => number_format($shipment->partial_amount ?? 0, 0),
                'remaining_amount'  => number_format(($shipment->total_amount ?? 0) - ($shipment->partial_amount ?? 0), 0),
                'notes'             => $shipment->notes,
                'honey_details'     => $honeyDetails,
            ];
            $totalShipmentsCount++;
        }

        // 7. الثيم والألوان
        $theme = $app?->theme ?? [
            'primary'   => '#fb6514',
            'secondary' => '#333333',
            'bg_light'  => '#fcfcfc',
        ];

        $dateInfo = $this->prepareDateInfo($package->created_at);

        // 8. إرجاع البيانات المنظمة
        return [
            'company' => [
                'name'         => $app?->name ?? 'اسم الشركة غير محدد',
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

            'title'             => 'كشف شحنات السائق',
            'package_number'    => $package->id ?? 'غير متوفر',
            'manifest_number'   => $package->id ?? 'غير متوفر',
            'date'              => $dateInfo['date_full'],

            'driver_name'       => $package->driver?->name ?? 'غير محدد',
            'driver_phone'      => $this->formatPhoneNumber($package->driver?->phone),
            'driver_vehicle_type' => $package->driver?->vehicle_type ?? '---',
            'vehicle_type'      => $package->driver?->vehicle_type ?? '---',
            'package_sender_branch' => $package->senderBranch?->name ?? '---',
            'sender_branch'     => $package->senderBranch?->name ?? 'المركز الرئيسي',
            'total_shipments'   => $totalShipmentsCount,

            'shipments'         => $shipmentsData,

            'creator_name'      => $package->creator?->name ?? 'مسؤول النظام',
            'print_date'        => now()->timezone('Asia/Aden')->format('Y-m-d h:i A'),

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
        return 'receipts.templates.DriverDetection';
    }

    public function getFileName(array $data): string
    {
        return 'Manifest_' . $data['package_number'] . '.pdf';
    }
}
