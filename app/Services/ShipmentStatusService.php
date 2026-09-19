<?php

namespace App\Services;

use App\Models\User;
use App\Models\Shipment;
use App\Models\ShipmentPackage;
use Illuminate\Support\Facades\Notification;

class ShipmentStatusService
{
    /**
     * دالة مساعدة للتحقق من الإرسالية وإغلاقها إذا فرغت
     */
    public function checkAndClosePackage($packageId)
    {
        if ($packageId) {
            $activeCount = Shipment::where('shipment_package_id', $packageId)
                ->whereNotIn('status', ['delivered', 'cancelled', 'returned'])->count();
            if ($activeCount === 0) {
                ShipmentPackage::where('id', $packageId)->update(['status' => 'delivered']);
            }
        }
    }

    /**
     * دالة معالجة الإشعارات للإدارة والفروع
     */
    public function handleNotifications($shipment, $newStatus, $user)
    {
        $admins = User::where('app_id', $user->app_id)->where('type', 'admin')->get();
        if ($admins->isNotEmpty()) {
            $statusNamesAr = [
                'pending' => 'قيد التجهيز', 'in_transit' => 'قيد النقل',
                'received_at_branch' => 'وصل المستودع', 'out_for_delivery' => 'خرج للتوصيل',
                'delivered' => 'تم التسليم', 'cancelled' => 'ملغي',
            ];
            $statusText = ($shipment->is_returned && $newStatus === 'pending') ? 'مرتجع قيد العودة' : ($statusNamesAr[$newStatus] ?? $newStatus);
            
            Notification::send($admins, new \App\Notifications\AdminShipmentStatusUpdated($user->name, $shipment->bond_number, $statusText, $shipment->id));
        }
    }

    /**
     * معالجة الإجراءات الجانبية للإرساليات (Packages)
     */
    public function handlePackageSideEffects($shipment, $newStatus, $user)
    {
        if ($shipment->shipment_package_id && in_array($newStatus, ['received_at_branch', 'delivered'])) {
            $package = ShipmentPackage::find($shipment->shipment_package_id);
            if ($package && $package->status === 'in_transit') {
                // إرسال إشعار لفرع المصدر بوصول الطرد
                $senderBranchUsers = User::where('branch_id', $shipment->sender_branch_id)->get();
                if ($senderBranchUsers->isNotEmpty()) {
                    Notification::send($senderBranchUsers, new \App\Notifications\PackageReceivedNotification($package->tracking_number, $user->branch->name ?? 'الفرع المستلم', $shipment->bond_number, $shipment->id));
                }
                // إغلاق الإرسالية إذا سلمت كل طرودها
                $remaining = Shipment::where('shipment_package_id', $package->id)->whereIn('status', ['pending', 'in_transit'])->count();
                if ($remaining === 0) { $package->update(['status' => 'delivered']); }
            }
        }
    }
}
