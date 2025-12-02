<?php

namespace App\Services;

use App\Repositories\ServiceRepository;
use App\Repositories\AppSettingRepository;

class PriceCalculationService{
/**
     * Create a new class instance.
     */
    public function __construct(private ServiceRepository $serviceRepository, private AppSettingRepository $appSettingRepository    )
    {
        //
    }
    public function calculateBasePrice($serviceId, $distanceKm)
    {
        $service = $this->serviceRepository->getById($serviceId);
    
        $basePrice = $service->base_price;
        $distanceCost = $service->price_per_km * $distanceKm;
        $originalPrice = $basePrice + $distanceCost;

        return [
            'service' => [
                'id' => $service->id,
                'name' => $service->name,
                'base_price' => $service->base_price,
                'price_per_km' => $service->price_per_km
            ],
            'distance_km' => $distanceKm,
            'original_price' => $originalPrice,
            'discount_applied' => false,
            'discount_amount' => 0,
            'final_price' => $originalPrice,
        ];
    }

    public function calculatePriceWithDiscount($serviceId, $distanceKm, $coupon)
    {
        
        $baseResult = $this->calculateBasePrice($serviceId, $distanceKm);
        $originalPrice = $baseResult['original_price'];

        
        $discountAmount = $this->calculateDiscountAmount($originalPrice, $coupon);
        $finalPrice = max(0, $originalPrice - $discountAmount);

        
        return [
            'service' => $baseResult['service'],
            'distance_km' => $baseResult['distance_km'],
            'original_price' => $originalPrice,
            'discount_applied' => true,
            'discount_amount' => $discountAmount,
            'final_price' => $finalPrice,
        ];
    }

     private function calculateDiscountAmount($originalPrice, $coupon)
    {
        return ($originalPrice * $coupon->discount_rate) / 100;
    }
    public function calculateCommission($finalPrice){
        $setting = $this->appSettingRepository->getSetting();

        $commissionRate = $setting ? $setting->commission_rate : 10;

        $commission = ($finalPrice * $commissionRate) / 100;
        return $commission;
     }


}