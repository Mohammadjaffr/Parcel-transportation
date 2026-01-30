<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\Transaction;
use App\Models\BranchLedger;
use InvalidArgumentException;
use App\Models\CustomerPayment;
use App\Models\BranchTransaction;
use Illuminate\Support\Facades\DB;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;

class ShipmentPaymentService
{
    /**
     * معالجة الدفع عند إنشاء شحنة جديدة
     */
    public function handlePaymentForNewShipment(
        Shipment $shipment,
        string $paymentType,
        ?float $paidAmount = null,
        ?string $referenceNumber = null
    ): void {

        // ✅ إذا تحويل بنكي => لازم رقم الإيداع فقط
        if ($paymentType === 'bank_transfer' && empty($referenceNumber)) {
            throw new InvalidArgumentException('في حالة التحويل البنكي، يجب إدخال رقم الإيداع.');
        }

        switch ($shipment->payment_method) {

            case 'prepaid':
                $this->handlePrepaidPayment($shipment, $paymentType, $referenceNumber);
                break;

            case 'partial_payment':
                if (is_null($paidAmount) || $paidAmount <= 0) {
                    throw new InvalidArgumentException('في حالة الدفع الجزئي، يجب إرسال المبلغ المدفوع.');
                }
                $this->handlePartialPayment($shipment, $paidAmount, $paymentType, $referenceNumber);
                break;

            case 'customer_credit':
                $this->handleCustomerCreditPayment($shipment);
                break;

            case 'cod':
                $shipment->customer_debt_status = 'pending';
                $shipment->save();

                // عند التسليم فقط يتم إنشاء حركة الفروع (إذا كان عندك منطق التسليم هنا)
                if ($shipment->status === 'delivered') {
                    BranchTransaction::create([
                        'shipment_id'          => $shipment->id,
                        'sender_branch_code'   => $shipment->receiver_branch_code, // الفرع الذي استلم المبلغ
                        'receiver_branch_code' => $shipment->sender_branch_code,   // الفرع صاحب الشحنة
                        'amount'               => $shipment->total_amount,
                        'type'                 => 'cod',
                        'description'          => 'تحصيل مبلغ شحنة رقم ' . $shipment->tracking_number,
                    ]);
                }
                break;
        }
    }

    private function handlePrepaidPayment(
        Shipment $shipment,
        string $paymentType,
        ?string $referenceNumber = null
    ): void {
        DB::transaction(function () use ($shipment, $paymentType, $referenceNumber) {

            $this->createCustomerPaymentRecord(
                $shipment,
                $shipment->sender_customer_id,
                $shipment->sender_branch_code,
                $shipment->total_amount,
                $paymentType,
                'دفعة مقدمة تلقائية للشحنة رقم ' . $shipment->bond_number,
                $referenceNumber
            );
            TransactionService::recordShipmentPayment(
                $shipment,
                $shipment->total_amount,
                Auth::user()->branch_code, 
                $paymentType,              
                $referenceNumber,
                $shipment->sender_customer_id
            );

            $shipment->customer_debt_status = 'fully_paid';
            $shipment->save();
        });
    }

    private function handlePartialPayment(
        Shipment $shipment,
        float $paidAmount,
        string $paymentType,
        ?string $referenceNumber = null
    ): void {

        if ($paidAmount >= $shipment->total_amount) {
            throw new InvalidArgumentException('المبلغ المدفوع جزئيًا يجب أن يكون أقل من المبلغ الإجمالي.');
        }

        DB::transaction(function () use ($shipment, $paidAmount, $paymentType, $referenceNumber) {

            $this->createCustomerPaymentRecord(
                $shipment,
                $shipment->sender_customer_id,
                $shipment->sender_branch_code,
                $paidAmount,
                $paymentType,
                'دفعة جزئية تلقائية للشحنة رقم ' . $shipment->bond_number,
                $referenceNumber
            );
            TransactionService::recordShipmentPayment(
                $shipment,
                $paidAmount,               
                Auth::user()->branch_code,
                $paymentType,
                $referenceNumber,
                $shipment->sender_customer_id
            );

            $shipment->customer_debt_status = 'partially_paid';
            $shipment->save();
        });
    }

    private function handleCustomerCreditPayment(Shipment $shipment): void
    {
        $shipment->customer_debt_status = 'pending';
        $shipment->save();

        \App\Models\CustomerTransaction::create([
            'customer_id'  => $shipment->sender_customer_id,
            'shipment_id'  => $shipment->id,
            'amount'       => $shipment->total_amount,
            'type'         => 'debit',
            'description'  => 'رسوم شحنة رقم ' . $shipment->tracking_number,
        ]);
    }

    private function createCustomerPaymentRecord(
        Shipment $shipment,
        int $customerId,
        string $branchCode,
        float $amount,
        string $paymentType,
        string $notes,
        ?string $referenceNumber = null
    ): void {

        //  إذا تحويل بنكي => لازم رقم الإيداع
        if ($paymentType === 'bank_transfer' && empty($referenceNumber)) {
            throw new InvalidArgumentException('في حالة التحويل البنكي، يجب إدخال رقم الإيداع.');
        }

        CustomerPayment::updateOrCreate(
            ['shipment_id' => $shipment->id],
            [
                'customer_id'      => $customerId,
                'branch_code'      => $branchCode,
                'amount'           => $amount,
                'payment_method'   => $paymentType,
                'payment_date'     => now(),
                'notes'            => $notes,
                'reference_number' => $referenceNumber,
            ]
        );
    }

    /**
     * Create COD/Partial branch transaction on delivery.
     * Records cash in receiver's box AND creates double-entry ledger.
     */
    public function createCodBranchTransactionOnDelivery(Shipment $shipment): void
    {
        // Guard 1: Must be delivered
        if ($shipment->status !== 'delivered') {
            return;
        }

        // Guard 2: Must be COD or Partial Payment
        if (!in_array($shipment->payment_method, ['cod', 'partial_payment'])) {
            return;
        }

        // Guard 3: Idempotency - prevent duplicate ledger entries
        if (BranchLedger::where('shipment_id', $shipment->id)->exists()) {
            return;
        }

        // Calculate collected amount (what receiver branch physically collected)
        $totalPaid = $shipment->customerPayments()->sum('amount');
        $collectedAmount = (float) max($shipment->total_amount - $totalPaid, 0);

        // Guard 4: Nothing to collect
        if ($collectedAmount <= 0) {
            return;
        }

        DB::transaction(function () use ($shipment, $collectedAmount) {
            // A. Record cash entering the Receiver Branch's physical box
            // TransactionService::recordShipmentPayment(
            //     shipment: $shipment,
            //     amount: $collectedAmount,
            //     branchCode: $shipment->receiver_branch_code,
            //     paymentMethod: 'cash',
            //     referenceNumber: null,
            //     customerId: $shipment->receiver_customer_id
            // );

            // B. Create double-entry ledger records
            // Receiver Branch: DEBIT (they OWE this money to sender)
            BranchLedger::create([
                'branch_code' => $shipment->receiver_branch_code,
                'related_branch_code' => $shipment->sender_branch_code,
                'shipment_id' => $shipment->id,
                'type' => 'shipment_cod',
                'debit' => $collectedAmount,
                'credit' => 0,
                'description' => "تحصيل مبلغ شحنة COD رقم {$shipment->bond_number}",
            ]);

            // Sender Branch: CREDIT (they ARE OWED this money from receiver)
            BranchLedger::create([
                'branch_code' => $shipment->sender_branch_code,
                'related_branch_code' => $shipment->receiver_branch_code,
                'shipment_id' => $shipment->id,
                'type' => 'shipment_cod',
                'debit' => 0,
                'credit' => $collectedAmount,
                'description' => "استحقاق من فرع {$shipment->receiver_branch_code} لشحنة رقم {$shipment->bond_number}",
            ]);
        });
    }

   public function voidShipmentTransactions(Shipment $shipment): void
    {
        // 1. حذف المدفوعات من سجل العميل
        CustomerPayment::where('shipment_id', $shipment->id)->delete();

        // 2. حذف المعاملات من الصندوق
        // ✅ التعديل هنا: نستخدم whereHas للبحث داخل جدول التصنيفات المرتبط
        Transaction::where('shipment_id', $shipment->id)
            ->whereHas('category', function($q) {
                $q->where('type', 'in'); // البحث داخل جدول transaction_categories
            })
            ->delete();

        // 3. تصفير الشحنة
        $shipment->customer_debt_status = null;
        $shipment->partial_amount = null;
        $shipment->save();
    }
}