<?php

namespace App\Services\Receipts\Concerns;

use Carbon\Carbon;

trait FormatsReceiptHeaderData
{
    /**
     * تجهيز مسار وشعار الشركة بصيغة Base64
     */
    public function getLogoBase64($app): ?string
    {
        $imagePath = $app?->logo
            ? public_path('storage/' . $app->logo)
            : public_path('assets/image/icon_without_bg.png');

        if (file_exists($imagePath)) {
            $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
            $data = @file_get_contents($imagePath);
            if ($data) {
                return 'data:image/' . $extension . ';base64,' . base64_encode($data);
            }
        }
        return null;
    }

    /**
     * جلب وتجهيز قائمة الفروع المعتمدة ديناميكياً
     */
    public function getBranchAddresses($app): array
    {
        if (!$app) {
            return [];
        }

        $effective = $app->effective_branch_addresses;
        if (!empty($effective) && is_array($effective) && count($effective) > 0) {
            return array_values(array_map(function ($item) {
                $phone = $item['phone'] ?? null;
                return [
                    'name'    => $item['name'] ?? '',
                    'address' => $item['address'] ?? '',
                    'phone'   => !empty($phone) ? $this->formatPhoneNumber($phone) : null,
                ];
            }, $effective));
        }

        $branches = $app->branches()->get();
        if ($branches && $branches->count() > 0) {
            return $branches->map(function ($b) {
                $name = $b->is_main ? 'المركز الرئيسي' : $b->name;
                $addressParts = [];
                if (!empty($b->city)) {
                    $addressParts[] = trim($b->city);
                }
                if (!empty($b->address)) {
                    $addressParts[] = trim($b->address);
                }
                return [
                    'name'    => $name,
                    'address' => implode(' - ', $addressParts) ?: 'العنوان غير مسجل',
                    'phone'   => !empty($b->phone) ? $this->formatPhoneNumber($b->phone) : null,
                ];
            })->toArray();
        }

        return [];
    }

    /**
     * تفكيك التاريخ واليوم والوقت وفق المتطلبات الرسمية
     */
    public function prepareDateInfo($date = null): array
    {
        $carbon = null;
        if ($date instanceof Carbon) {
            $carbon = $date->copy()->timezone('Asia/Aden');
        } elseif (!empty($date)) {
            try {
                $carbon = Carbon::parse($date)->timezone('Asia/Aden');
            } catch (\Throwable $e) {
                $carbon = now()->timezone('Asia/Aden');
            }
        } else {
            $carbon = now()->timezone('Asia/Aden');
        }

        return [
            'day_name'       => $carbon->locale('ar')->translatedFormat('l'),
            'formatted_date' => $carbon->format('Y / m / d') . ' م',
            'formatted_time' => $carbon->locale('ar')->translatedFormat('h:i A'),
            'date_full'      => $carbon->locale('ar')->translatedFormat('l Y-m-d H:i'),
            'date_simple'    => $carbon->format('Y-m-d'),
        ];
    }

    /**
     * تنظيف وتنسيق أرقام الهواتف وإزالة مكررات مفاتيح الدول
     */
    public function formatPhoneNumber(?string $phone): string
    {
        if (empty($phone) || trim($phone) === '' || trim($phone) === '---') {
            return '---';
        }

        $parts = preg_split('/[\s,\/]+/', trim($phone));
        $formattedParts = [];

        $countries = config('countries', []);
        $dialCodes = [];
        foreach ($countries as $country) {
            $rawCode = ltrim($country['dial_code'] ?? '', '+');
            if ($rawCode) {
                $dialCodes[] = $rawCode;
            }
        }
        $dialCodes = array_unique(array_merge($dialCodes, ['967', '966', '968', '971']));
        usort($dialCodes, fn($a, $b) => strlen($b) <=> strlen($a));

        foreach ($parts as $part) {
            $cleaned = trim($part);
            if ($cleaned === '') continue;

            foreach ($dialCodes as $rawCode) {
                if (preg_match('/^(\+|00)?' . preg_quote($rawCode, '/') . '/', $cleaned)) {
                    $cleaned = preg_replace('/^(\+|00)?' . preg_quote($rawCode, '/') . '/', '', $cleaned);
                    break;
                }
            }
            $formattedParts[] = $cleaned;
        }

        return !empty($formattedParts) ? implode(' - ', $formattedParts) : '---';
    }
}

