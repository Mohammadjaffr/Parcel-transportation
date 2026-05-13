<?php

namespace App\Services\Receipts\Types;

use App\Models\Passengers;
use App\Interfaces\ReceiptStrategyInterface;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportPassanger implements ReceiptStrategyInterface
{
    public function sizepage(): string|array
    {
        return 'A4';
    }

    public function fetchData(string $referenceId): array
    {
        // referenceId يمكن أن يكون 'all' أو يحتوي فلاتر مثل: status:pending|from:2026-01-01|to:2026-12-31
        $filters = $this->parseFilters($referenceId);

        $query = Passengers::with(['driver', 'customer', 'branch'])->latest('date');

        if (!empty($filters['from'])) {
            $query->whereDate('date', '>=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            $query->whereDate('date', '<=', $filters['to']);
        }
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        $passengers = $query->get();

        $user = auth()->user();
        $app = $user->app ?? null;

        $imagePath = $app?->logo
            ? public_path('storage/' . $app->logo)
            : public_path('assets/image/icon_without_bg.png');

        $logoBase64 = null;
        if (file_exists($imagePath)) {
            $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
            $data = file_get_contents($imagePath);
            $logoBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($data);
        }

        $statusLabels = [
            'pending'   => 'قيد الانتظار',
            'completed' => 'مكتمل',
            'cancel'    => 'ملغي',
        ];

        $passengersData = [];
        foreach ($passengers as $p) {
            $passengersData[] = [
                'date'             => $p->date ? $p->date->format('Y-m-d') : '---',
                'passenger_number' => $p->passenger_number ?? '---',
                'customer_name'    => $p->customer->name ?? 'غير محدد',
                'customer_phone'   => $p->customer->phone ?? '---',
                'driver_name'      => $p->driver->name ?? 'غير محدد',
                'driver_phone'     => $p->driver->phone ?? '---',
                'location'         => $p->location ?? '---',
                'count'            => $p->count ?? 0,
                'total_commission'  => number_format($p->total_commission ?? 0, 0),
                'status_key'       => $p->status ?? 'pending',
                'status_label'     => $statusLabels[$p->status ?? 'pending'] ?? 'غير محدد',
                'note'             => $p->note,
            ];
        }

        return [
            'company' => [
                'name' => $app?->name ?? 'اسم الشركة غير محدد',
                'logo' => $logoBase64,
            ],
            'title'            => 'تقرير جميع الركاب',
            'date_from'        => $filters['from'] ?? null,
            'date_to'          => $filters['to'] ?? null,
            'status_filter'    => $filters['status'] ?? null,
            'passengers'       => $passengersData,
            'total_passengers' => $passengers->count(),
            'total_count'      => $passengers->sum('count'),
            'total_commission' => $passengers->sum('total_commission'),
            'creator_name'     => $user->name ?? 'مسؤول النظام',
            'print_date'       => Carbon::now()->locale('ar')->translatedFormat('l Y-m-d H:i'),
        ];
    }

    public function getTemplatePath(): string
    {
        return 'receipts.templates.ReportAllPassengers';
    }

    public function getFileName(array $data): string
    {
        return 'ReportAllPassengers_' . now()->format('Y-m-d') . '.pdf';
    }

    /**
     * تحليل الفلاتر من referenceId
     * الصيغة: all أو status:pending|from:2026-01-01|to:2026-12-31
     */
    private function parseFilters(string $referenceId): array
    {
        $filters = ['status' => null, 'from' => null, 'to' => null];

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
}