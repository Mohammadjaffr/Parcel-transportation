<?php

namespace App\Services\Receipts\Types;

use App\Models\PassengerTrip;
use App\Interfaces\ReceiptStrategyInterface;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TripDetection implements ReceiptStrategyInterface
{
    public function sizepage(): string|array
    {
        return 'A4';
    }

public function fetchData(string $referenceId): array
{
    $filters = [];
    $user = auth()->user();

    // سند رحلة واحدة يكون عام إذا الرابط UUID
    $isSingleTrip = $referenceId !== 'all' && !str_contains($referenceId, ':');

    if ($isSingleTrip) {
        $trips = PassengerTrip::with([
            'driver',
            'passengers.broker',
            'passengers.branch',
            'branch.app',
        ])
            // مهم: للسند العام استخدم UUID فقط، لا تستخدم id
            ->where('uuid', $referenceId)
            ->get();

        if ($trips->isEmpty()) {
            abort(404, 'لم يتم العثور على سند الرحلة.');
        }

        $tripBranch = $trips->first()?->branch ?? null;
        $app = $tripBranch?->app ?? null;
        $userBranch = $tripBranch;
        $creatorName = 'النظام';
    } else {
        // كشف كل الرحلات والفلاتر يحتاج تسجيل دخول
        if (!$user) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه البيانات، يرجى تسجيل الدخول أولاً.');
        }

        $filters = $this->parseFilters($referenceId);

        $query = PassengerTrip::with([
            'driver',
            'passengers.broker',
            'passengers.branch',
            'branch.app',
        ])
            ->where('branch_id', $user->branch_id)
            ->latest();

        if (!empty($filters['from'])) {
            $query->whereDate('created_at', '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $query->whereDate('created_at', '<=', $filters['to']);
        }

        if (!empty($filters['driver_id']) && $filters['driver_id'] !== 'all') {
            $query->where('driver_id', $filters['driver_id']);
        }

        $trips = $query->get();

        $app = $user?->app ?? $user?->branch?->app ?? null;
        $userBranch = $user?->branch ?? null;
        $creatorName = $user?->name ?? 'مسؤول النظام';
    }

    // الشعار
    $imagePath = $app?->logo
        ? public_path('storage/' . $app->logo)
        : public_path('assets/image/icon_without_bg.png');

    $logoBase64 = null;

    if (file_exists($imagePath)) {
        $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
        $data = @file_get_contents($imagePath);

        if ($data) {
            $logoBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($data);
        }
    }

    // بيانات الفرع الحالي
    $mainBranchData = null;

    if ($userBranch) {
        $mainBranchData = [
            'title' => 'فرع / ' . $userBranch->name . ($userBranch->address ? ' - ' . $userBranch->address : ''),
            'phones' => implode(' - ', array_filter(array_map('trim', preg_split('/[\s,\-]+/', $userBranch->phone ?? '')))),
        ];
    }

    // بيانات الفرع الرئيسي وباقي أرقام الفروع
    $headquartersData = null;
    $otherPhonesList = [];

    if ($app) {
        if ($app->phone) {
            $hqPhoneArray = array_filter(array_map('trim', preg_split('/[\s,\-]+/', $app->phone)));

            if (!empty($hqPhoneArray)) {
                $headquartersData = [
                    'title' => 'الفرع الرئيسي' . ($app->address ? ' - ' . $app->address : ''),
                    'phones' => implode(' - ', $hqPhoneArray),
                ];
            }
        }

        if (method_exists($app, 'branches')) {
            $allBranches = $app->branches()->get();

            foreach ($allBranches as $b) {
                if ($userBranch && $b->id === $userBranch->id) {
                    continue;
                }

                $phonesArray = array_filter(array_map('trim', preg_split('/[\s,\-]+/', $b->phone ?? '')));
                $otherPhonesList = array_merge($otherPhonesList, $phonesArray);
            }
        }
    }

    $otherPhonesStr = !empty($otherPhonesList)
        ? implode(' - ', array_unique($otherPhonesList))
        : null;

    $statusLabels = [
        'pending'   => 'قيد الانتظار',
        'confirmed' => 'مؤكد',
        'completed' => 'مكتمل',
        'cancel'    => 'ملغي',
    ];

    $theme = $app?->theme ?? [
        'primary'   => '#ea580c',
        'secondary' => '#1e293b',
        'bg_light'  => '#fffaf5',
    ];

    $driversData = [];
    $totalPassengers = 0;
    $totalCount = 0;
    $totalOfficeCommission = 0;
    $totalOtherOfficeCommission = 0;

    foreach ($trips as $trip) {
        $driverName = $trip->driver?->name ?? 'سائق غير محدد';
        $driverPhone = $trip->driver?->phone ?? '';

        $msg = "السلام عليكم ورحمة الله وبركاته\n";
        $msg .= "الأخ الكابتن / *{$driverName}* المحترم،\n\n";
        $msg .= "إليك كشف الركاب لرحلتك رقم #{$trip->id}:\n\n";
        $msg .= "*تفاصيل الركاب:*\n";
        $msg .= "------------------------------------------\n";

        $passengersData = [];
        $i = 1;

        $tripTotalCount = 0;
        $tripTotalOffice = 0;
        $tripTotalOther = 0;

        foreach ($trip->passengers as $p) {
            $pNum = $p->passenger_number ?? '---';
            $pPickup = $p->pickup_location ?? '---';
            $pDest = $p->destination ?? '---';
            $pCnt = $p->count ?? 0;
            $pNote = $p->note ?: '---';

            $tripTotalCount += $pCnt;
            $tripTotalOffice += $p->office_commission ?? 0;
            $tripTotalOther += $p->other_office_commission ?? 0;
            $totalPassengers++;

            $passengersData[] = [
                'date'                    => $p->date ? $p->date->format('Y-m-d') : '---',
                'passenger_number'        => $pNum,
                'broker_name' => $user ? ($p->broker?->name ?? 'بدون وسيط') : null,
                'branch_name'             => $p->branch?->name ?? '---',
                'pickup_location'         => $pPickup,
                'destination'             => $pDest,
                'count'                   => $pCnt,
                'office_commission'       => number_format($p->office_commission ?? 0, 0),
                'other_office_commission' => number_format($p->other_office_commission ?? 0, 0),
                'status_key'              => $p->status ?? 'pending',
                'status_label'            => $statusLabels[$p->status ?? 'pending'] ?? 'غير محدد',
                'note'                    => $p->note,
            ];

            $msg .= "{$i}. الراكب: {$pNum}\n";
            $msg .= "📍 مكان الركوب: {$pPickup}\n";
            $msg .= "🏁 الوجهة: {$pDest}\n";
            $msg .= "👥 العدد: {$pCnt}\n";

            if ($p->note) {
                $msg .= "📝 ملاحظات: {$pNote}\n";
            }

            $msg .= "------------------------------------------\n";
            $i++;
        }

        $pdfLink = route('receipt.generate', [
            'type' => 'trip',
            'id'   => $trip->uuid,
        ]);

        $msg .= "📊 إجمالي عدد الركاب: *{$tripTotalCount}* راكب\n";
        $msg .= "📄 رابط كشف الـ PDF للرحلة:\n{$pdfLink}\n\n";
        $msg .= "رافقتكم السلامة. 🚚";

        $whatsappLink = '';

        if (!empty($driverPhone)) {
            $whatsappLink = $this->getWhatsAppLink($driverPhone, $msg);
        }

        $driversData[] = [
            'trip_id'                       => $trip->id,
            'trip_uuid'                     => $trip->uuid ?? null,
            'driver_id'                     => $trip->driver_id ?: 'unassigned',
            'driver_name'                   => $driverName . " (رحلة #{$trip->id})",
            'driver_phone'                  => $driverPhone ?: '---',
            'passengers'                    => $passengersData,
            'total_passengers_count'        => count($passengersData),
            'total_count'                   => $tripTotalCount,
            'total_office_commission'       => number_format($tripTotalOffice, 0),
            'total_other_office_commission' => number_format($tripTotalOther, 0),
            'whatsapp_link'                 => $whatsappLink,
            'pdf_link'                      => $pdfLink,
        ];

        $totalCount += $tripTotalCount;
        $totalOfficeCommission += $tripTotalOffice;
        $totalOtherOfficeCommission += $tripTotalOther;
    }

    $tripId = $trips->first()?->id ?? '';
    $reportTitle = $isSingleTrip ? "سند رحلة رقم #{$tripId}" : "كشف الرحلات";

    return [
        'company' => [
            'name'         => $app?->name ?? 'اسم الشركة غير محدد',
            'logo'         => $logoBase64,
            'main_branch'  => $mainBranchData,
            'headquarters' => $headquartersData,
            'other_phones' => $otherPhonesStr,
        ],

        'title'         => $reportTitle,
        'date'          => Carbon::now()->locale('ar')->translatedFormat('l Y-m-d H:i'),
        'date_from'     => $filters['from'] ?? null,
        'date_to'       => $filters['to'] ?? null,
        'status_filter' => null,

        'drivers'                       => $driversData,
        'total_passengers'              => $totalPassengers,
        'total_count'                   => $totalCount,
        'total_office_commission'       => number_format($totalOfficeCommission, 0),
        'total_other_office_commission' => number_format($totalOtherOfficeCommission, 0),

        'creator_name' => $creatorName,
        'print_date'   => now()->format('Y-m-d H:i'),
        'user_branch'  => $userBranch?->name ?? 'الفرع الرئيسي',

        'terms_and_conditions' => (is_array($app?->terms_and_conditions) && count($app->terms_and_conditions) > 0)
            ? $app->terms_and_conditions
            : [
                'يرجى التأكد من بيانات الركاب قبل اعتماد الرحلة.',
                'المكتب غير مسؤول عن أي بيانات غير صحيحة تم إدخالها من المستخدم.',
                'يجب التواصل مع السائق قبل موعد الرحلة للتأكيد.',
            ],

        'design' => [
            'primary_color'   => $theme['primary'] ?? '#ea580c',
            'secondary_color' => $theme['secondary'] ?? '#1e293b',
            'bg_color'        => $theme['bg_light'] ?? '#fffaf5',
            'font_family'     => "'aealarabiya', 'dejavusans', sans-serif",
            'paper_size'      => 'a4',
        ],
    ];
}

    public function getTemplatePath(): string
    {
        return 'receipts.templates.TripDetection';
    }

    public function getFileName(array $data): string
    {
        return 'TripManifest_' . now()->format('Y-m-d') . '.pdf';
    }

    /**
     * تحليل الفلاتر من referenceId
     * الصيغة: all أو from:2026-01-01|to:2026-12-31|driver_id:5
     */
    private function parseFilters(string $referenceId): array
    {
        $filters = ['from' => null, 'to' => null, 'driver_id' => null];

        if ($referenceId === 'all') {
            return $filters;
        }

        $parts = explode('|', $referenceId);
        foreach ($parts as $part) {
            $segments = explode(':', $part, 2);
            if (count($segments) === 2) {
                $key = trim($segments[0]);
                $value = trim($segments[1]);
                if (array_key_exists($key, $filters)) {
                    $filters[$key] = $value;
                }
            }
        }

        return $filters;
    }

    private function getWhatsAppLink(string $phone, string $message): string
    {
        $encodedMessage = urlencode($message);
        // Clean phone number
        $cleanPhone = preg_replace('/[^\d\+]/', '', $phone);
        $cleanPhone = ltrim($cleanPhone, '+');

        if (str_starts_with($cleanPhone, '00')) {
            $cleanPhone = substr($cleanPhone, 2);
        }
        if (str_starts_with($cleanPhone, '967967')) {
            $cleanPhone = substr($cleanPhone, 3);
        }
        if (str_starts_with($cleanPhone, '966966')) {
            $cleanPhone = substr($cleanPhone, 3);
        }
        if (preg_match('/^0(7[0-9]\d{7})$/', $cleanPhone, $matches)) {
            $cleanPhone = '967' . $matches[1];
        }
        // If Yemen local 9-digit
        if (strlen($cleanPhone) === 9 && (str_starts_with($cleanPhone, '77') || str_starts_with($cleanPhone, '73') || str_starts_with($cleanPhone, '71') || str_starts_with($cleanPhone, '70'))) {
            $cleanPhone = '967' . $cleanPhone;
        }

        return "https://wa.me/{$cleanPhone}?text={$encodedMessage}";
    }
}