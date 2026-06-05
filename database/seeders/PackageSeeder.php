<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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

        User::create([
            'name' => 'تيار',
            'phone' => '967780261952',
            'password' => Hash::make('123456789'),
            'type' => 'super_admin',
            'whatsapp_number' => '967780261952',
            'is_phone_verified' => true,

        ]);
        User::create([
            'name' => 'السعدي',
            'phone' => '967775190521',
            'password' => Hash::make('123456789'),
            'type' => 'admin',
            'whatsapp_number' => '967775190521',
            'is_phone_verified' => true,

        ]);
        
        
     
    }
}