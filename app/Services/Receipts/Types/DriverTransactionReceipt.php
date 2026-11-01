<?php

namespace App\Services\Receipts\Types;

use App\Models\DriverTransaction;
use App\Interfaces\ReceiptStrategyInterface;
use App\Services\Receipts\Concerns\FormatsReceiptHeaderData;

class DriverTransactionReceipt implements ReceiptStrategyInterface
{
    use FormatsReceiptHeaderData;

    public function sizepage(): string|array
    {
        return 'A5 landscape';
    }

    public function fetchData(string $referenceId): array
    {
        // جلب الحركة المالية مع بيانات السائق والشركة
        $transaction = DriverTransaction::with([
            'driver.branch.app',
            'driver.app',
            'creator.branch',
            'cashTransaction',
        ])
        ->where(function ($query) use ($referenceId) {
            $query->where('uuid', $referenceId);
            if (is_numeric($referenceId)) {
                $query->orWhere('id', $referenceId);
            }
        })
        ->firstOrFail();

        $driver = $transaction->driver;

        // جلب بيانات التطبيق (الشركة)
        $app = $driver->app ?? $driver->branch?->app;
        if (!$app && auth()->check()) {
            $app = auth()->user()->App ?? auth()->user()->app;
        }

        // تجهيز الشعار والفروع ديناميكياً
        $logoBase64 = $this->getLogoBase64($app);
        $branchAddresses = $this->getBranchAddresses($app);

        // إعداد بيانات الفرع
        $branchData = null;
        if ($driver->branch) {
            $branchData = [
                'title' => 'فرع / ' . $driver->branch->name . ($driver->branch->address ? ' - ' . $driver->branch->address : ''),
                'phones' => $this->formatPhoneNumber($driver->branch->phone)
            ];
        }

        // نوع السند
        $isCredit = $transaction->type === 'credit';
        $receiptTitle = $isCredit ? 'سند قبض من سائق' : 'سند قيد على سائق';

        // الثيم والألوان
        $theme = $app?->theme ?? [
            'primary'   => '#ea580c',
            'secondary' => '#1e293b',
            'bg_light'  => '#fffaf5',
        ];

        // تفكيك التاريخ واليوم بدقة
        $dateInfo = $this->prepareDateInfo($transaction->created_at);

        $senderBranchName = $driver->branch?->name
            ?? $transaction->creator?->branch?->name
            ?? 'المركز الرئيسي';

        // حساب الرصيد السابق والحالي
        $previousBalance = $driver->transactions()
            ->where('created_at', '<', $transaction->created_at)
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END), 0) - COALESCE(SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END), 0) as balance")
            ->value('balance');

        if ($transaction->type === 'debit') {
            $currentBalance = $previousBalance - $transaction->amount;
        } else {
            $currentBalance = $previousBalance + $transaction->amount;
        }

        return [
            'company' => [
                'name'         => $app?->name ?? 'اسم الشركة غير محدد',
                'logo'         => $logoBase64,
                'main_branch'  => $branchData,
                'branch_addresses' => $branchAddresses,
            ],
            'branch_addresses'  => $branchAddresses,

            'day_name'          => $dateInfo['day_name'],
            'formatted_date'    => $dateInfo['formatted_date'],
            'formatted_time'    => $dateInfo['formatted_time'],

            'title'             => $receiptTitle,
            'transaction_id'    => $transaction->id,
            'bond_number'       => $transaction->id,
            'reference_number'  => $transaction->id ?? '---',
            'date'              => $dateInfo['date_full'],

            'sender_branch'     => $senderBranchName,
            'user_branch'       => $senderBranchName,

            'driver_name'       => $driver->name ?? 'سائق غير محدد',
            'driver_phone'      => $this->formatPhoneNumber($driver->phone),
            'driver_branch'     => $driver->branch?->name ?? '---',

            'amount'            => number_format($transaction->amount ?? 0, 2),
            'type'              => $transaction->type,
            'is_credit'         => $isCredit,
            'description'       => $transaction->description ?? '---',
            'notes'             => '---',

            'cash_receipt_number' => $transaction->cashTransaction?->receipt_number ?? null,
            'cash_receipt_formatted' => $transaction->cashTransaction?->formatted_receipt_number ?? null,

            'previous_balance'  => $previousBalance,
            'current_balance'   => $currentBalance,

            'payment_method'    => $transaction->cashTransaction?->payment_method === 'bank_transfer' ? 'حوالة بنكية' : 'نقداً كاش',

            'creator_name'      => $transaction->creator?->name ?? 'مسؤول النظام',
            'print_date'        => now()->timezone('Asia/Aden')->format('Y-m-d h:i A'),

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
        return 'receipts.templates.DriverTransactionReceipt';
    }

    public function getFileName(array $data): string
    {
        return 'Driver_Transaction_Receipt_' . $data['transaction_id'] . '.pdf';
    }
}

