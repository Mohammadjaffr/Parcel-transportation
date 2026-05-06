<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'السائقين',
                'slug' => 'Drivers',
                'description' => 'إدارة السائقين',
                'is_global_active' => true,
            ],
            [
                'name' => 'الركاب',
                'slug' => 'Passengers',
                'description' => 'إدارة الركاب',
                'is_global_active' => true,
            ],
            [
                'name' => 'المستخدمين',
                'slug' => 'Users',
                'description' => 'إدارة المستخدمين',
                'is_global_active' => true,
            ],
            [
                'name' => 'العملاء',
                'slug' => 'Customers',
                'description' => 'إدارة العملاء',
                'is_global_active' => true,
            ],
            [
                'name' => 'المكاتب موثوقه',
                'slug' => 'Offices_Verified',
                'description' => 'المكاتب موثقة',
                'is_global_active' => true,
            ],
            [
                'name' => 'المكاتب الغير موثوقه',
                'slug' => 'Offices_Unverified',
                'description' => 'المكاتب الغير موثقة',
                'is_global_active' => true,
            ],
            [
                'name' => 'الطرود المرسله',
                'slug' => 'Shipment_Out',
                'description' => 'إدارة الطرود المرسله',
                'is_global_active' => true,
            ],
            [
                'name' => 'الطرود المستلمة',
                'slug' => 'Shipment_In',
                'description' => 'إدارة الطرود المستلمة',
                'is_global_active' => true,
            ],
            [
                'name' => 'الشحنات المرسلة',
                'slug' => 'Package_Out',
                'description' => 'إدارة الشحنات المرسلة',
                'is_global_active' => true,
            ],
            [
                'name' => 'الشحنات المستلمة',
                'slug' => 'Package_In',
                'description' => 'إدارة الشحنات المستلمة',
                'is_global_active' => true,
            ],
            [
                'name' => 'الاشعارات',
                'slug' => 'Notifications',
                'description' => 'ادارة الاشعارات',
                'is_global_active' => true,
            ],
            [
                'name' => 'تقرير الركاب',
                'slug' => 'Passengers',
                'description' => ' الركاب ',
                'is_global_active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['slug' => $service['slug']], 
                $service
            );
        }
    }
}