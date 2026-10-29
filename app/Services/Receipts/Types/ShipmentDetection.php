<?php

namespace App\Services\Receipts\Types;

use App\Models\ShipmentPackage;
use App\Interfaces\ReceiptStrategyInterface;
use App\Services\Receipts\Concerns\FormatsReceiptHeaderData;
use Carbon\Carbon;

class ShipmentDetection implements ReceiptStrategyInterface
{
    use FormatsReceiptHeaderData;

    public function sizepage(): string|array
    {
        return 'A4 landscape';
    }

    public function fetchData(string $referenceId): array
    {
        // 1. جلب الإرسالية المجمعة مع طرودها وعلاقة الشركة
        $package = ShipmentPackage::with([
            'senderBranch.app',
            'driver',
            'creator.app',
            'creator.branch',
            'shipments.senderCustomer',
            'shipments.receiverCustomer',
            'shipments.senderBranch',
            'shipments.receiverBranch',
            'shipments.receiverOfficeBranch',
        ])->where('uuid', $referenceId)->firstOrFail();

        $app = $package->senderBranch?->app ?? $package->creator?->app;
        if (!$app && auth()->check()) {
            $app = auth()->user()->App ?? auth()->user()->app;
        }

        $logoBase64 = $this->getLogoBase64($app);
        $branchAddresses = $this->getBranchAddresses($app);

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
                if ($senderBranch && $b->id === $senderBranch->id) continue;
                $phonesArray = array_filter(array_map('trim', preg_split('/[\s,\-]+/', $b->phone ?? '')));
                $otherPhonesList = array_merge($otherPhonesList, $phonesArray);
            }
        }

        $otherPhonesStr = !empty($otherPhonesList) ? implode(' - ', array_unique($otherPhonesList)) : null;

        $paymentMethods = [
            'prepaid'         => 'مدفوع مقدماً',
            'cod'             => 'الدفع عند الاستلام',
            'partial_payment' => 'دفع جزئي',
            'customer_credit' => 'آجل (ذمة)'
        ];

        $shipmentsData = [];
        $totalShipmentsCount = 0;

        // المتغيرات الحسابية للكشف
        $totalPackageComm = 0;
        $totalHoneyComm = 0;
        $totalExpectedCash = 0;
        $totalAccountantAmounts = 0;
        $totalDeferredAmounts = 0;
        $isReceiver = false;
        $totalPackageFee = 0;
        $totalOtherFee = 0;

        foreach ($package->shipments as $shipment) {
            if (!empty($shipment->code)) {
                $isReceiver = true;
            }

            $receiverDestination = $shipment->receiverBranch?->name
                ?? $shipment->receiverOfficeBranch?->name
                ?? 'الوجهة غير محددة';

            $honeyDetails = ($shipment->no_gallons_honey || $shipment->no_honey_jars)
                ? "جوالين: " . ($shipment->no_gallons_honey ?? 0) . " | قروف: " . ($shipment->no_honey_jars ?? 0)
                : null;

            $totalAmount = (float) ($shipment->total_amount ?? 0);
            $paidAmount = (float) ($shipment->partial_amount ?? 0);
            $remainingAmount = (float) ($shipment->amount_to_collect_from_receiver ?? 0);
            $paymentMethod = $shipment->payment_method ?? 'prepaid';

            $accountantAmount = 0;
            $deferredAmount = 0;

            switch ($paymentMethod) {
                case 'customer_credit':
                case 'cod':
                    $deferredAmount = $remainingAmount > 0 ? $remainingAmount : $totalAmount;
                    break;
                case 'partial_payment':
                    $accountantAmount = $paidAmount;
                    $deferredAmount = $remainingAmount;
                    break;
                case 'prepaid':
                default:
                    $accountantAmount = $totalAmount;
                    break;
            }

            $totalPackageComm += (float) ($shipment->package_commission_amount ?? 0);
            $totalHoneyComm   += (float) ($shipment->honey_commission_amount ?? 0);
            $totalPackageFee  += (float) ($shipment->package_fee ?? 0);
            $otherFee          = (float) ($shipment->other_fee ?? 0);
            $totalOtherFee    += $otherFee;

            $totalExpectedCash += $remainingAmount;
            $totalAccountantAmounts += $accountantAmount;
            $totalDeferredAmounts += $deferredAmount;
            $rowTotal = $accountantAmount + $deferredAmount + $otherFee;

            $shipmentsData[] = [
                'bond_number'       => isset($shipment->bond_number) ? (explode('-', $shipment->bond_number)[1] ?? $shipment->bond_number) : 'غير متوفر',
                'tracking_code'     => isset($shipment->bond_number) ? (explode('-', $shipment->bond_number)[1] ?? $shipment->bond_number) : 'غير متوفر',
                'sender_name'       => $shipment->senderCustomer?->name ?? 'عميل نقدي',
                'sender_phone'      => $this->formatPhoneNumber($shipment->senderCustomer?->phone),
                'receiver_name'     => $shipment->receiverCustomer?->name ?? 'مستلم غير محدد',
                'receiver_phone'    => $this->formatPhoneNumber($shipment->receiverCustomer?->phone),
                'sender_branch'     => $shipment->senderBranch?->name ?? '---',
                'receiver_branch'   => $receiverDestination,
                'package_type'      => $shipment->package_type ?? 'طرد',
                'weight'            => $shipment->weight ? $shipment->weight . ' كجم' : null,
                'is_price_agreed'   => (bool) ($shipment->is_price_agreed ?? true),
                'payment_key'       => $paymentMethod,
                'payment_method'    => !($shipment->is_price_agreed ?? true)
                    ? 'سيتم الاتفاق عليه لاحقاً'
                    : ($paymentMethods[$paymentMethod] ?? 'غير محدد'),

                'accountant_amount' => $accountantAmount > 0 ? number_format($accountantAmount, 0) : '',
                'deferred_amount'   => $deferredAmount > 0 ? number_format($deferredAmount, 0) : '',
                'other_fee'         => $otherFee > 0 ? number_format($otherFee, 0) : '',
                'other_fee_description'     => $shipment->other_fee_description,
                'show_other_fee_in_receipt' => (bool) ($shipment->show_other_fee_in_receipt ?? false),
                'row_total'         => $rowTotal > 0 ? number_format($rowTotal, 0) : '',
                'has_accountant'    => $accountantAmount > 0,
                'has_deferred'      => $deferredAmount > 0,
                'raw_accountant'    => $accountantAmount,
                'raw_deferred'      => $deferredAmount,

                'notes'             => $shipment->notes,
                'honey_details'     => $honeyDetails,
                'total_package_fee' => $totalPackageFee,
            ];
            $totalShipmentsCount++;
        }

        $theme = $app?->theme ?? [
            'primary'   => '#1e3a8a',
            'secondary' => '#fb6514',
            'bg_light'  => '#fcfcfc',
        ];

        $grandCommission = $totalPackageComm + $totalHoneyComm;
        $settlementResult = $totalAccountantAmounts - $grandCommission;

        if ($settlementResult > 0) {
            $settlementStatus = 'pay_to_driver';
            $settlementLabel  = 'صافي مستحق للسائق من المكتب';
            $settlementAmount = abs($settlementResult);
            $settlementColor  = '#16a34a';
        } elseif ($settlementResult < 0) {
            $settlementStatus = 'collect_from_driver';
            $settlementLabel  = 'صافي مستحق للمكتب من السائق';
            $settlementAmount = abs($settlementResult);
            $settlementColor  = '#dc2626';
        } else {
            $settlementStatus = 'settled';
            $settlementLabel  = 'خالص';
            $settlementAmount = 0;
            $settlementColor  = '#4b5563';
        }

        $dateInfo = $this->prepareDateInfo($package->created_at);

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

            'title'             => $isReceiver ? 'كشف إرسال شحنات' : 'كشف إرسال شحنة',
            'package_number'    => $isReceiver ? $package->tracking_number_only ?? $package->id : $package->id,
            'manifest_number'   => $package->tracking_number_only ?? $package->id,
            'date'              => $dateInfo['date_full'],

            'driver_name'       => $package->driver?->name ?? 'غير محدد',
            'driver_phone'      => $this->formatPhoneNumber($package->driver?->phone),
            'driver_vehicle_type' => $package->driver?->vehicle_type ?? '---',
            'vehicle_type'      => $package->driver?->vehicle_type ?? '---',
            'package_sender_branch' => $package->senderBranch?->name ?? '---',
            'sender_branch'     => $package->senderBranch?->name ?? 'المركز الرئيسي',
            'total_shipments'   => $totalShipmentsCount,

            'shipments'         => $shipmentsData,
            'isReceiver'        => $isReceiver,

            'totals' => [
                'total_amounts'      => $totalAccountantAmounts + $totalDeferredAmounts,
                'accountant_amount'  => $totalAccountantAmounts,
                'deferred_amount'    => $totalDeferredAmounts,
                'package_commission' => $totalPackageComm,
                'honey_commission'   => $totalHoneyComm,
                'grand_commission'   => $grandCommission,
                'expected_cash'      => $totalExpectedCash,
                'package_fee'        => $totalPackageFee,
                'other_fee'          => $totalOtherFee,
                'settlement_result'  => $settlementResult,
                'settlement_amount'  => $settlementAmount,
                'settlement_status'  => $settlementStatus,
                'settlement_label'   => $settlementLabel,
                'settlement_color'   => $settlementColor,
            ],
            'creator_name'      => $package->creator?->name ?? 'مسؤول النظام',
            'print_date'        => now()->timezone('Asia/Aden')->format('Y-m-d h:i A'),
            'user_branch'       => $package->creator?->branch?->name ?? $package->senderBranch?->name ?? 'الفرع الرئيسي',
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
        return 'Driver_Manifest_' . $data['package_number'] . '.pdf';
    }
}
