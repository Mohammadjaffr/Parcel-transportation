<?php

namespace App\Services\Receipts\Types;

use App\Models\PassengerTrip;
use App\Interfaces\ReceiptStrategyInterface;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TripsDetection implements ReceiptStrategyInterface
{
    public function sizepage(): string|array
    {
        return 'A4';
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
        if ($trips->isNotEmpty()) {
            $app = $trips->first()->branch?->app ?? null;
        } elseif ($user) {
            $app = $user->app ?? null;
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

            // بناء نص رسالة الواتساب للسائق
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
                $pNote = $p->note ? $p->note : '---';

                $tripTotalCount += $pCnt;
                $tripTotalOffice += $p->office_commission ?? 0;
                $tripTotalOther += $p->other_office_commission ?? 0;
                $totalOfficeCommissionAll += $p->office_commission + $p->other_office_commission ?? 0;   
                $totalPassengers++;

                $passengersData[] = [
                    'date'                    => $p->date ? $p->date->format('Y-m-d') : '---',
                    'passenger_number'        => $pNum,
'broker_name' => $user ? ($p->broker?->name ?? 'بدون وسيط') : null,
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

            // بناء رابط الـ PDF للرحلة
            $pdfLink = route('receipt.generate', [
                'type' => 'trip',
                'id'   => $trip->uuid
            ]);

            $msg .= "📊 إجمالي عدد الركاب: *{$tripTotalCount}* راكب\n";
            $msg .= "📄 رابط كشف الـ PDF للرحلة:\n{$pdfLink}\n\n";
            $msg .= "رافقتكم السلامة. 🚚";

            $whatsappLink = '';
            if (!empty($driverPhone)) {
                $whatsappLink = $this->getWhatsAppLink($driverPhone, $msg);
            }

            $driversData[] = [
                'driver_id'               => $trip->driver_id ?: 'unassigned',
                'driver_name'             => $driverName . " (رحلة #{$trip->id})",
                'driver_phone'            => $driverPhone ?: '---',
                'passengers'              => $passengersData,
                'total_passengers_count'  => count($passengersData),
                'total_count'             => $tripTotalCount,
                'total_office_commission' => $tripTotalOffice,
                'total_other_office_commission' => $tripTotalOther,
                'whatsapp_link'           => $whatsappLink,
                'pdf_link'                => $pdfLink,
            ];

            $totalCount += $tripTotalCount;
            $totalOfficeCommission += $tripTotalOffice;
            $totalOtherOfficeCommission += $tripTotalOther;
            $totalOfficeCommissionAll += $tripTotalOffice + $tripTotalOther;
        }

        return [
            'company' => [
                'name' => $app?->name ?? 'اسم الشركة غير محدد',
                'logo' => $logoBase64,
            ],
            'title'                         => "كشف الرحلات",
            'date_from'                     => $filters['from'] ?? null,
            'date_to'                       => $filters['to'] ?? null,
            'status_filter'                 => null,
            'drivers'                       => $driversData,
            'total_passengers'              => $totalPassengers,
            'total_count'                   => $totalCount,
            'total_office_commission'       => $totalOfficeCommission,
            'total_other_office_commission' => $totalOtherOfficeCommission,
            'creator_name'                  => $user->name ?? 'مسؤول النظام',
            'print_date'                    => Carbon::now()->locale('ar')->translatedFormat('l Y-m-d H:i'),
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

    /**
     * تحليل الفلاتر من referenceId
     * الصيغة: all أو from:2026-01-01|to:2026-12-31|driver_id:5
     */
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