<?php

namespace App\Services\Receipts\Types;

use App\Models\Passengers;
use App\Interfaces\ReceiptStrategyInterface;
use Carbon\Carbon;

class PassangerDetection implements ReceiptStrategyInterface
{
    public function sizepage(): string|array
    {
        return 'A4'; // يفضل A4 عمودي (Portrait) أو أفقي (Landscape) حسب الواجهة
    }

    public function fetchData(string $referenceId): array
    {
        $user = auth()->user();
        $filters = [];
        
        if (is_numeric($referenceId)) {
            $query = Passengers::with(['driver', 'branch'])->where('branch_id', $user->branch_id)->where('id', $referenceId);
        } else {
            $filters = $this->parseFilters($referenceId);
            $query = Passengers::with(['driver', 'branch'])->where('branch_id', $user->branch_id)->latest('date');

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
        $app = $user->app ?? null;

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
        $totalOther  = $passengers->sum('other_commission');
        $totalAll    = $totalOffice + $totalOther;

        return [
            'company' => [
                'name' => $app?->name ?? 'اسم الشركة غير محدد',
                'logo' => $logoBase64,
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
        $filters = ['status' => null, 'from' => null, 'to' => null, 'driver_id' => null];
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