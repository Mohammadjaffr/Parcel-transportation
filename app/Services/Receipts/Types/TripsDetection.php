<?php

namespace App\Services\Receipts\Types;

use App\Models\PassengerTrip;
use App\Interfaces\ReceiptStrategyInterface;
use App\Services\Receipts\Concerns\FormatsReceiptHeaderData;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TripsDetection implements ReceiptStrategyInterface
{
    use FormatsReceiptHeaderData;

    public function sizepage(): string|array
    {
        return 'A4 landscape';
    }

    public function fetchData(string $referenceId): array
    {
        $user = auth()->user();
        $filters = [];
        if (is_numeric($referenceId) || \Illuminate\Support\Str::isUuid($referenceId)) {
            $query = PassengerTrip::with(['driver', 'passengers.broker', 'passengers.branch']);
            if (is_numeric($referenceId)) {
                $query->where('id', $referenceId);
            } else {
                $query->where('uuid', $referenceId);
            }
            $trips = $query->get();
        } else {
            $filters = $this->parseFilters($referenceId);
            $branchId = $filters['branch_id'] ?? ($user ? $user->branch_id : null);

            if (!$branchId) {
                abort(400, 'معرف الفرع مطلوب لعرض هذه البيانات.');
            }

            $query = PassengerTrip::with(['driver', 'passengers.broker', 'passengers.branch'])
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
        }

        $app = null;
        $currentBranch = null;
        if ($trips->isNotEmpty()) {
            $app = $trips->first()->branch?->app ?? null;
            $currentBranch = $trips->first()->branch ?? null;
        } elseif ($user) {
            $app = $user->app ?? null;
            $currentBranch = $user->branch ?? null;
        }

        $logoBase64 = $this->getLogoBase64($app);
        $branchAddresses = $this->getBranchAddresses($app);

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
        $totalOfficeCommissionAll = 0;

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

                $passengersData[] = [
                    'id'                      => $p->id,
                    'passenger_name'          => $p->passenger_name ?? '---',
                    'passenger_phone'         => $displayPhone,
                    'count'                   => $p->count ?? 1,
                    'pickup_point'            => $p->pickup_point ?? '---',
                    'dropoff_point'           => $p->dropoff_point ?? '---',
                    'broker_name'             => $p->broker->name ?? '---',
                    'office_commission'       => $p->office_commission ?? 0,
                    'other_office_commission' => $p->other_office_commission ?? 0,
                    'status'                  => $statusAr,
                    'raw_status'              => $rawStatus,
                    'notes'                   => $p->notes ?? '---',
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
                'total_count'                   => $tripTotalCount,
                'total_office_commission'       => number_format($tripTotalOffice, 0),
                'total_other_office_commission' => number_format($tripTotalOther, 0),
            ];

            $totalPassengers += $tripTotalPassengers;
            $totalCount += $tripTotalCount;
            $totalOfficeCommission += $tripTotalOffice;
            $totalOtherOfficeCommission += $tripTotalOther;
        }

        $mainBranchData = null;
        if ($currentBranch) {
            $mainBranchData = [
                'title' => 'فرع / ' . $currentBranch->name . ($currentBranch->address ? ' - ' . $currentBranch->address : ''),
                'phones' => $this->formatPhoneNumber($currentBranch->phone)
            ];
        }

        $headquartersData = null;
        $otherPhonesList = [];

        if ($app) {
            if ($app->phone) {
                $headquartersData = [
                    'title' => 'الفرع الرئيسي' . ($app->address ? ' - ' . $app->address : ''),
                    'phones' => $this->formatPhoneNumber($app->phone)
                ];
            }

            $allBranches = $app->branches()->get();
            foreach ($allBranches as $b) {
                if ($currentBranch && $b->id === $currentBranch->id) continue;
                $phonesArray = array_filter(array_map('trim', preg_split('/[\s,\-]+/', $b->phone ?? '')));
                $otherPhonesList = array_merge($otherPhonesList, $phonesArray);
            }
        }
        $otherPhonesStr = !empty($otherPhonesList) ? implode(' - ', array_unique($otherPhonesList)) : null;

        $dateInfo = $this->prepareDateInfo(now());
        $firstTrip = $trips->first();

        return [
            'company' => [
                'name'         => $app?->name ?? 'اسم الشركة غير محدد',
                'logo'         => $logoBase64,
                'main_branch'  => $mainBranchData,
                'headquarters' => $headquartersData,
                'other_phones' => $otherPhonesStr,
                'branch_addresses' => $branchAddresses,
            ],
            'branch_addresses'              => $branchAddresses,

            'day_name'                      => $dateInfo['day_name'],
            'formatted_date'                => $dateInfo['formatted_date'],
            'formatted_time'                => $dateInfo['formatted_time'],

            'title'                         => "كشف الرحلات",
            'package_number'                => $firstTrip?->id ?? '---',
            'trip_number'                   => $firstTrip?->id ?? '---',
            'manifest_number'               => $firstTrip?->id ?? '---',
            'sender_branch'                 => $currentBranch?->name ?? 'المركز الرئيسي',

            'date'                          => $dateInfo['date_full'],
            'date_from'                     => $filters['from'] ?? null,
            'date_to'                       => $filters['to'] ?? null,
            'status_filter'                 => null,
            'drivers'                       => $driversData,
            'total_passengers'              => $totalPassengers,
            'total_count'                   => $totalCount,
            'total_office_commission'       => $totalOfficeCommission,
            'total_other_office_commission' => $totalOtherOfficeCommission,
            'creator_name'                  => $user->name ?? 'مسؤول النظام',
            'print_date'                    => now()->timezone('Asia/Aden')->format('Y-m-d h:i A'),
        ];
    }

    public function getTemplatePath(): string
    {
        return 'receipts.templates.TripsDetection';
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
