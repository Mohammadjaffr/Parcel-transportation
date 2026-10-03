<?php

namespace App\Services\Receipts\Types;

use App\Models\Passengers;
use App\Interfaces\ReceiptStrategyInterface;
use Carbon\Carbon;

class PassangerDetection implements ReceiptStrategyInterface
{
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

        // تجميع الركاب حسب السائق
        $grouped = $passengers->groupBy(function ($passenger) {
            return $passenger->driver_id ?: 'unassigned';
        });

        $driversData = [];
        foreach ($grouped as $driverId => $groupPassengers) {
            $firstP = $groupPassengers->first();
            $driverName = $firstP->driver->name ?? 'سائق غير محدد';
            $driverPhone = $firstP->driver->phone ?? '';

            $passengersData = [];
            foreach ($groupPassengers as $p) {
                $pNum = $p->passenger_number ?? '---';
                
                // 🌟 معالجة رقم الهاتف (إزالة 967 لليمن، وإضافة + للبقية) 🌟
                $displayPhone = $pNum;
                if ($pNum !== '---') {
                    if (str_starts_with($pNum, '967')) {
                        $displayPhone = substr($pNum, 3); // إزالة مفتاح اليمن 967
                    } elseif (strlen($pNum) > 0) {
                        $displayPhone = '+' . $pNum; // إضافة علامة الزائد للدول الأخرى مثل السعودية 966
                    }
                }

                $passengersData[] = [
                    'date'             => $p->date ? \Carbon\Carbon::parse($p->date)->format('Y-m-d') : '---',
                    'day'              => $p->date ? \Carbon\Carbon::parse($p->date)->locale('ar')->translatedFormat('l') : '---',
                    'passenger_number' => $displayPhone,
                    'pickup_location'  => $p->pickup_location ?? '---',
                    'destination'      => $p->destination ?? '---',
                    'count'            => $p->count ?? 0,
                    'note'             => $p->note ?: '---',
                ];
            }

            $driversData[] = [
                'driver_id'              => $driverId,
                'driver_name'            => $driverName,
                'driver_phone'           => $driverPhone ?: '---',
                'passengers'             => $passengersData,
                'total_passengers_count' => $groupPassengers->count(),
                'total_count'            => $groupPassengers->sum('count'),
            ];
        }

       $totalOffice = $passengers->sum('office_commission');
        $totalOther  = $passengers->sum('other_office_commission');
        $totalAll    = $totalOffice + $totalOther;

        $mainBranchData = null;
        if ($currentBranch) {
            $mainBranchData = [
                'title' => 'فرع / ' . $currentBranch->name . ($currentBranch->address ? ' - ' . $currentBranch->address : ''),
                'phones' => implode(' - ', array_filter(array_map('trim', preg_split('/[\s,\-]+/', $currentBranch->phone ?? ''))))
            ];
        }

        $otherPhonesList = [];
        $headquartersData = null;
        if ($app) {
            if ($app->phone) {
                $hqPhoneArray = array_filter(array_map('trim', preg_split('/[\s,\-]+/', $app->phone)));
                if (!empty($hqPhoneArray)) {
                    $headquartersData = [
                        'title' => 'الفرع الرئيسي' . ($app->address ? ' - ' . $app->address : ''),
                        'phones' => implode(' - ', $hqPhoneArray)
                    ];
                }
            }

            $allBranches = $app->branches()->get();
            foreach ($allBranches as $b) {
                if ($currentBranch && $b->id === $currentBranch->id) continue;
                $phonesArray = array_filter(array_map('trim', preg_split('/[\s,\-]+/', $b->phone ?? '')));
                $otherPhonesList = array_merge($otherPhonesList, $phonesArray);
            }
        }
        $otherPhonesStr = !empty($otherPhonesList) ? implode(' - ', array_unique($otherPhonesList)) : null;

        return [
            'company' => [
                'name' => $app?->name ?? 'اسم الشركة غير محدد',
                'logo' => $logoBase64,
                'main_branch'  => $mainBranchData,
                'headquarters' => $headquartersData,
                'other_phones' => $otherPhonesStr,


            ],
            'title'                    => "كشف تسليم السائق",
            'date_from'                => $filters['from'] ?? null,
            'date_to'                  => $filters['to'] ?? null,
            'drivers'                  => $driversData,
            
            'totalOfficeCommissionAll' => $totalOffice, 
            'totalOtherCommissionAll'  => $totalOther,
            'totalCommissionall'       => $totalAll,
            
            'total_passengers'         => $passengers->count(),
            'total_count'              => $passengers->sum('count'),
            'creator_name'             => $user->name ?? 'مسؤول النظام',
            'print_date'               => Carbon::now()->locale('ar')->translatedFormat('l Y-m-d H:i'),
        ];
    }

    public function getTemplatePath(): string
    {
        // 🌟 توجيه التقرير لملف Blade خاص بالسائق فقط 🌟
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