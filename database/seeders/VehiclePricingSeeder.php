<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\vehicle_pricing;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class VehiclePricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('vehicle_pricings')->truncate();
        $vehicles = Vehicle::all();

        foreach ($vehicles as $vehicle) {
            switch ($vehicle->name) {
                case 'دباب':
                    $this->createMotorcyclePricing($vehicle->id);
                    break;
                
                case 'فوكسي':
                    $this->createSuvPricing($vehicle->id);
                    break;
                
                case 'تاكسي':
                    $this->createTaxiPricing($vehicle->id);
                    break;
                
                case 'سيارة VIP':
                    $this->createVipPricing($vehicle->id);
                    break;
                
                default:
                    $this->createDefaultPricing($vehicle->id);
                    break;
            }
        }
    }
    
    private function createMotorcyclePricing($vehicleId): void
    {
        $pricing = [
            ['vehicle_id' => $vehicleId, 'base_price' => 2500.00, 'min_distance_km' => 0, 'max_distance_km' => 2],
            ['vehicle_id' => $vehicleId, 'base_price' => 950.00, 'min_distance_km' => 3, 'max_distance_km' => 5],
            ['vehicle_id' => $vehicleId, 'base_price' => 850.00, 'min_distance_km' => 6, 'max_distance_km' => 10],
            ['vehicle_id' => $vehicleId, 'base_price' => 750.00, 'min_distance_km' => 11, 'max_distance_km' => 15],
            ['vehicle_id' => $vehicleId, 'base_price' => 650.00, 'min_distance_km' => 16, 'max_distance_km' => 20],
            ['vehicle_id' => $vehicleId, 'base_price' => 600.00, 'min_distance_km' => 21, 'max_distance_km' => 30],
            ['vehicle_id' => $vehicleId, 'base_price' => 500.00, 'min_distance_km' => 31, 'max_distance_km' => 40],
        ];

        vehicle_pricing::insert($pricing);
    }

    private function createSuvPricing($vehicleId): void
    {
        $pricing = [
            ['vehicle_id' => $vehicleId, 'base_price' => 3000.00, 'min_distance_km' => 0, 'max_distance_km' => 2],
            ['vehicle_id' => $vehicleId, 'base_price' => 1100.00, 'min_distance_km' => 3, 'max_distance_km' => 5],
            ['vehicle_id' => $vehicleId, 'base_price' => 1000.00, 'min_distance_km' => 6, 'max_distance_km' => 10],
            ['vehicle_id' => $vehicleId, 'base_price' => 950.00, 'min_distance_km' => 11, 'max_distance_km' => 15],
            ['vehicle_id' => $vehicleId, 'base_price' => 900.00, 'min_distance_km' => 16, 'max_distance_km' => 20],
            ['vehicle_id' => $vehicleId, 'base_price' => 800.00, 'min_distance_km' => 21, 'max_distance_km' => 30],
            ['vehicle_id' => $vehicleId, 'base_price' => 750.00, 'min_distance_km' => 31, 'max_distance_km' => 40],
        ];

        vehicle_pricing::insert($pricing);
    }
    private function createTaxiPricing($vehicleId): void
    {
        $pricing = [
            ['vehicle_id' => $vehicleId, 'base_price' => 3500.00, 'min_distance_km' => 0, 'max_distance_km' => 2],
            ['vehicle_id' => $vehicleId, 'base_price' => 1500.00, 'min_distance_km' => 3, 'max_distance_km' => 5],
            ['vehicle_id' => $vehicleId, 'base_price' => 1400.00, 'min_distance_km' => 6, 'max_distance_km' => 10],
            ['vehicle_id' => $vehicleId, 'base_price' => 1300.00, 'min_distance_km' => 11, 'max_distance_km' => 15],
            ['vehicle_id' => $vehicleId, 'base_price' => 1200.00, 'min_distance_km' => 16, 'max_distance_km' => 20],
            ['vehicle_id' => $vehicleId, 'base_price' => 1100.00, 'min_distance_km' => 21, 'max_distance_km' => 30],
            ['vehicle_id' => $vehicleId, 'base_price' => 1000.00, 'min_distance_km' => 31, 'max_distance_km' => 40],
        ];

        vehicle_pricing::insert($pricing);
    }
    private function createVipPricing($vehicleId): void
    {
        $pricing = [
            ['vehicle_id' => $vehicleId, 'base_price' => 5000.00, 'min_distance_km' => 0, 'max_distance_km' => 2],
            ['vehicle_id' => $vehicleId, 'base_price' => 1200.00, 'min_distance_km' => 3, 'max_distance_km' => 5],
            ['vehicle_id' => $vehicleId, 'base_price' => 1100.00, 'min_distance_km' => 6, 'max_distance_km' => 10],
            ['vehicle_id' => $vehicleId, 'base_price' => 1000.00, 'min_distance_km' => 11, 'max_distance_km' => 15],
            ['vehicle_id' => $vehicleId, 'base_price' => 950.00, 'min_distance_km' => 16, 'max_distance_km' => 20],
            ['vehicle_id' => $vehicleId, 'base_price' => 850.00, 'min_distance_km' => 21, 'max_distance_km' => 30],
            ['vehicle_id' => $vehicleId, 'base_price' => 800.00, 'min_distance_km' => 31, 'max_distance_km' => 40],
        ];

        vehicle_pricing::insert($pricing);
    }
    private function createDefaultPricing($vehicleId): void
    {
        $pricing = [
            ['vehicle_id' => $vehicleId, 'base_price' => 2500.00, 'min_distance_km' => 0, 'max_distance_km' => 2],
            ['vehicle_id' => $vehicleId, 'base_price' => 1000.00, 'min_distance_km' => 3, 'max_distance_km' => 5],
            ['vehicle_id' => $vehicleId, 'base_price' => 700.00, 'min_distance_km' => 6, 'max_distance_km' => 10],
            ['vehicle_id' => $vehicleId, 'base_price' => 500.00, 'min_distance_km' => 11, 'max_distance_km' => 999.99],
        ];

        vehicle_pricing::insert($pricing);
    }
    
}
