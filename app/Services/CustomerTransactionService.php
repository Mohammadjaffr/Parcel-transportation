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
     * 2. الحركات المالية عند التسليم (مجهزة لمحاسبة الفروع مستقبلاً)
     */
    public function recordDelivery(Shipment $shipment)
    {
        // 🛑 الجزء الخاص بالعميل (المرسل):
        // تم إيقافه لأن المستلم عندما يدفع رسوم الشحن، فإن المبلغ يذهب لخزينة الشركة 
        // ولا يتم تسجيله كرصيد للعميل المرسل (نحن شركة نقل ولسنا متجر).
        
        // ====================================================================
        // 🚀 TODO: [ميزة مستقبلية - محاسبة الفروع Inter-Branch Accounting]
        // ====================================================================
        // مستقبلاً، سنستخدم هذه الدالة لتسجيل المبالغ التي بعهدة الفرع المستلم.
        // الفرع المستلم سيأخذ كاش من الزبون في حالتين فقط (COD أو الدفع الجزئي).
        
        /* $collectedAmount = 0;

        if ($shipment->payment_method === 'cod') {
            // الفرع سيستلم كامل المبلغ
            $collectedAmount = $shipment->total_amount;
        } 
        elseif ($shipment->payment_method === 'partial_payment') {
            // الفرع سيستلم (باقي المبلغ) فقط، لأن المرسل دفع جزءاً منه مسبقاً
            $collectedAmount = $shipment->total_amount - $shipment->partial_amount;
        }

        // إذا كان هناك مبلغ تم تحصيله فعلياً بواسطة الفرع المستلم، نسجله كدين عليه
        if ($collectedAmount > 0) {
             \App\Models\BranchTransaction::create([
                 'branch_id'   => $shipment->receiver_branch_id,
                 'shipment_id' => $shipment->id,
                 'amount'      => $collectedAmount,
                 'type'        => 'debit', // دين على الفرع المستلم (عهدة الكاش التي لديه)
                 'description' => "متحصلات أجور شحن بوليصة #{$shipment->bond_number} لصالح الإدارة",
                 'created_by'  => auth()->id(), // 👈 تسجيل الموظف هنا مستقبلاً
             ]);
        }
        */

        return true; 
    }

    /**
     * 3. إلغاء الحركات المالية (إذا أصبح الطرد مرتجعاً للتاجر)
     */
    public function cancelTransactions(Shipment $shipment)
    {
        // هذا السطر سيقوم بمسح كل الحركات المالية (تكلفة الشحن + أي سداد نقدي تم مسبقاً)
        // لكي يعود كشف حساب العميل نظيفاً وكأن الطرد لم يُنشأ من الأساس
        CustomerTransaction::where('shipment_id', $shipment->id)->delete();
        
        return true;
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
            'created_by'  => auth()->id(), // 👈 تم إضافة الموظف الذي أنشأ الحركة
        ]);
    }

    /**
     * 4. تسجيل دفعة نقدية (سداد مديونية) لحساب العميل بشكل عام
     */
    public function addPayment($customer, $amount, $notes = null)
    {
        return CustomerTransaction::create([
            'customer_id' => $customer->id,
            'amount'      => $amount,
            'type'        => 'credit', // رصيد موجب يقلص الديون
            'description' => 'سداد نقدي لحساب العميل' . ($notes ? ' - ' . $notes : ''),
            'shipment_id' => null, // دفعة عامة غير مرتبطة بطرد معين
            'created_by'  => auth()->id(), // 👈 تم إضافة الموظف
        ]);
    }

    /**
     * 5. صرف رصيد مستحق للعميل (سحب نقدي من الفرع)
     */
    public function withdrawBalance($customer, $amount, $notes = null)
    {
        return CustomerTransaction::create([
            'customer_id' => $customer->id,
            'amount'      => $amount,
            'type'        => 'debit', 
            'description' => 'صرف رصيد نقدي للعميل' . ($notes ? ' - ' . $notes : ''),
            'shipment_id' => null, 
            'created_by'  => auth()->id(), // 👈 تم إضافة الموظف
        ]);
    }

    /**
     * 6. تسجيل عمولة للعميل مقابل جلب راكب
     */
    public function recordPassengerCommission($passenger)
    {
        // نتحقق أولاً أن الراكب مرتبط بعميل وأن له عمولة
        if ($passenger->customer_id && $passenger->total_commission > 0) {
            
            return CustomerTransaction::create([
                'customer_id'  => $passenger->customer_id,
                'passenger_id' => $passenger->id, 
                'amount'       => $passenger->total_commission,
                'type'         => 'credit', // رصيد لصالح العميل
                'description'  => "عمولة جلب راكب رقم #{$passenger->passenger_number} - المكان: {$passenger->location}",
                'created_by'   => auth()->id(), // 👈 تم إضافة الموظف
            ]);
        }
        return false;
    }
}