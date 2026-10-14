<?php

namespace App\Services\Receipts\Types;

use App\Models\Shipment;
use App\Interfaces\ReceiptStrategyInterface;
use Carbon\Carbon;

class SenderShipmentReceipt implements ReceiptStrategyInterface
{
    public function sizepage(): string|array
    {
        return 'A5 landscape'; // Default size
    }
    public function fetchData(string $referenceId): array
    {
        // 1. جلب الشحنة مع العلاقات مع تعطيل Global Scopes للعملاء لضمان ظهورهم للزائر غير المسجل
        $shipment = Shipment::with([
            'senderCustomer' => fn($q) => $q->withoutGlobalScopes(),
            'receiverCustomer' => fn($q) => $q->withoutGlobalScopes(),
            'senderBranch.app',
            'receiverBranch.app',
            'receiverOfficeBranch.office',
            'creator.app',
            'creator.branch'
        ])->where('uuid', $referenceId)->firstOrFail();

        // 2. جلب بيانات الشركة (التطبيق) - يدعم الزائر غير المسجل
        $app = $shipment->senderBranch?->app 
            ?? $shipment->creator?->app 
            ?? $shipment->receiverBranch?->app;

        if (!$app && auth()->check()) {
            $app = auth()->user()->App ?? auth()->user()->app;
        }

        // 3. تجهيز مسار الشعار
        $imagePath = $app?->logo
            ? public_path('storage/' . $app->logo)
            : public_path('assets/image/icon_without_bg.png');

        $logoBase64 = null;
        if (file_exists($imagePath)) {
            $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
            $data = file_get_contents($imagePath);
            $logoBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($data);
        }

        // 4. إعداد فرع السند وأرقام الهواتف
        $senderBranch = $shipment->senderBranch;
        $mainBranchData = null;
        if ($senderBranch) {
            $mainBranchData = [
                'title'  => 'فرع / ' . $senderBranch->name . ($senderBranch->address ? ' - ' . $senderBranch->address : ''),
                'phones' => implode(' - ', array_filter(array_map('trim', preg_split('/[\s,\/]+/', $senderBranch->phone ?? ''))))
            ];
        }

        $otherPhonesList = [];
        $companyPhone = !empty($app?->phone) ? trim($app->phone) : null;
        $headquartersData = null;

        if ($app) {
            if ($companyPhone) {
                $headquartersData = [
                    'title'  => 'الإدارة العامة',
                    'phones' => $companyPhone
                ];
            }

            // جلب الفروع الأخرى التابعة للتطبيق
            $allBranches = $app->branches()->get();
            foreach ($allBranches as $b) {
                if ($senderBranch && $b->id === $senderBranch->id) {
                    continue;
                }
                $phonesArray = array_filter(array_map('trim', preg_split('/[\s,\/]+/', $b->phone ?? '')));
                $otherPhonesList = array_merge($otherPhonesList, $phonesArray);
            }
        }

        $otherPhonesStr = !empty($otherPhonesList) ? implode(' - ', array_unique($otherPhonesList)) : null;

        // 5. القواميس والوجهات
        $paymentMethods = [
            'prepaid'         => 'مدفوع مقدماً',
            'cod'             => 'الدفع عند الاستلام',
            'partial_payment' => 'دفع جزئي',
            'customer_credit' => 'آجل (ذمة)'
        ];

        $receiverDestination = $shipment->receiverBranch?->name
            ?? $shipment->receiverOfficeBranch?->name
            ?? 'الوجهة غير محددة';

        $honeyDetails = ($shipment->no_gallons_honey || $shipment->no_honey_jars)
            ? "جوالين: " . ($shipment->no_gallons_honey ?? 0) . " | قروف: " . ($shipment->no_honey_jars ?? 0)
            : null;

        // 6. الثيم والألوان
        $theme = $app?->theme ?? [
            'primary'   => '#059669',
            'secondary' => '#1e293b',
            'bg_light'  => '#ecfdf5',
        ];

        // 7. الحسابات المالية الدقيقة بناءً على منطق الموديل وطريقة الدفع
        $totalAmount = (float) ($shipment->total_amount ?? 0);
        $remainingAmount = (float) ($shipment->amount_to_collect_from_receiver ?? 0);

        $paidAmount = match ($shipment->payment_method) {
            'prepaid'         => $totalAmount,
            'partial_payment' => (float) ($shipment->partial_amount ?? 0),
            'cod'             => 0,
            default           => 0,
        };

        // 8. إرجاع البيانات المنظمة
        return [
            'company' => [
                'name'         => $app?->name ?? 'اسم الشركة غير محدد',
                'phone'        => $companyPhone,
                'logo'         => $logoBase64,
                'main_branch'  => $mainBranchData,
                'headquarters' => $headquartersData,
                'other_phones' => $otherPhonesStr,
            ],

            'title'             => 'سند ارسال طرد',
            'bond_number'       => $shipment->bond_number ?? 'غير متوفر',
            'tracking_code'     => $shipment->bond_number ?? 'غير متوفر',

            'date'              => $shipment->created_at
                ? $shipment->created_at->timezone('Asia/Aden')->locale('ar')->translatedFormat('l Y-m-d H:i')
                : now()->timezone('Asia/Aden')->locale('ar')->translatedFormat('l Y-m-d H:i'),

            'sender_name'       => $shipment->senderCustomer?->name ?? 'عميل نقدي (غير مسجل)',
            'sender_phone'      => $this->formatPhoneNumber($shipment->senderCustomer?->phone),
            'sender_branch'     => $shipment->senderBranch?->name ?? 'الفرع الرئيسي',
            'sender_branch_phone' => $this->formatPhoneNumber($shipment->senderBranch?->phone),
            'sender_office'     => $app?->name ?? 'الفرع الرئيسي',

            'receiver_name'     => $shipment->receiverCustomer?->name ?? 'مستلم غير محدد',
            'receiver_phone'    => $this->formatPhoneNumber($shipment->receiverCustomer?->phone),
            'receiver_branch'   => $receiverDestination,
            'receiver_office'   => $shipment->receiverBranch?->app?->name 
                                ?? $shipment->receiverOfficeBranch?->office?->name 
                                ?? $app?->name 
                                ?? 'الفرع الرئيسي',

            'package_type'      => $shipment->package_type ?? ($honeyDetails ? null : 'طرد عادي'),
            'parcels_quantity'  => $shipment->parcels_quantity,
            'weight'            => $shipment->weight ? $shipment->weight . ' كجم' : null,
            'honey_details'     => $honeyDetails,
            'notes'             => $shipment->notes ?? 'لا توجد ملاحظات إضافية',

            'is_price_agreed'   => (bool) ($shipment->is_price_agreed ?? true),
            'payment_key'       => $shipment->payment_method ?? 'prepaid',
            'payment_method'    => !($shipment->is_price_agreed ?? true)
                ? 'سيتم الاتفاق عليه لاحقاً'
                : ($paymentMethods[$shipment->payment_method ?? 'prepaid'] ?? 'غير محدد'),
            
            // المبالغ المالية
            'total_amount'      => number_format($totalAmount, 0),
            'partial_amount'    => number_format($paidAmount, 0),       // المبلغ المسدد
            'remaining_amount'  => number_format($remainingAmount, 0),  // المبلغ المتبقي

            'creator_name'      => $shipment->creator?->name ?? 'مسؤول النظام',
            'print_date'        => now()->format('Y-m-d H:i'),
            'user_branch'       => $shipment->creator?->branch?->name ?? $shipment->senderBranch?->name ?? 'الفرع الرئيسي',
            'terms_and_conditions' => (is_array($app?->terms_and_conditions) && count($app->terms_and_conditions) > 0)
                ? $app->terms_and_conditions
                : ['نحن غير مسؤولين عن الإجراءات الأمنية الخارجة عن إرادتنا.', 'التأكد من بيانات السند قبل المغادرة.'],
            'design' => [
                'primary_color'   => $theme['primary'] ?? '#059669',
                'secondary_color' => $theme['secondary'] ?? '#1e293b',
                'bg_color'        => $theme['bg_light'] ?? '#ecfdf5',
                'font_family'     => "'aealarabiya', 'dejavusans', sans-serif",
                'paper_size'      => 'A5 landscape',
            ]
        ];
    }
    public function getTemplatePath(): string
    {
        return 'receipts.templates.SenderShipmentReceipt';
    }

    public function getFileName(array $data): string
    {
        return 'Sender_Receipt_' . $data['bond_number'] . '.pdf';
    }
       private function formatPhoneNumber(?string $phone): string
    {
        if (empty($phone) || trim($phone) === '' || trim($phone) === '---') {
            return '---';
        }

        $parts = preg_split('/[\s,\/]+/', trim($phone));
        $formattedParts = [];

        $countries = config('countries', []);
        $dialCodes = [];
        foreach ($countries as $country) {
            $rawCode = ltrim($country['dial_code'] ?? '', '+');
            if ($rawCode) {
                $dialCodes[] = $rawCode;
            }
        }
        $dialCodes = array_unique(array_merge($dialCodes, ['967', '966', '968', '971']));
        usort($dialCodes, fn($a, $b) => strlen($b) <=> strlen($a));

        foreach ($parts as $part) {
            $cleaned = trim($part);
            if ($cleaned === '') continue;

            foreach ($dialCodes as $rawCode) {
                if (preg_match('/^(\+|00)?' . preg_quote($rawCode, '/') . '/', $cleaned)) {
                    $cleaned = preg_replace('/^(\+|00)?' . preg_quote($rawCode, '/') . '/', '', $cleaned);
                    break;
                }
            }
            $formattedParts[] = $cleaned;
        }

        return !empty($formattedParts) ? implode(' - ', $formattedParts) : '---';
    }
}
