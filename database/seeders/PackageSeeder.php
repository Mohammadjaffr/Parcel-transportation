<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        Package::create([
            'name'             => 'الباقة التجريبية (مجاناً)',
            'price'            => 0.00,
            'duration_in_days' => 14,
            'max_branches'     => 2,
            'max_drivers'      => 0,
            'max_shipments'    => 30,  
            'max_packages'     => 7,  
            'is_active'        => true,
        ]);

        Package::create([
            'name'             => 'الباقة الاحترافية',
            'price'            => 99.00, 
            'duration_in_days' => 30,    
            'max_branches'     => 5,
            'max_drivers'      => 15,
            'max_shipments'    => 2000,
            'max_packages'     => 100,
            'is_active'        => true,
        ]);

        Package::create([
        'name'             => 'باقة الشركات الكبرى (VIP)',
        'price'            => 499.00, 
        'duration_in_days' => 30,    
        'max_branches'     => 0, 
        'max_drivers'      => 0,
        'max_shipments'    => 0,
        'max_packages'     => 0,
        'is_active'        => true,
        ]);
    }
}