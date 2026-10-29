<?php

namespace App\Services\Receipts\Types;

use App\Models\Passengers;
use App\Interfaces\ReceiptStrategyInterface;
use App\Services\Receipts\Concerns\FormatsReceiptHeaderData;
use Carbon\Carbon;

class PassangerDetection implements ReceiptStrategyInterface
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
            $query = Passengers::with(['driver', 'branch']);
            if (is_numeric($referenceId)) {
                $query->where('id', $referenceId);
            } else {
                $query->where('uuid', $referenceId);
            }
        } else {
            $filters = $this->parseFilters($referenceId);
            $branchId = $filters['branch_id'] ?? ($user ? $user->branch_id : null);
            
            if (!$branchId) {
                abort(400, 'معرف الفرع مطلوب لعرض هذه البيانات.');
            }

            $query = Passengers::with(['driver', 'branch'])->where('branch_id', $branchId)->latest('date');

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

        // تجميع الركاب حسب السائق
        $grouped = $passengers->groupBy(function ($passenger) {
            return $passenger->driver_id ?: 'unassigned';
        });

        $driversData = [];
        $firstDriverName = null;
        $firstDriverPhone = null;
        $firstDriverVehicle = null;

        foreach ($grouped as $driverId => $groupPassengers) {
            $firstP = $groupPassengers->first();
            $driverName = $firstP->driver->name ?? 'سائق غير محدد';
            $driverPhone = $firstP->driver->phone ?? '';
            $driverVehicle = $firstP->driver->vehicle_type ?? '---';

            if (!$firstDriverName) {
                $firstDriverName = $driverName;
                $firstDriverPhone = $driverPhone;
                $firstDriverVehicle = $driverVehicle;
            }

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

                $pDate = $p->date ? $p->date->format('Y-m-d') : '---';
                $pDay = $p->date ? $p->date->translatedFormat('l') : '---';

                $passengersData[] = [
                    'id'               => $p->id,
                    'passenger_name'   => $p->passenger_name ?? '---',
                    'passenger_phone'  => $displayPhone,
                    'passenger_number' => $displayPhone,
                    'count'            => $p->count ?? 1,
                    'date'             => $pDate,
                    'day'              => $pDay,
                    'pickup_point'     => $p->pickup_point ?? '---',
                    'pickup_location'  => $p->pickup_point ?? '---',
                    'dropoff_point'    => $p->dropoff_point ?? '---',
                    'destination'      => $p->dropoff_point ?? '---',
                    'vehicle_type'     => $p->vehicle_type ?? '---',
                    'office_commission'=> $p->office_commission ?? 0,
                    'other_commission' => $p->other_office_commission ?? 0,
                    'status'           => $p->status ?? '---',
                    'notes'            => $p->notes ?? '---',
                    'note'             => $p->notes ?? '---',
                ];
            }

            $driversData[] = [
                'driver_name'            => $driverName,
                'driver_phone'           => $this->formatPhoneNumber($driverPhone),
                'passengers'             => $passengersData,
                'total_passengers'       => count($passengersData),
                'total_passengers_count' => count($passengersData),
                'total_count'            => $groupPassengers->sum('count'),
                'total_office'           => $groupPassengers->sum('office_commission'),
                'total_other'            => $groupPassengers->sum('other_office_commission'),
            ];
        }

        $totalOffice = $passengers->sum('office_commission');
        $totalOther  = $passengers->sum('other_office_commission');
        $totalAll    = $totalOffice + $totalOther;

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
            'branch_addresses'         => $branchAddresses,

            'day_name'                 => $dateInfo['day_name'],
            'formatted_date'           => $dateInfo['formatted_date'],
            'formatted_time'           => $dateInfo['formatted_time'],

            'title'                    => "كشف تسليم السائق",
            'package_number'           => $singlePass?->id ?? '---',
            'manifest_number'          => $singlePass?->id ?? '---',
            'driver_name'              => $firstDriverName ?? 'غير محدد',
            'driver_phone'             => $this->formatPhoneNumber($firstDriverPhone),
            'vehicle_type'             => $firstDriverVehicle ?? '---',
            'sender_branch'            => $currentBranch?->name ?? 'المركز الرئيسي',

            'date_from'                => $filters['from'] ?? null,
            'date_to'                  => $filters['to'] ?? null,
            'drivers'                  => $driversData,
            
            'totalOfficeCommissionAll' => $totalOffice, 
            'totalOtherCommissionAll'  => $totalOther,
            'totalCommissionall'       => $totalAll,
            
            'total_passengers'         => $passengers->count(),
            'total_count'              => $passengers->sum('count'),
            'creator_name'             => $user->name ?? 'مسؤول النظام',
            'print_date'               => now()->timezone('Asia/Aden')->format('Y-m-d h:i A'),
        ];
    }

    public function getTemplatePath(): string
    {
        return 'receipts.templates.PassangerDetection';
    }

    public function getFileName(array $data): string
    {
        return 'Driver_Report_' . now()->format('Y-m-d') . '.pdf';
    }

    private function parseFilters(string $referenceId): array
    {
        $filters = ['status' => null, 'from' => null, 'to' => null, 'driver_id' => null, 'branch_id' => null];
        if ($referenceId === 'all') return $filters;

        $parts = explode('|', $referenceId);
        foreach ($parts as $part) {
            $segments = explode(':', $part, 2);
            if (count($segments) === 2 && array_key_exists(trim($segments[0]), $filters)) {
                $filters[trim($segments[0])] = trim($segments[1]);
            }
        }
        return $filters;
    }
}
