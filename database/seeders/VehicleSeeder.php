<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vehicle;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicles = [
            [
                'name' => 'دباب',
                'description' =>'دباب مريح وسهل الركوب',
                'max_passengers' => 2
            ],
            [
                'name' => 'فوكسي',
                'description' => 'سيارة واسعة مناسبة للعائلات والطرق الوعرة',
                'max_passengers' => 7
            ],
            [
                'name' => ' تاكسي ',
                'description' => 'مركبة كبيرة مناسبة لنقل البضائع والمجموعات',
                'max_passengers' => 6
            ],
            [
                'name' => 'سيارة VIP',
                'description' => 'سيارة موفرة للوقود مناسبة للرحلات القصيرة',
                'max_passengers' => 4
            ],
        ];
        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }

    }
}
