<?php

namespace App\Services\Receipts\Types;

use App\Models\Customer;
use App\Models\CustomerTransaction;
use App\Models\Shipment;
use App\Interfaces\ReceiptStrategyInterface;
use Carbon\Carbon;

class CustomerAccountStatementReceipt implements ReceiptStrategyInterface
{
    public function sizepage(): string|array
    {
        return 'A4';
    }

    public function fetchData(string $referenceId): array
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $app = $user->app;
        $appId = $user->app_id;
        $branchId = $user->branch_id;

        /*
        |--------------------------------------------------------------------------
        | 1. بيانات العميل
        |--------------------------------------------------------------------------
        */
        $customer = Customer::with('branch')
            ->where('app_id', $appId)
            ->where(function ($query) use ($referenceId) {
                $query->where('uuid', $referenceId);

                if (is_numeric($referenceId)) {
                    $query->orWhere('id', $referenceId);
                }
            })
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | 2. بيانات الشركة / الشعار / الفروع
        |--------------------------------------------------------------------------
        */
        $appName = $user->cached_app_name ?? $app?->name ?? 'اسم الشركة غير محدد';

        $logoPath = $app?->logo
            ? public_path('storage/' . $app->logo)
            : public_path('assets/image/icon_without_bg.png');

        $mainBranchData = null;

        if ($customer->branch) {
            $mainBranchData = [
                'title' => 'فرع / ' . $customer->branch->name . ($customer->branch->address ? ' - ' . $customer->branch->address : ''),
                'phones' => implode(' - ', array_filter(array_map('trim', preg_split('/[\s,\-]+/', $customer->branch->phone ?? '')))),
            ];
        }

        $headquartersData = null;

        if ($app?->phone) {
            $hqPhoneArray = array_filter(array_map('trim', preg_split('/[\s,\-]+/', $app->phone)));

            if (!empty($hqPhoneArray)) {
                $headquartersData = [
                    'title'  => 'الفرع الرئيسي' . ($app->address ? ' - ' . $app->address : ''),
                    'phones' => implode(' - ', $hqPhoneArray),
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 3. جلب الشحنات المرتبطة بالعميل
        |--------------------------------------------------------------------------
        | نجيب كل الشحنات التي يكون العميل فيها مرسل أو مستلم داخل نفس التطبيق.
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
                $query->whereHas('senderBranch', function ($q) use ($appId) {
                    $q->where('app_id', $appId);
                })
                ->orWhereHas('receiverBranch', function ($q) use ($appId) {
                    $q->where('app_id', $appId);
                });
            })
            ->orderBy('created_at', 'asc')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 4. تجهيز تفاصيل الطرود
        |--------------------------------------------------------------------------
        */
        $shipmentRows = $shipments->map(function ($shipment) use ($customer) {
            $totalAmount = (float) ($shipment->total_amount ?? 0);
            $paidAmount = (float) $shipment->payments->sum('amount');
            $remainingAmount = max(0, $totalAmount - $paidAmount);

            $direction = $shipment->sender_customer_id == $customer->id ? 'sent' : 'received';
            $directionLabel = $direction === 'sent' ? 'مرسل' : 'مستلم';

            $otherPartyName = $direction === 'sent'
                ? ($shipment->receiverCustomer?->name ?? '---')
                : ($shipment->senderCustomer?->name ?? '---');

            $otherBranchName = $direction === 'sent'
                ? ($shipment->receiverBranch?->name ?? '---')
                : ($shipment->senderBranch?->name ?? '---');

            return [
                'id' => $shipment->id,
                'date' => optional($shipment->created_at)->format('Y-m-d'),
                'bond_number' => $shipment->bond_number ?? '---',
                'code' => $shipment->code ?? '---',
                'direction' => $direction,
                'direction_label' => $directionLabel,
                'other_party_name' => $otherPartyName,
                'other_branch_name' => $otherBranchName,
                'payment_method' => $this->paymentMethodLabel($shipment->payment_method),
                'status' => $this->statusLabel($shipment->status),
                'package_type' => $shipment->package_type ?? '---',
                'weight' => $shipment->weight ?? null,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'remaining_amount' => $remainingAmount,
                'notes' => $shipment->notes,
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | 5. جلب الحركات المالية من دفتر الأستاذ CustomerTransaction
        |--------------------------------------------------------------------------
        | هذا هو المصدر الأهم لكشف الحساب لأنه يسجل debit / credit.
        */
        $transactions = CustomerTransaction::with('shipment')
            ->where('customer_id', $customer->id)
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 6. بناء جدول كشف الحساب
        |--------------------------------------------------------------------------
        */
        $entries = collect();

        foreach ($transactions as $transaction) {
            $amount = (float) ($transaction->amount ?? 0);
            $type = $transaction->type;

            $shipment = $transaction->shipment;

            $entries->push([
                'date' => $transaction->created_at,
                'reference' => $shipment?->bond_number ?? $transaction->reference_number ?? '---',
                'movement_type' => $type === 'debit' ? 'قيد مديونية' : 'سداد / تحصيل',
                'description' => $transaction->description
                    ?? $transaction->notes
                    ?? ($type === 'debit' ? 'مبلغ مستحق على العميل' : 'مبلغ مسدد من العميل'),
                'payment_method' => $this->paymentMethodLabel($transaction->payment_method ?? null),
                'debit' => $type === 'debit' ? $amount : 0,
                'credit' => $type === 'credit' ? $amount : 0,
                'notes' => $transaction->notes ?? null,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 7. في حال لم تكن كل الشحنات مسجلة في CustomerTransaction
        |--------------------------------------------------------------------------
        | هذه حماية إضافية: لو عندك شحنات آجل/جزئي ولم ينشأ لها قيد في دفتر الأستاذ،
        | ستظهر في التقرير كحركات تقديرية حتى لا يطلع الكشف ناقص.
        */
        $transactionShipmentIds = $transactions
            ->pluck('shipment_id')
            ->filter()
            ->unique()
            ->values();

        $creditPaymentMethods = ['customer_credit', 'partial_payment'];

        $missingLedgerShipments = $shipments
            ->whereIn('payment_method', $creditPaymentMethods)
            ->whereNotIn('id', $transactionShipmentIds);

        foreach ($missingLedgerShipments as $shipment) {
            $totalAmount = (float) ($shipment->total_amount ?? 0);
            $paidAmount = (float) $shipment->payments->sum('amount');

            $entries->push([
                'date' => $shipment->created_at,
                'reference' => $shipment->bond_number ?? '---',
                'movement_type' => 'شحنة',
                'description' => 'شحنة غير مضافة في دفتر الأستاذ - ' . ($shipment->sender_customer_id == $customer->id ? 'مرسلة' : 'مستقبلة'),
                'payment_method' => $this->paymentMethodLabel($shipment->payment_method),
                'debit' => $totalAmount,
                'credit' => 0,
                'notes' => $shipment->notes,
            ]);

            if ($paidAmount > 0) {
                $entries->push([
                    'date' => $shipment->created_at,
                    'reference' => $shipment->bond_number ?? '---',
                    'movement_type' => 'دفعة',
                    'description' => 'دفعة مسجلة على الشحنة',
                    'payment_method' => '---',
                    'debit' => 0,
                    'credit' => $paidAmount,
                    'notes' => null,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 8. ترتيب الحركات وحساب الرصيد الجاري
        |--------------------------------------------------------------------------
        */
        $entries = $entries
            ->sortBy([
                ['date', 'asc'],
                ['reference', 'asc'],
            ])
            ->values();

        $runningBalance = 0;

        $entries = $entries->map(function ($entry) use (&$runningBalance) {
            $runningBalance += ((float) $entry['debit'] - (float) $entry['credit']);
            $entry['balance'] = $runningBalance;
            $entry['date_formatted'] = $entry['date']
                ? Carbon::parse($entry['date'])->format('Y-m-d')
                : '---';

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

        return [
            'company' => [
                'name'         => $appName,
                'logo'         => $logoPath,
                'main_branch'  => $mainBranchData,
                'headquarters' => $headquartersData,
            ],

            'title' => 'كشف حساب عميل شامل',

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

            'creator_name' => $user->name ?? 'مسؤول النظام',

            'print_date' => Carbon::now()
                ->locale('ar')
                ->translatedFormat('l Y-m-d H:i'),

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