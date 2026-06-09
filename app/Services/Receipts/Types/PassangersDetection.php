<?php

namespace App\Services\Receipts\Types;

use App\Models\Passengers;
use App\Interfaces\ReceiptStrategyInterface;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PassangersDetection implements ReceiptStrategyInterface
{
    public function sizepage(): string|array
    {
        return 'A4';
    }

    public function fetchData(string $referenceId): array
    {
        $user = auth()->user();
         // 🔒 حماية دفاعية: إذا كان المستخدم غير مسجل، اقطع الطلب فوراً واعرض صفحة غير مصرح
        if (!$user) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه البيانات، يرجى تسجيل الدخول أولاً.');
        }
        // referenceId يمكن أن يكون 'all' أو يحتوي فلاتر أو رقم معرف راكب مفرد
        $filters = [];
        if (is_numeric($referenceId)) {
            $query = Passengers::with(['driver', 'broker', 'branch'])->where('branch_id', $user->branch_id)->where('id', $referenceId);
        } else {
            $filters = $this->parseFilters($referenceId);
            $query = Passengers::with(['driver', 'broker', 'branch'])->where('branch_id', $user->branch_id)->latest('date');

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

        $statusLabels = [
            'pending'   => 'قيد الانتظار',
            'confirmed' => 'مؤكد',
            'completed' => 'مكتمل',
            'cancel'    => 'ملغي',
        ];

        // تجميع الركاب حسب السائق
        $grouped = $passengers->groupBy(function ($passenger) {
            return $passenger->driver_id ?: 'unassigned';
        });

        $driversData = [];
        foreach ($grouped as $driverId => $groupPassengers) {
            $firstP = $groupPassengers->first();
            $driverName = $firstP->driver->name ?? 'سائق غير محدد';
            $driverPhone = $firstP->driver->phone ?? '';

            // بناء نص رسالة الواتساب للسائق
            $msg = "السلام عليكم ورحمة الله وبركاته\n";
            $msg .= "الأخ الكابتن / *{$driverName}* المحترم،\n\n";
            $msg .= "إليك كشف الركاب المكلف بنقلهم اليوم:\n\n";
            $msg .= "*تفاصيل الركاب:*\n";
            $msg .= "------------------------------------------\n";

            $passengersData = [];
            $i = 1;
            foreach ($groupPassengers as $p) {
                $pNum = $p->passenger_number ?? '---';
                $pLoc = $p->location ?? '---';
                $pCnt = $p->count ?? 0;
                $pNote = $p->note ? $p->note : '---';

                $passengersData[] = [
                    'date'                    => $p->date ? $p->date->format('Y-m-d') : '---',
                    'passenger_number'        => $pNum,
                    'broker_name'             => $p->broker?->name ?? 'بدون وسيط',
                    'location'                => $pLoc,
                    'count'                   => $pCnt,
                    'office_commission'       => number_format($p->office_commission ?? 0, 0),
                    'other_office_commission' => number_format($p->other_office_commission ?? 0, 0),
                    'status_key'              => $p->status ?? 'pending',
                    'status_label'            => $statusLabels[$p->status ?? 'pending'] ?? 'غير محدد',
                    'note'                    => $p->note,
                ];

                $msg .= "{$i}. الراكب: {$pNum}\n";
                $msg .= "📍 المكان: {$pLoc}\n";
                $msg .= "👥 العدد: {$pCnt}\n";
                if ($p->note) {
                    $msg .= "📝 ملاحظات: {$pNote}\n";
                }
                $msg .= "------------------------------------------\n";
                $i++;
            }

            // بناء رابط الـ PDF الخاص بالسائق مع الحفاظ على الفلاتر النشطة (التاريخ، الحالة)
            $filterParts = [];
            if (!empty($filters['from'])) {
                $filterParts[] = 'from:' . $filters['from'];
            }
            if (!empty($filters['to'])) {
                $filterParts[] = 'to:' . $filters['to'];
            }
            if (!empty($filters['status']) && $filters['status'] !== 'all') {
                $filterParts[] = 'status:' . $filters['status'];
            }
            if ($driverId !== 'unassigned') {
                $filterParts[] = 'driver_id:' . $driverId;
            }

            $referenceIdForDriver = count($filterParts) > 0 ? implode('|', $filterParts) : 'all';

            $pdfLink = route('receipt.generate', [
                'type' => 'passenger',
                'id'   => $referenceIdForDriver
            ]);

            $totalCount = $groupPassengers->sum('count');
            $msg .= "📊 إجمالي عدد الركاب: *{$totalCount}* راكب\n";
            $msg .= "📄 رابط كشف الـ PDF للركاب:\n{$pdfLink}\n\n";
            $msg .= "رافقتكم السلامة. 🚚";

            $whatsappLink = '';
            if (!empty($driverPhone)) {
                $whatsappLink = $this->getWhatsAppLink($driverPhone, $msg);
            }

            $driversData[] = [
                'driver_id'               => $driverId,
                'driver_name'             => $driverName,
                'driver_phone'            => $driverPhone ?: '---',
                'passengers'              => $passengersData,
                'total_passengers_count'  => $groupPassengers->count(),
                'total_count'             => $totalCount,
                'total_office_commission' => $groupPassengers->sum('office_commission'),
                'total_other_office_commission' => $groupPassengers->sum('other_office_commission'),
                'whatsapp_link'           => $whatsappLink,
                'pdf_link'                => $pdfLink,
            ];
        }

        return [
            'company' => [
                'name' => $app?->name ?? 'اسم الشركة غير محدد',
                'logo' => $logoBase64,
            ],
            'title'                         => "كشف الركاب ",
            'date_from'                     => $filters['from'] ?? null,
            'date_to'                       => $filters['to'] ?? null,
            'status_filter'                 => $filters['status'] ?? null,
            'drivers'                       => $driversData,
            'total_passengers'              => $passengers->count(),
            'total_count'                   => $passengers->sum('count'),
            'total_office_commission'       => $passengers->sum('office_commission'),
            'total_other_office_commission' => $passengers->sum('other_office_commission'),
            'creator_name'                  => $user->name ?? 'مسؤول النظام',
            'print_date'                    => Carbon::now()->locale('ar')->translatedFormat('l Y-m-d H:i'),
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

    /**
     * تحليل الفلاتر من referenceId
     * الصيغة: all أو status:pending|from:2026-01-01|to:2026-12-31|driver_id:5
     */
    private function parseFilters(string $referenceId): array
    {
        $filters = ['status' => null, 'from' => null, 'to' => null, 'driver_id' => null];

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