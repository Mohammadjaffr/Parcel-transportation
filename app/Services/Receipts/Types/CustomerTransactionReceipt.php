<?php

namespace App\Services\Receipts\Types;

use App\Models\CustomerTransaction;
use App\Interfaces\ReceiptStrategyInterface;
use App\Services\Receipts\Concerns\FormatsReceiptHeaderData;
use Carbon\Carbon;

class CustomerTransactionReceipt implements ReceiptStrategyInterface
{
    use FormatsReceiptHeaderData;

    public function sizepage(): string|array
    {
        return 'A5 landscape';
    }

    public function fetchData(string $referenceId): array
    {
        // 1. جلب الحركة المالية مع بيانات العميل والشركة
        $transaction = CustomerTransaction::with([
            'customer.branch.app', 
            'customer.app',
            'creator.branch'
        ])->findOrFail($referenceId);

        $customer = $transaction->customer;

        // 2. جلب بيانات التطبيق (الشركة) من العميل أو المستخدم المسجل
        $app = $customer->app ?? $customer->branch?->app;
        if (!$app && auth()->check()) {
            $app = auth()->user()->App ?? auth()->user()->app;
        }

        // 3. تجهيز الشعار والفروع ديناميكياً
        $logoBase64 = $this->getLogoBase64($app);
        $branchAddresses = $this->getBranchAddresses($app);

        // 4. إعداد الفروع
        $branchData = null;
        if ($customer->branch) {
            $branchData = [
                'title' => 'فرع / ' . $customer->branch->name . ($customer->branch->address ? ' - ' . $customer->branch->address : ''),
                'phones' => $this->formatPhoneNumber($customer->branch->phone)
            ];
        }

        // 5. نوع السند
        $isCredit = $transaction->type === 'credit';
        $receiptTitle = $isCredit ? 'سند قبض' : 'سند صرف';

        $paymentMethods = [
            'prepaid'         => 'مدفوع مقدماً',
            'cod'             => 'الدفع عند الاستلام',
            'partial_payment' => 'دفع جزئي',
            'customer_credit' => 'آجل (ذمة)',
            'cash'            => 'نقداً كاش',
            'bank_transfer'   => 'حوالة بنكية'
        ];

        // 6. الثيم والألوان
        $theme = $app?->theme ?? [
            'primary'   => '#ea580c',
            'secondary' => '#1e293b',
            'bg_light'  => '#fffaf5',
        ];

        // 7. تفكيك التاريخ واليوم بدقة
        $dateInfo = $this->prepareDateInfo($transaction->created_at);

        $senderBranchName = $customer->branch?->name 
            ?? $transaction->creator?->branch?->name 
            ?? 'المركز الرئيسي';

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
            'bond_number'       => $transaction->receipt_number ?? $transaction->id,
            'reference_number'  => $transaction->id ?? '---',
            'date'              => $dateInfo['date_full'],

            'sender_branch'     => $senderBranchName,
            'user_branch'       => $senderBranchName,

            'customer_name'     => $customer->name ?? 'عميل غير محدد',
            'customer_phone'    => $this->formatPhoneNumber($customer->phone),
            'customer_branch'   => $customer->branch?->name ?? '---',

            'amount'            => number_format($transaction->amount ?? 0, 2),
            'type'              => $transaction->type,
            'is_credit'         => $isCredit,
            'description'       => $transaction->description ?? '---',
            'notes'             => $transaction->notes ?? 'لا توجد ملاحظات',
            
            'payment_method'    => $paymentMethods[$transaction->payment_method ?? 'cash'] ?? $transaction->payment_method ?? 'نقدي',

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
        return 'receipts.templates.CustomerTransactionReceipt';
    }

    public function getFileName(array $data): string
    {
        return 'Transaction_Receipt_' . $data['transaction_id'] . '.pdf';
    }
}
