<?php

namespace App\Observers;

use App\Models\Branch;
use App\Models\BranchLedger;
use App\Models\CashCategory;
use App\Models\CashTransaction;
use App\Models\Shipment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\CashTransactionService;


class ShipmentObserver
{
    public function __construct(
        protected CashTransactionService $cashTransactionService
    ) {}
    /**
     * دالة مركزية لحساب كل الأمور المالية للشنحة
     */
    private function calculateFinancials(Shipment $shipment): void
    {
        // 1. حساب مبالغ العمولات بناءً على النسب المئوية (إذا لم تكن موجودة نعتبرها صفر)
        $shipment->package_commission_amount = (($shipment->package_fee ?? 0) * ($shipment->package_commission_rate ?? 0)) / 100;
        
        $shipment->honey_commission_amount = (($shipment->honey_fee ?? 0) * ($shipment->honey_commission_rate ?? 0)) / 100;

        // 2. حساب إجمالي العمولات
        $shipment->total_commission = $shipment->package_commission_amount + $shipment->honey_commission_amount;

        // 3. حساب إجمالي مبلغ الشحنة (أجرة الطرد + أجرة العسل)
        $shipment->total_amount = ($shipment->package_fee ?? 0) + ($shipment->honey_fee ?? 0);
    }
    /**
     * Handle the Shipment "created" event.
     */
   public function creating(Shipment $shipment): void
    {
        $this->calculateFinancials($shipment);
    }

    /**
     * Handle the Shipment "updated" event.
     */
    public function updating(Shipment $shipment): void
    {
        $this->calculateFinancials($shipment);
        if ($shipment->isDirty('status') && $shipment->status === 'in_transit') {
            $this->cashTransactionService->recordShipmentCommission($shipment);
        }
    }
    

    /**
     * Handle the Shipment "deleted" event.
     */
    public function deleted(Shipment $shipment): void
    {
        //
    }

    /**
     * Handle the Shipment "restored" event.
     */
    public function restored(Shipment $shipment): void
    {
        //
    }

    /**
     * Handle the Shipment "force deleted" event.
     */
    public function forceDeleted(Shipment $shipment): void
    {
        //
    }
}
