<?php

namespace App\Services\Receipts\Types;

use App\Models\PassengerTrip;
use App\Interfaces\ReceiptStrategyInterface;
use App\Services\Receipts\Concerns\FormatsReceiptHeaderData;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TripDetection implements ReceiptStrategyInterface
{
    use FormatsReceiptHeaderData;

    public function sizepage(): string|array
    {
        return 'A4 landscape';
    }

    public function fetchData(string $referenceId): array
    {
        $filters = [];
        $user = auth()->user();

        // كشف رحلة واحدة يكون عام إذا الرابط UUID أو الرقم المعرف
        $isSingleTrip = $referenceId !== 'all' && !str_contains($referenceId, ':');

        if ($isSingleTrip) {
            $trips = PassengerTrip::with([
                'driver',
                'passengers.broker',
                'passengers.branch',
                'branch.app',
            ])
                ->where(function($q) use ($referenceId) {
                    if (is_numeric($referenceId)) {
                        $q->where('id', $referenceId);
                    } else {
                        $q->where('uuid', $referenceId);
                    }
                })
                ->get();

            if ($trips->isEmpty()) {
                abort(404, 'لم يتم العثور على سند الرحلة.');
            }

            $tripBranch = $trips->first()?->branch ?? null;
            $app = $tripBranch?->app ?? null;
            $userBranch = $tripBranch;
            $creatorName = 'النظام';
        } else {
            $filters = $this->parseFilters($referenceId);
            $branchId = $filters['branch_id'] ?? ($user ? $user->branch_id : null);

            if (!$branchId) {
                abort(403, 'غير مصرح لك بالوصول إلى هذه البيانات، يرجى تحديد الفرع أو تسجيل الدخول أولاً.');
            }

            $query = PassengerTrip::with([
                'driver',
                'passengers.broker',
                'passengers.branch',
                'branch.app',
            ])
                ->where('branch_id', $branchId)
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

            $firstTrip = $trips->first();
            $app = $firstTrip?->branch?->app ?? ($user?->app ?? $user?->branch?->app ?? null);
            $userBranch = $firstTrip?->branch ?? ($user?->branch ?? null);
            $creatorName = $user?->name ?? 'مسؤول النظام';
        }

        if (!$app && auth()->check()) {
            $app = auth()->user()->App ?? auth()->user()->app;
        }

        // الشعار والفروع ديناميكياً
        $logoBase64 = $this->getLogoBase64($app);
        $branchAddresses = $this->getBranchAddresses($app);

        // بيانات الفرع الحالي
        $mainBranchData = null;
        if ($userBranch) {
            $mainBranchData = [
                'title' => 'فرع / ' . $userBranch->name . ($userBranch->address ? ' - ' . $userBranch->address : ''),
                'phones' => $this->formatPhoneNumber($userBranch->phone),
            ];
        }

        $headquartersData = null;
        $otherPhonesList = [];

        if ($app) {
            if ($app->phone) {
                $headquartersData = [
                    'title' => 'الفرع الرئيسي' . ($app->address ? ' - ' . $app->address : ''),
                    'phones' => $this->formatPhoneNumber($app->phone),
                ];
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

        $otherPhonesStr = !empty($otherPhonesList) ? implode(' - ', array_unique($otherPhonesList)) : null;

        $theme = $app?->theme ?? [
            'primary'   => '#ea580c',
            'secondary' => '#1e293b',
            'bg_light'  => '#fffaf5',
        ];

        $statusLabels = [
            'pending'   => 'قيد الانتظار',
            'confirmed' => 'مؤكد',
            'completed' => 'مكتمل',
            'cancel'    => 'ملغي',
        ];

        $driversData = [];
        $totalPassengers = 0;
        $totalCount = 0;
        $totalOfficeCommission = 0;
        $totalOtherOfficeCommission = 0;

        foreach ($trips as $trip) {
            $driverName = $trip->driver->name ?? 'سائق غير محدد';
            $driverPhone = $trip->driver->phone ?? '';

            $msg = "السلام عليكم ورحمة الله وبركاته\n";
            $msg .= "الأخ الكابتن / *{$driverName}* المحترم،\n\n";
            $msg .= "إليك كشف الركاب لرحلتك رقم #{$trip->id}:\n\n";
            $msg .= "*تفاصيل الركاب:*\n";
            $msg .= "------------------------------------------\n";

            $passengersData = [];
            $tripTotalPassengers = 0;
            $tripTotalCount = 0;
            $tripTotalOffice = 0;
            $tripTotalOther = 0;

            foreach ($trip->passengers as $p) {
                $pNum = $p->passenger_number ?? '---';

                $displayPhone = $pNum;
                if ($pNum !== '---') {
                    if (str_starts_with($pNum, '967')) {
                        $displayPhone = substr($pNum, 3);
                    } elseif (strlen($pNum) > 0) {
                        $displayPhone = '+' . $pNum;
                    }
                }

                $rawStatus = strtolower(trim((string) $p->status));
                $statusAr = $statusLabels[$rawStatus] ?? ($p->status ?? '---');
                $pDate = $p->date ? (is_string($p->date) ? Carbon::parse($p->date)->format('Y-m-d') : $p->date->format('Y-m-d')) : ($p->created_at ? $p->created_at->format('Y-m-d') : '---');

                $passengersData[] = [
                    'id'                      => $p->id,
                    'date'                    => $pDate,
                    'passenger_name'          => $p->passenger_name ?? '---',
                    'passenger_phone'         => $displayPhone,
                    'passenger_number'        => $displayPhone,
                    'count'                   => $p->count ?? 1,
                    'pickup_point'            => $p->pickup_location ?? ($p->pickup_point ?? '---'),
                    'pickup_location'         => $p->pickup_location ?? ($p->pickup_point ?? '---'),
                    'dropoff_point'           => $p->destination ?? ($p->dropoff_point ?? '---'),
                    'destination'             => $p->destination ?? ($p->dropoff_point ?? '---'),
                    'vehicle_type'            => $p->vehicle_type ?? '---',
                    'broker_name'             => $p->broker?->name ?? '---',
                    'office_commission'       => $p->office_commission ?? 0,
                    'other_office_commission' => $p->other_office_commission ?? 0,
                    'status'                  => $statusAr,
                    'raw_status'              => $rawStatus,
                    'status_key'              => $rawStatus,
                    'status_label'            => $statusAr,
                    'notes'                   => $p->note ?? ($p->notes ?? '---'),
                    'note'                    => $p->note ?? ($p->notes ?? '---'),
                ];

                $tripTotalPassengers++;
                $tripTotalCount += ($p->count ?? 1);
                $tripTotalOffice += ($p->office_commission ?? 0);
                $tripTotalOther += ($p->other_office_commission ?? 0);

                $msg .= "• *الاسم:* " . ($p->passenger_name ?? '---') . "\n";
                $msg .= "  *الهاتف:* " . ($displayPhone ?? '---') . "\n";
                $msg .= "  *المقاعد:* " . ($p->count ?? 1) . "\n";
                $msg .= "  *مكان الركوب:* " . ($p->pickup_point ?? '---') . "\n";
                $msg .= "  *مكان النزول:* " . ($p->dropoff_point ?? '---') . "\n";
                $msg .= "  *الحالة:* " . $statusAr . "\n";
                $msg .= "------------------------------------------\n";
            }

            $msg .= "\n*الإجمالي:*\n";
            $msg .= "• إجمالي الحجوزات: {$tripTotalPassengers}\n";
            $msg .= "• إجمالي الركاب: {$tripTotalCount}\n\n";
            $msg .= "نتمنى لك رحلة آمنة وموفقة!\n";
            $msg .= ($app?->name ?? 'شركة النقل');

            $whatsappLink = !empty($driverPhone) ? $this->getWhatsAppLink($driverPhone, $msg) : null;

            $driversData[] = [
                'trip_id'                       => $trip->id,
                'driver_name'                   => $driverName,
                'driver_phone'                  => $this->formatPhoneNumber($driverPhone),
                'whatsapp_link'                 => $whatsappLink,
                'passengers'                    => $passengersData,
                'total_passengers'              => $tripTotalPassengers,
                'total_passengers_count'        => $tripTotalPassengers,
                'total_count'                   => $tripTotalCount,
                'total_office_commission'       => number_format($tripTotalOffice, 0),
                'total_other_office_commission' => number_format($tripTotalOther, 0),
            ];

            $totalPassengers += $tripTotalPassengers;
            $totalCount += $tripTotalCount;
            $totalOfficeCommission += $tripTotalOffice;
            $totalOtherOfficeCommission += $tripTotalOther;
        }

        $firstTrip = $trips->first();
        $tripId = $firstTrip?->id ?? '';
        $reportTitle = $isSingleTrip ? "كشف رحلة" : "كشف الرحلات";

        $dateInfo = $this->prepareDateInfo($firstTrip?->created_at ?? now());

        return [
            'company' => [
                'name'         => $app?->name ?? 'اسم الشركة غير محدد',
                'logo'         => $logoBase64,
                'main_branch'  => $mainBranchData,
                'headquarters' => $headquartersData,
                'other_phones' => $otherPhonesStr,
                'branch_addresses' => $branchAddresses,
            ],
            'branch_addresses' => $branchAddresses,

            'day_name'          => $dateInfo['day_name'],
            'formatted_date'    => $dateInfo['formatted_date'],
            'formatted_time'    => $dateInfo['formatted_time'],

            'title'             => $reportTitle,
            'package_number'    => $isSingleTrip ? $tripId : '---',
            'trip_number'       => $isSingleTrip ? $tripId : '---',
            'manifest_number'   => $isSingleTrip ? $tripId : '---',
            'driver_name'       => $isSingleTrip ? ($firstTrip?->driver?->name ?? '---') : '---',
            'driver_phone'      => $isSingleTrip ? $this->formatPhoneNumber($firstTrip?->driver?->phone) : '---',
            'driver_vehicle_type' => $isSingleTrip ? ($firstTrip?->driver?->vehicle_type ?? '---') : '---',
            'vehicle_type'      => $isSingleTrip ? ($firstTrip?->driver?->vehicle_type ?? '---') : '---',
            'sender_branch'     => $userBranch?->name ?? 'المركز الرئيسي',

            'date'              => $dateInfo['date_full'],
            'date_from'         => $filters['from'] ?? null,
            'date_to'           => $filters['to'] ?? null,
            'status_filter'     => null,

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

    private function parseFilters(string $referenceId): array
    {
        $filters = ['from' => null, 'to' => null, 'driver_id' => null, 'branch_id' => null];

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
        if (strlen($cleanPhone) === 9 && (str_starts_with($cleanPhone, '77') || str_starts_with($cleanPhone, '73') || str_starts_with($cleanPhone, '71') || str_starts_with($cleanPhone, '70'))) {
            $cleanPhone = '967' . $cleanPhone;
        }

        return "https://wa.me/{$cleanPhone}?text={$encodedMessage}";
    }
}
