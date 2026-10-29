<?php

namespace App\Services\Receipts\Types;

use App\Models\Passengers;
use App\Interfaces\ReceiptStrategyInterface;
use App\Services\Receipts\Concerns\FormatsReceiptHeaderData;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PassangersDetection implements ReceiptStrategyInterface
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
            $branchId = $user ? $user->branch_id : null;
            
            $isBranchId = false;
            if (is_numeric($referenceId)) {
                if ($branchId && (int)$referenceId === (int)$branchId) {
                    $isBranchId = true;
                } elseif (\App\Models\Branch::where('id', $referenceId)->exists() && !Passengers::where('id', $referenceId)->exists()) {
                    $isBranchId = true;
                }
            }

            if ($isBranchId) {
                $query = Passengers::with(['driver', 'broker', 'branch'])->where('branch_id', $referenceId)->latest('date');
            } else {
                $query = Passengers::with(['driver', 'broker', 'branch']);
                if (is_numeric($referenceId)) {
                    $query->where('id', $referenceId);
                } else {
                    $query->where('uuid', $referenceId);
                }
            }
        } else {
            $filters = $this->parseFilters($referenceId);
            $branchId = $filters['branch_id'] ?? ($user ? $user->branch_id : null);
            
            if (!$branchId) {
                abort(400, 'معرف الفرع مطلوب لعرض هذه البيانات.');
            }

            $query = Passengers::with(['driver', 'broker', 'branch'])->where('branch_id', $branchId)->latest('date');

            if (!empty($filters['from'])) {
                $query->whereDate('date', '>=', $filters['from']);
            }
            if (!empty($filters['to'])) {
                $query->whereDate('date', '<=', $filters['to']);
            }
            if (!empty($filters['status']) && $filters['status'] !== 'all') {
                $query->where('status', $filters['status']);
            }
            if (!empty($filters['driver_id']) && $filters['driver_id'] !== 'all') {
                $query->where('driver_id', $filters['driver_id']);
            }
        }

        $passengers = $query->get();

        $app = null;
        $currentBranch = null;
        if ($passengers->isNotEmpty()) {
            $firstP = $passengers->first();
            $app = $firstP->branch?->app ?? null;
            $currentBranch = $firstP->branch ?? null;
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
        $firstDriverName = null;
        $firstDriverPhone = null;

        foreach ($passengers->groupBy('driver_id') as $driverId => $groupPassengers) {
            $firstP = $groupPassengers->first();
            $driverName = $firstP->driver->name ?? 'سائق غير محدد';
            $driverPhone = $firstP->driver->phone ?? '';

            if (!$firstDriverName) {
                $firstDriverName = $driverName;
                $firstDriverPhone = $driverPhone;
            }

            $msg = "السلام عليكم ورحمة الله وبركاته\n";
            $msg .= "الأخ الكابتن / *{$driverName}* المحترم،\n\n";
            $msg .= "إليك كشف الركاب المسجلين لديك:\n\n";
            $msg .= "*تفاصيل الركاب:*\n";
            $msg .= "------------------------------------------\n";

            $passengersData = [];
            foreach ($groupPassengers as $p) {
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
                    'count'                   => $p->count,
                    'pickup_point'            => $p->pickup_location ?? ($p->pickup_point ?? '---'),
                    'pickup_location'         => $p->pickup_location ?? ($p->pickup_point ?? '---'),
                    'dropoff_point'           => $p->destination ?? ($p->dropoff_point ?? '---'),
                    'destination'             => $p->destination ?? ($p->dropoff_point ?? '---'),
                    'vehicle_type'            => $p->vehicle_type ?? '---',
                    'broker_name'             => $p->broker?->name ?? '---',
                    'office_commission'       => $p->office_commission,
                    'other_office_commission' => $p->other_office_commission,
                    'status'                  => $statusAr,
                    'raw_status'              => $rawStatus,
                    'status_key'              => $rawStatus,
                    'status_label'            => $statusAr,
                    'notes'                   => $p->note ?? ($p->notes ?? '---'),
                    'note'                    => $p->note ?? ($p->notes ?? '---'),
                ];

                $msg .= "• *الاسم:* " . ($p->passenger_name ?? '---') . "\n";
                $msg .= "  *الهاتف:* " . ($displayPhone ?? '---') . "\n";
                $msg .= "  *المقاعد:* " . $p->count . "\n";
                $msg .= "  *مكان الركوب:* " . ($p->pickup_point ?? '---') . "\n";
                $msg .= "  *مكان النزول:* " . ($p->dropoff_point ?? '---') . "\n";
                $msg .= "  *الحالة:* " . $statusAr . "\n";
                $msg .= "------------------------------------------\n";
            }

            $whatsappLink = !empty($driverPhone) ? $this->getWhatsAppLink($driverPhone, $msg) : null;

            $driversData[] = [
                'driver_name'            => $driverName,
                'driver_phone'           => $this->formatPhoneNumber($driverPhone),
                'whatsapp_link'          => $whatsappLink,
                'passengers'             => $passengersData,
                'total_passengers'       => count($passengersData),
                'total_passengers_count' => count($passengersData),
                'total_count'            => $groupPassengers->sum('count'),
                'total_office'           => $groupPassengers->sum('office_commission'),
                'total_other'            => $groupPassengers->sum('other_office_commission'),
            ];
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
        $singlePass = (is_numeric($referenceId) || \Illuminate\Support\Str::isUuid($referenceId)) ? $passengers->first() : null;

        return [
            'company' => [
                'name' => $app?->name ?? 'اسم الشركة غير محدد',
                'logo' => $logoBase64,
                'main_branch'  => $mainBranchData,
                'headquarters' => $headquartersData,
                'other_phones' => $otherPhonesStr,
                'branch_addresses' => $branchAddresses,
            ],
            'branch_addresses'              => $branchAddresses,

            'day_name'                      => $dateInfo['day_name'],
            'formatted_date'                => $dateInfo['formatted_date'],
            'formatted_time'                => $dateInfo['formatted_time'],

            'title'                         => "كشف الركاب",
            'package_number'                => $singlePass?->id ?? '---',
            'manifest_number'               => $singlePass?->id ?? '---',
            'driver_name'                   => $firstDriverName ?? 'غير محدد',
            'driver_phone'                  => $this->formatPhoneNumber($firstDriverPhone),
            'sender_branch'                 => $currentBranch?->name ?? 'المركز الرئيسي',

            'date'                          => $dateInfo['date_full'],
            'date_from'                     => $filters['from'] ?? null,
            'date_to'                       => $filters['to'] ?? null,
            'status_filter'                 => $filters['status'] ?? null,
            'drivers'                       => $driversData,
            'total_passengers'              => $passengers->count(),
            'total_count'                   => $passengers->sum('count'),
            'total_office_commission'       => $passengers->sum('office_commission'),
            'total_other_office_commission' => $passengers->sum('other_office_commission'),
            'creator_name'                  => $user->name ?? 'مسؤول النظام',
            'print_date'                    => now()->timezone('Asia/Aden')->format('Y-m-d h:i A'),
        ];
    }

    public function getTemplatePath(): string
    {
        return 'receipts.templates.PassangersDetection';
    }

    public function getFileName(array $data): string
    {
        return 'DriverManifest_' . now()->format('Y-m-d') . '.pdf';
    }

    private function parseFilters(string $referenceId): array
    {
        $filters = ['status' => null, 'from' => null, 'to' => null, 'driver_id' => null, 'branch_id' => null];

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
