<?php

namespace App\Services\Receipts\Types;

use App\Models\Customer;
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

        $branchId = $user->branch_id;
        $branchCode = $user->branch_code;

        // العميل
        $customer = Customer::with('branch')
            ->where('app_id', $user->app_id)
            ->where(function ($query) use ($referenceId) {
                $query->where('uuid', $referenceId);

                if (is_numeric($referenceId)) {
                    $query->orWhere('id', $referenceId);
                }
            })
            ->firstOrFail();
        $appName = $user->cached_app_name;
        $app = $user->app;

        $logoPath = $app?->logo
            ? public_path('storage/' . $app->logo)
            : public_path('assets/image/icon_without_bg.png');

        // بيانات الفرع الحالي
        $mainBranchData = null;
        if ($customer->branch) {
            $mainBranchData = [
                'title' => 'فرع / ' . $customer->branch->name . ($customer->branch->address ? ' - ' . $customer->branch->address : ''),
                'phones' => implode(' - ', array_filter(array_map('trim', preg_split('/[\s,\-]+/', $customer->branch->phone ?? '')))),
            ];
        }

        // بيانات المركز الرئيسي
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

        // تحديد طرق الدفع المطلوبة لكشف الحساب
        $targetPaymentMethods = ['customer_credit', 'partial_payment'];

        // الشحنات المرسلة (مفلترة من قاعدة البيانات مباشرة)
        $sentShipments = Shipment::with(['receiverBranch', 'payments'])
            ->where('sender_customer_id', $customer->id)
            ->whereHas('senderBranch', function ($query) use ($user) {
                $query->where('app_id', $user->app_id);
            })
            ->whereIn('payment_method', $targetPaymentMethods)
            ->get();

        $receivedShipments = Shipment::with(['senderBranch', 'payments'])
            ->where('receiver_customer_id', $customer->id)
            ->whereHas('receiverBranch', function ($query) use ($user) {
                $query->where('app_id', $user->app_id);
            })
            ->whereIn('payment_method', $targetPaymentMethods)
            ->get();
        $entries = collect();

        // معالجة الشحنات المرسلة
        foreach ($sentShipments as $shipment) {
            $paid = (float) $shipment->payments->sum('amount');
            $shipmentAmount = (float) ($shipment->total_amount ?? 0);

            $entries->push([
                'date' => $shipment->created_at,
                'reference' => $shipment->bond_number ?? '---',
                'description' => 'شحنة مرسلة إلى ' . ($shipment->receiverBranch?->name ?? '---'),
                'payment_method' => $shipment->payment_method,
                'debit' => $shipmentAmount,
                'credit' => 0,
                'notes' => $shipment->notes,
            ]);

            if ($paid > 0) {
                $entries->push([
                    'date' => $shipment->created_at,
                    'reference' => $shipment->bond_number ?? '---',
                    'description' => 'دفعة مسددة على شحنة مرسلة',
                    'payment_method' => $shipment->payment_method,
                    'debit' => 0,
                    'credit' => $paid,
                    'notes' => null,
                ]);
            }
        }

        // معالجة الشحنات المستقبلة
        foreach ($receivedShipments as $shipment) {
            $paid = (float) $shipment->payments->sum('amount');
            $shipmentAmount = (float) ($shipment->total_amount ?? 0);

            $entries->push([
                'date' => $shipment->created_at,
                'reference' => $shipment->bond_number ?? '---',
                'description' => 'شحنة مستقبلة من ' . ($shipment->senderBranch?->name ?? '---'),
                'payment_method' => $shipment->payment_method,
                'debit' => $shipmentAmount,
                'credit' => 0,
                'notes' => $shipment->notes,
            ]);

            if ($paid > 0) {
                $entries->push([
                    'date' => $shipment->created_at,
                    'reference' => $shipment->bond_number ?? '---',
                    'description' => 'دفعة مسددة على شحنة مستقبلة',
                    'payment_method' => $shipment->payment_method,
                    'debit' => 0,
                    'credit' => $paid,
                    'notes' => null,
                ]);
            }
        }

        // ترتيب الحركات حسب التاريخ
        $entries = $entries->sortBy('date')->values();

        // حساب الرصيد الجاري
        $runningBalance = 0;
        $entries = $entries->map(function ($entry) use (&$runningBalance) {
            $runningBalance += ((float) $entry['debit'] - (float) $entry['credit']);
            $entry['balance'] = $runningBalance;
            return $entry;
        });

        $totalDebit = (float) $entries->sum('debit');
        $totalCredit = (float) $entries->sum('credit');
        $finalBalance = $totalDebit - $totalCredit;
        $isDebtor = $finalBalance > 0;

        $theme = $app ? $app->theme : [
            'primary'   => '#fb6514',
            'secondary' => '#333333',
            'bg_light'  => '#fff4ee',
        ];

        return [
            'company' => [
                'name'         => $appName ?? 'اسم الشركة غير محدد',
                'logo'         => $logoPath,
                'main_branch'  => $mainBranchData,
                'headquarters' => $headquartersData,
            ],

            'title' => 'كشف حساب عميل',

            'customer' => [
                'id'     => $customer->id,
                'name'   => $customer->name ?? '---',
                'phone'  => $customer->phone ?? '---',
                'branch' => $customer->branch?->name ?? '---',
            ],

            'statement' => [
                'entries'        => $entries->toArray(),
                'entries_count'  => $entries->count(),
                'total_debit'    => number_format($totalDebit, 0),
                'total_credit'   => number_format($totalCredit, 0),
                'final_balance'  => number_format(abs($finalBalance), 0),
                'balance_status' => $isDebtor ? 'مدين' : 'دائن',
                'is_debtor'      => $isDebtor,
            ],

            'creator_name' => $user->name ?? 'مسؤول النظام',
            'print_date'   => Carbon::now()
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
        return 'receipts.templates.CoustomerAcoountDetection';
    }

    public function getFileName(array $data): string
    {
        $customerName = str_replace(' ', '_', $data['customer']['name'] ?? 'customer');
        return 'Customer_Account_Statement_' . $customerName . '.pdf';
    }
}
