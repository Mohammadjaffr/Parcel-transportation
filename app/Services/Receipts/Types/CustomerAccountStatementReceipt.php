<?php

namespace App\Services\Receipts\Types;

use App\Models\Customer;
use App\Models\CustomerTransaction;
use App\Models\Shipment;
use App\Interfaces\ReceiptStrategyInterface;
use App\Services\Receipts\Concerns\FormatsReceiptHeaderData;
use Carbon\Carbon;

class CustomerAccountStatementReceipt implements ReceiptStrategyInterface
{
    use FormatsReceiptHeaderData;

    public function sizepage(): string|array
    {
        return 'A4 landscape';
    }

    public function fetchData(string $referenceId): array
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $app = $user?->app;
        $appId = $user?->app_id;
        $branchId = $user?->branch_id;

        /*
        |--------------------------------------------------------------------------
        | 1. بيانات العميل
        |--------------------------------------------------------------------------
        */
        $customer = Customer::with('branch')
            ->where(function ($query) use ($appId) {
                if ($appId) {
                    $query->where('app_id', $appId);
                }
            })
            ->where(function ($query) use ($referenceId) {
                $query->where('uuid', $referenceId);

                if (is_numeric($referenceId)) {
                    $query->orWhere('id', $referenceId);
                }
            })
            ->firstOrFail();

        if (!$app) {
            $app = $customer->app ?? $customer->branch?->app;
        }

        /*
        |--------------------------------------------------------------------------
        | 2. بيانات الشركة / الشعار / الفروع
        |--------------------------------------------------------------------------
        */
        $appName = $user?->cached_app_name ?? $app?->name ?? 'اسم الشركة غير محدد';
        $logoBase64 = $this->getLogoBase64($app);
        $branchAddresses = $this->getBranchAddresses($app);

        $mainBranchData = null;
        if ($customer->branch) {
            $mainBranchData = [
                'title' => 'فرع / ' . $customer->branch->name . ($customer->branch->address ? ' - ' . $customer->branch->address : ''),
                'phones' => $this->formatPhoneNumber($customer->branch->phone),
            ];
        }

        $headquartersData = null;
        if ($app?->phone) {
            $headquartersData = [
                'title'  => 'الفرع الرئيسي' . ($app->address ? ' - ' . $app->address : ''),
                'phones' => $this->formatPhoneNumber($app->phone),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | 3. جلب الشحنات المرتبطة بالعميل
        |--------------------------------------------------------------------------
        */
        $shipments = Shipment::with([
                'senderBranch',
                'receiverBranch',
                'senderCustomer',
                'receiverCustomer',
                'payments',
            ])
            ->where(function ($query) use ($customer) {
                $query->where('sender_customer_id', $customer->id)
                    ->orWhere('receiver_customer_id', $customer->id);
            })
            ->where(function ($query) use ($appId) {
                if ($appId) {
                    $query->whereHas('senderBranch', fn($q) => $q->where('app_id', $appId));
                }
            })
            ->orderBy('created_at', 'asc')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 4. جلب سندات القبض/الصرف الخاصة بالعميل
        |--------------------------------------------------------------------------
        */
        $transactions = CustomerTransaction::where('customer_id', $customer->id)
            ->orderBy('created_at', 'asc')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 5. تجهيز جدول الشحنات المخصصة
        |--------------------------------------------------------------------------
        */
        $shipmentRows = $shipments->map(function ($s) use ($customer) {
            $isSender = (int) $s->sender_customer_id === (int) $customer->id;
            $direction = $isSender ? 'sent' : 'received';

            $totalAmount = (float) ($s->total_amount ?? 0);
            $paid = (float) $s->payments->sum('amount');
            $remaining = (float) ($s->amount_to_collect_from_receiver ?? max(0, $totalAmount - $paid));

            return [
                'id' => $s->id,
                'uuid' => $s->uuid,
                'bond_number' => $s->bond_number ?? '---',
                'direction' => $direction,
                'direction_label' => $isSender ? 'مرسلة' : 'مستقبلة',
                'other_party_name' => $isSender
                    ? ($s->receiverCustomer->name ?? '---')
                    : ($s->senderCustomer->name ?? '---'),
                'other_party_phone' => $isSender
                    ? ($s->receiverCustomer->phone ?? '---')
                    : ($s->senderCustomer->phone ?? '---'),
                'route' => ($s->senderBranch->name ?? '---') . ' ➔ ' . ($s->receiverBranch->name ?? '---'),
                'payment_method' => $this->paymentMethodLabel($s->payment_method),
                'total_amount' => $totalAmount,
                'paid_amount' => $paid,
                'remaining_amount' => $remaining,
                'status' => $this->statusLabel($s->status),
                'date' => $s->created_at ? $s->created_at->format('Y-m-d') : '---',
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | 6. بناء الحركات المالية لكشف الحساب
        |--------------------------------------------------------------------------
        */
        $entries = collect();

        foreach ($shipments as $shipment) {
            $isSender = (int) $shipment->sender_customer_id === (int) $customer->id;
            $shipmentAmount = (float) ($shipment->total_amount ?? 0);
            $paid = (float) $shipment->payments->sum('amount');

            if (in_array($shipment->payment_method, ['customer_credit', 'partial_payment'])) {
                $description = $isSender
                    ? 'شحنة صادرة إلى ' . ($shipment->receiverCustomer->name ?? '---') . ' - سند #' . ($shipment->bond_number ?? $shipment->id)
                    : 'شحنة واردة من ' . ($shipment->senderCustomer->name ?? '---') . ' - سند #' . ($shipment->bond_number ?? $shipment->id);

                $entries->push([
                    'date'           => $shipment->created_at ? $shipment->created_at->format('Y-m-d H:i') : '---',
                    'raw_date'       => $shipment->created_at,
                    'type'           => 'shipment',
                    'type_label'     => 'شحنة آجل',
                    'description'    => $description,
                    'reference'      => $shipment->bond_number ?? $shipment->id,
                    'debit'          => $shipmentAmount,
                    'credit'         => 0,
                    'payment_method' => $this->paymentMethodLabel($shipment->payment_method),
                    'notes'          => $shipment->notes,
                ]);

                if ($paid > 0) {
                    $entries->push([
                        'date'           => $shipment->created_at ? $shipment->created_at->format('Y-m-d H:i') : '---',
                        'raw_date'       => $shipment->created_at,
                        'type'           => 'payment',
                        'type_label'     => 'سداد شحنة',
                        'description'    => 'دفعة مسددة على سند #' . ($shipment->bond_number ?? $shipment->id),
                        'reference'      => $shipment->bond_number ?? $shipment->id,
                        'debit'          => 0,
                        'credit'         => $paid,
                        'payment_method' => $this->paymentMethodLabel($shipment->payment_method),
                        'notes'          => null,
                    ]);
                }
            }
        }

        foreach ($transactions as $trx) {
            $isCredit = $trx->type === 'credit';
            $amount = (float) ($trx->amount ?? 0);

            $entries->push([
                'date'           => $trx->created_at ? $trx->created_at->format('Y-m-d H:i') : '---',
                'raw_date'       => $trx->created_at,
                'type'           => 'transaction',
                'type_label'     => $isCredit ? 'سند قبض' : 'سند صرف',
                'description'    => $trx->description ?? ($isCredit ? 'تحصيل نقدي / سند قبض' : 'صرف / قيد مديونية'),
                'reference'      => '#' . $trx->id,
                'debit'          => $isCredit ? 0 : $amount,
                'credit'         => $isCredit ? $amount : 0,
                'payment_method' => $this->paymentMethodLabel($trx->payment_method),
                'notes'          => $trx->notes,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 7. ترتيب الحركات زمنياً
        |--------------------------------------------------------------------------
        */
        $entries = $entries->sortBy(function ($item) {
            return $item['raw_date'] ? Carbon::parse($item['raw_date'])->timestamp : 0;
        })->values();

        /*
        |--------------------------------------------------------------------------
        | 8. احتساب الرصيد التراكمي
        |--------------------------------------------------------------------------
        */
        $runningBalance = 0;
        $entries = $entries->map(function ($entry) use (&$runningBalance) {
            $runningBalance += ((float) $entry['debit'] - (float) $entry['credit']);
            $entry['balance'] = $runningBalance;
            return $entry;
        });

        /*
        |--------------------------------------------------------------------------
        | 9. المجاميع النهائية
        |--------------------------------------------------------------------------
        */
        $totalDebit = (float) $entries->sum('debit');
        $totalCredit = (float) $entries->sum('credit');
        $finalBalance = $totalDebit - $totalCredit;

        $totalShipmentsAmount = (float) $shipmentRows->sum('total_amount');
        $totalShipmentsPaid = (float) $shipmentRows->sum('paid_amount');
        $totalShipmentsRemaining = (float) $shipmentRows->sum('remaining_amount');

        $sentCount = $shipmentRows->where('direction', 'sent')->count();
        $receivedCount = $shipmentRows->where('direction', 'received')->count();

        $theme = $app ? $app->theme : [
            'primary'   => '#fb6514',
            'secondary' => '#333333',
            'bg_light'  => '#fff4ee',
        ];

        $dateInfo = $this->prepareDateInfo(now());

        return [
            'company' => [
                'name'         => $appName,
                'logo'         => $logoBase64,
                'main_branch'  => $mainBranchData,
                'headquarters' => $headquartersData,
                'branch_addresses' => $branchAddresses,
            ],
            'branch_addresses'  => $branchAddresses,

            'day_name'          => $dateInfo['day_name'],
            'formatted_date'    => $dateInfo['formatted_date'],
            'formatted_time'    => $dateInfo['formatted_time'],

            'title'             => 'كشف حساب عميل شامل',
            'package_number'    => $customer->id,
            'manifest_number'   => $customer->id,
            'sender_branch'     => $customer->branch?->name ?? 'المركز الرئيسي',

            'customer' => [
                'id'     => $customer->id,
                'uuid'   => $customer->uuid ?? null,
                'name'   => $customer->name ?? '---',
                'phone'  => $customer->phone ?? '---',
                'branch' => $customer->branch?->name ?? '---',
            ],

            'summary' => [
                'sent_count' => $sentCount,
                'received_count' => $receivedCount,
                'total_shipments' => $shipmentRows->count(),

                'total_shipments_amount_raw' => $totalShipmentsAmount,
                'total_shipments_paid_raw' => $totalShipmentsPaid,
                'total_shipments_remaining_raw' => $totalShipmentsRemaining,

                'total_shipments_amount' => number_format($totalShipmentsAmount, 0),
                'total_shipments_paid' => number_format($totalShipmentsPaid, 0),
                'total_shipments_remaining' => number_format($totalShipmentsRemaining, 0),

                'total_debit_raw' => $totalDebit,
                'total_credit_raw' => $totalCredit,
                'final_balance_raw' => $finalBalance,

                'total_debit' => number_format($totalDebit, 0),
                'total_credit' => number_format($totalCredit, 0),
                'final_balance' => number_format(abs($finalBalance), 0),

                'balance_status' => $finalBalance > 0 ? 'مدين' : ($finalBalance < 0 ? 'دائن' : 'مسدد'),
                'is_debtor' => $finalBalance > 0,
                'is_creditor' => $finalBalance < 0,
                'is_cleared' => $finalBalance == 0,
            ],

            'statement' => [
                'entries' => $entries->toArray(),
                'entries_count' => $entries->count(),
            ],

            'shipments' => $shipmentRows->toArray(),

            'creator_name' => $user?->name ?? 'مسؤول النظام',

            'print_date' => now()->timezone('Asia/Aden')->format('Y-m-d h:i A'),

            'design' => [
                'primary_color'   => $theme['primary'] ?? '#fb6514',
                'secondary_color' => $theme['secondary'] ?? '#333333',
                'bg_color'        => $theme['bg_light'] ?? '#fff4ee',
                'font_family'     => "'aealarabiya', 'dejavusans', sans-serif",
                'paper_size'      => 'a4',
            ],
        ];
    }

    public function getTemplatePath(): string
    {
        return 'receipts.templates.CustomerAccountStatement';
    }

    public function getFileName(array $data): string
    {
        $customerName = str_replace(' ', '_', $data['customer']['name'] ?? 'customer');
        return 'Customer_Account_Statement_' . $customerName . '.pdf';
    }

    private function paymentMethodLabel(?string $method): string
    {
        return match ($method) {
            'prepaid' => 'مدفوع مقدماً',
            'cod' => 'دفع عند الاستلام',
            'partial_payment' => 'دفع جزئي',
            'customer_credit' => 'آجل على العميل',
            'cash' => 'نقداً',
            'bank_transfer' => 'حوالة بنكية',
            null, '' => '---',
            default => $method,
        };
    }

    private function statusLabel(?string $status): string
    {
        return match ($status) {
            'pending' => 'قيد الانتظار',
            'in_transit' => 'قيد النقل',
            'delivered' => 'تم التسليم',
            'cancelled' => 'ملغي',
            'returned' => 'مرتجع',
            null, '' => '---',
            default => $status,
        };
    }
}
