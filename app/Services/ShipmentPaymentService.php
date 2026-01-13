<?php

namespace App\Services;

use App\Models\BranchTransaction;
use App\Models\CustomerPayment;
use App\Models\Shipment;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

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

    public function createCodBranchTransactionOnDelivery(Shipment $shipment): void
    {
        $totalPaid = $shipment->customerPayments()->sum('amount');
        $outstanding = max($shipment->total_amount - $totalPaid, 0);

        if ($outstanding <= 0) return;

        BranchTransaction::create([
            'shipment_id'          => $shipment->id,
            'sender_branch_code'   => $shipment->receiver_branch_code,
            'receiver_branch_code' => $shipment->sender_branch_code,
            'amount'               => $outstanding,
            'type'                 => 'cod',
            'description'          => 'تحصيل مبلغ شحنة رقم ' . $shipment->tracking_number,
        ]);
    }
}