<?php
//  معتمد
namespace App\Services;

use App\Models\Shipment;
use App\Models\CustomerTransaction;

class CustomerTransactionService
{
    /**
     * 1. الحركات المالية الأولية (عند إنشاء الطرد)
     */
    public function recordInitialShipment(Shipment $shipment)
    {
        if ($shipment->payment_method === 'customer_credit') {
            // آجل: تسجيل دين فقط
            $this->createTransaction($shipment, $shipment->total_amount, 'debit', "رسوم شحن آجل لطرد رقم #{$shipment->bond_number}");
        } 
        elseif ($shipment->payment_method === 'prepaid') {
            // مسبق: قيد مزدوج (دين + سداد) لتصفير الرصيد
            $this->createTransaction($shipment, $shipment->total_amount, 'debit', "تكلفة شحن لطرد رقم #{$shipment->bond_number}");
            $this->createTransaction($shipment, $shipment->total_amount, 'credit', "سداد نقدي (دفع مسبق) لطرد رقم #{$shipment->bond_number}");
        } 
        elseif ($shipment->payment_method === 'partial_payment') {
            // جزئي: قيد مزدوج (للجزء المدفوع فقط)
            $this->createTransaction($shipment, $shipment->partial_amount, 'debit', "جزء من تكلفة شحن لطرد رقم #{$shipment->bond_number}");
            $this->createTransaction($shipment, $shipment->partial_amount, 'credit', "سداد نقدي (دفع جزئي) لطرد رقم #{$shipment->bond_number}");
        }
    }

    /**
     * 2. الحركات المالية عند التسليم (تسجيل أرصدة للمرسل)
     */
    public function recordDelivery(Shipment $shipment)
    {
        if ($shipment->payment_method === 'cod') {
            // الدفع عند الاستلام: كامل المبلغ رصيد للمرسل
            $this->createTransaction($shipment, $shipment->total_amount, 'credit', "متحصلات بوليصة دفع عند الاستلام #{$shipment->bond_number}");
        } 
        elseif ($shipment->payment_method === 'partial_payment') {
            // دفع جزئي: المتبقي من المبلغ رصيد للمرسل
            $remainingAmount = $shipment->total_amount - $shipment->partial_amount;
            if ($remainingAmount > 0) {
                $this->createTransaction($shipment, $remainingAmount, 'credit', "باقي متحصلات بوليصة دفع جزئي #{$shipment->bond_number}");
            }
        }
    }

    /**
     * 3. إلغاء الحركات المالية (إذا أصبح الطرد مرتجعاً للتاجر)
     */
    public function cancelTransactions(Shipment $shipment)
    {
        // نحذف الديون (debit) المعلقة التي لم يتم تسديدها
        CustomerTransaction::where('shipment_id', $shipment->id)
            ->where('type', 'debit')
            ->delete();
    }

    /**
     * دالة مساعدة داخلية لتقليل تكرار الكود (Helper)
     */
    private function createTransaction(Shipment $shipment, $amount, $type, $description)
    {
        CustomerTransaction::create([
            'customer_id' => $shipment->sender_customer_id,
            'shipment_id' => $shipment->id,
            'amount'      => $amount,
            'type'        => $type,
            'description' => $description,
        ]);
    }
}