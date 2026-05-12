<?php

namespace App\Services\Receipts\Types;

use App\Models\CustomerTransaction;
use App\Interfaces\ReceiptStrategyInterface;
use Carbon\Carbon;

class CustomerTransactionReceipt implements ReceiptStrategyInterface
{
    public function sizepage(): string|array
    {
        return 'A5'; // أو يمكنك تغييره لمقاس حراري لاحقاً إذا أردت
    }

    public function fetchData(string $referenceId): array
    {
        // 1. جلب الحركة المالية مع بيانات العميل والشركة
        // ملاحظة: نفترض أن referenceId هو الـ id الخاص بالحركة المالية
        $transaction = CustomerTransaction::with([
            'customer.branch.app', 
            'customer.app',
            'creator'
        ])->findOrFail($referenceId);

        $customer = $transaction->customer;

        // 2. جلب بيانات التطبيق (الشركة) من العميل
        $app = $customer->app ?? $customer->branch?->app;

        // 3. تجهيز الشعار
        $imagePath = $app?->logo
            ? public_path('storage/' . $app->logo)
            : public_path('assets/image/icon_without_bg.png');

        $logoBase64 = null;
        if (file_exists($imagePath)) {
            $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
            $data = file_get_contents($imagePath);
            $logoBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($data);
        }

        // 4. إعداد الفروع
        $branchData = null;
        if ($customer->branch) {
            $branchData = [
                'title' => 'فرع / ' . $customer->branch->name . ($customer->branch->address ? ' - ' . $customer->branch->address : ''),
                'phones' => implode(' - ', array_filter(array_map('trim', preg_split('/[\s,\-]+/', $customer->branch->phone ?? ''))))
            ];
        }

        // 5. نوع السند
        // credit = العميل دفع فلوس (سند قبض)
        // debit = سجلنا دين على العميل أو صرفنا له (سند قيد/صرف)
        $isCredit = $transaction->type === 'credit';
        $receiptTitle = $isCredit ? 'سند قبض (تحصيل)' : 'سند صرف / قيد مديونية';

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

        return [
            'company' => [
                'name'         => $app?->name ?? 'اسم الشركة غير محدد',
                'logo'         => $logoBase64,
                'main_branch'  => $branchData,
            ],

            'title'             => $receiptTitle,
            'transaction_id'    => $transaction->id,
            'reference_number'  => $transaction->reference_number ?? '---',
            'date'              => ($transaction->created_at ? $transaction->created_at->format('Y-m-d h:i A') : now()->format('Y-m-d h:i A')),

            'customer_name'     => $customer->name ?? 'عميل غير محدد',
            'customer_phone'    => $customer->phone ?? '---',
            'customer_branch'   => $customer->branch?->name ?? '---',

            'amount'            => number_format($transaction->amount ?? 0, 2),
            'type'              => $transaction->type,
            'is_credit'         => $isCredit,
            'description'       => $transaction->description ?? '---',
            'notes'             => $transaction->notes ?? 'لا توجد ملاحظات',
            
            'payment_method'    => $paymentMethods[$transaction->payment_method ?? 'cash'] ?? $transaction->payment_method ?? 'نقدي',

            'creator_name'      => $transaction->creator?->name ?? 'مسؤول النظام',
            'print_date'        => Carbon::now()->locale('ar')->translatedFormat('l Y-m-d H:i'),
            
            'design' => [
                'primary_color'   => $theme['primary'] ?? '#ea580c',
                'secondary_color' => $theme['secondary'] ?? '#1e293b',
                'bg_color'        => $theme['bg_light'] ?? '#fffaf5',
                'font_family'     => "'aealarabiya', 'dejavusans', sans-serif",
                'paper_size'      => 'a4',
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