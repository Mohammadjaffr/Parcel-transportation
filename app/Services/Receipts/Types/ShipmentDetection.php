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
            'senderBranch',
            'driver',
            'creator',
            'shipments.senderCustomer',
            'shipments.receiverCustomer',
            'shipments.senderBranch',
            'shipments.receiverBranch',
            'shipments.receiverOfficeBranch',
        ])->where('uuid', $referenceId)->firstOrFail();

        $app = auth()->user()->app;

        $logoPath = $app?->logo
            ? public_path('storage/' . $app->logo)
            : public_path('assets/image/icon_without_bg.png');

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
            foreach($allBranches as $b) {
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
        foreach($package->shipments as $shipment) {
            $receiverDestination = $shipment->receiverBranch?->name
                ?? $shipment->receiverOfficeBranch?->name
                ?? 'الوجهة غير محددة';

            $honeyDetails = ($shipment->no_gallons_honey || $shipment->no_honey_jars)
                ? "جوالين: " . ($shipment->no_gallons_honey ?? 0) . " | دبات: " . ($shipment->no_honey_jars ?? 0)
                : null;

            $shipmentsData[] = [
                'bond_number'       => $shipment->bond_number ?? '---',
                'tracking_code'     => $shipment->code ?? '---',
                'sender_name'       => $shipment->senderCustomer?->name ?? 'عميل نقدي',
                'sender_phone'      => $shipment->senderCustomer?->phone ?? '---',
                'receiver_name'     => $shipment->receiverCustomer?->name ?? 'مستلم غير محدد',
                'receiver_phone'    => $shipment->receiverCustomer?->phone ?? '---',
                'sender_branch'     => $shipment->senderBranch?->name ?? '---',
                'receiver_branch'   => $receiverDestination,
                'package_type'      => $shipment->package_type ?? 'طرد',
                'weight'            => $shipment->weight ? $shipment->weight . ' كجم' : null,
                'payment_key'       => $shipment->payment_method ?? 'prepaid',
                'payment_method'    => $paymentMethods[$shipment->payment_method ?? 'prepaid'] ?? 'غير محدد',
                'total_amount'      => number_format($shipment->total_amount ?? 0, 0),
                'partial_amount'    => number_format($shipment->partial_amount ?? 0, 0),
                'remaining_amount'  => number_format(($shipment->total_amount ?? 0) - ($shipment->partial_amount ?? 0), 0),
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
                'logo'        => $logoPath,
                'main_branch' => $mainBranchData,
                'headquarters'=> $headquartersData,
                'other_phones'=> $otherPhonesStr,
            ],

            'title'             => 'كشف حمولة الرسائل',
            'package_number'    => $package->tracking_number ?? 'غير متوفر',
            'date'              => $package->created_at ? $package->created_at->format('Y-m-d h:i A') : now()->format('Y-m-d h:i A'),
            
            'driver_name'       => $package->driver?->name ?? 'غير محدد',
            'driver_phone'      => $package->driver?->phone ?? '---',
            'package_sender_branch' => $package->senderBranch?->name ?? '---',
            'total_shipments'   => $totalShipmentsCount,

            'shipments'         => $shipmentsData,

            'creator_name'      => $package->creator?->name ?? 'مسؤول النظام',
            'print_date' => Carbon::now()
                ->locale('ar')
                ->translatedFormat('l Y-m-d H:i'),
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