<?php

namespace App\Services;

use App\Models\Driver;

class DriverLocationService{
    public function getNearestDrivers($latitude, $longitude, $limit = 8, $maxRadiusKm = 50)
{
    $foundDrivers = collect();
    $currentRadius = 2; 
    
    while ($currentRadius <= $maxRadiusKm && $foundDrivers->count() < $limit) {
        // حساب عدد السائقين المطلوبين
        $remaining = $limit - $foundDrivers->count();
        
        $driversInRadius = Driver::select('drivers.*')
            ->selectRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                [$latitude, $longitude, $latitude]
            )
            ->where('is_active', true)
            ->where('is_online', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereNotNull('device_token')
            ->whereNotIn('id', $foundDrivers->pluck('id')->toArray())
            ->having('distance', '<=', $currentRadius)
            ->orderBy('distance', 'asc')
            ->limit($remaining * 2)
            ->get();

        // إضافة السائقين الجدد
        foreach ($driversInRadius as $driver) {
            $foundDrivers->push($driver);
            if ($foundDrivers->count() >= $limit) {
                break;
            }
        }

        // زيادة نصف القطر
        if ($foundDrivers->count() == 0) {
            $currentRadius += 5;
        } else {
            $currentRadius += 3;
        }
    }

    
    if ($foundDrivers->isNotEmpty() && $foundDrivers->count() < $limit && $currentRadius > $maxRadiusKm) {
    }

    
    if ($foundDrivers->isEmpty()) {
        return null;
    }
    return $foundDrivers;
}


}