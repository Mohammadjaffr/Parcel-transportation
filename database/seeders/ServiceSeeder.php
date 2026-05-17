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
                'name' => 'المكاتب الداخليه',
                'slug' => 'Offices_Verified',
                'description' => 'المكاتب موثقة',
                'is_global_active' => false,
            ],
            [
                'name' => 'المكاتب الخارجية',
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
                'name' => 'الركاب',
                'slug' => 'Passengers',
                'description' => ' الركاب ',
                'is_global_active' => true,
            ],
        [
                'name' => 'إشعار إنشاء بيان تسليم (شحنة)',
                'slug' => 'AdminManifestCreated',
                'description' => 'يصل للمكتب عند إنشاء شحنة جديده',
                'is_global_active' => true,
            ],
            [
                'name' => 'إشعار إنشاء شحنة جديدة',
                'slug' => 'AdminShipmentCreated',
                'description' => 'يصل للمكتب عند تسجيل شحنة جديدة في نظامه',
                'is_global_active' => true,
            ],
            [
                'name' => 'إشعار تحديث حالة طرد',
                'slug' => 'AdminShipmentStatusUpdated',
                'description' => 'يصل عند تغير حالة أي طرد تخص المكتب',
                'is_global_active' => true,
            ],
            [
                'name' => 'إشعار قبول طلب الربط',
                'slug' => 'ConnectionAcceptedNotification',
                'description' => 'يصل عند قبول مكتب آخر لطلب الربط',
                'is_global_active' => true,
            ],
            [
                'name' => 'إشعار رفض طلب الربط',
                'slug' => 'ConnectionRejectedNotification',
                'description' => 'يصل عند رفض طلب الربط بين المكاتب',
                'is_global_active' => true,
            ],
            [
                'name' => 'إشعار طلب ربط جديد',
                'slug' => 'ConnectionRequestNotification',
                'description' => 'يصل عندما يطلب مكتب آخر الربط معك',
                'is_global_active' => true,
            ],
            [
                'name' => 'إشعار شحنة قادمة',
                'slug' => 'IncomingPackageNotification',
                'description' => 'يصل للفرع المستلم بأن هناك شحنة في الطريق إليه',
                'is_global_active' => true,
            ],
            [
                'name' => 'إشعار طرد جديدة للعميل',
                'slug' => 'NewShipmentNotification',
                'description' => 'إشعار عام بإنشاء الطرود',
                'is_global_active' => true,
            ],
            [
                'name' => 'إشعار استلام شحنة',
                'slug' => 'PackageReceivedNotification',
                'description' => 'يصل عند وصول شحنة لفرع التسليم',
                'is_global_active' => true,
            ],

            [
                'name' => 'دفع: مدفوع مقدماً',
                'slug' => 'Payment_Prepaid',
                'description' => 'صلاحية استخدام الدفع المسبق (كامل المبلغ)',
                'is_global_active' => true,
            ],
            [
                'name' => 'دفع: جزئي',
                'slug' => 'Payment_Partial',
                'description' => 'صلاحية استخدام الدفع الجزئي عند الإرسال',
                'is_global_active' => true,
            ],
            [
                'name' => 'دفع: عند الاستلام',
                'slug' => 'Payment_COD',
                'description' => 'صلاحية إرسال طرود الدفع عند الاستلام (على المستلم)',
                'is_global_active' => true,
            ],
            [
                'name' => 'دفع: آجل (ذمة)',
                'slug' => 'Payment_Credit',
                'description' => 'صلاحية إنشاء طرود آجلة الدفع للعملاء المسجلين',
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