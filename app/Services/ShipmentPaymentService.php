<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\CustomerPayment;
use Illuminate\Http\UploadedFile; // <-- استيراد UploadedFile
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ShipmentPaymentService
{
    /**
     * @var ImageService
     */
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function handlePaymentForNewShipment(Shipment $shipment, string $paymentType, ?float $paidAmount = null, ?UploadedFile $attachment = null): void
    {

        if ($paymentType === 'bank_transfer' && is_null($attachment)) {
            throw new InvalidArgumentException('في حالة التحويل البنكي، يجب إرفاق سند الدفع.');
        }

        switch ($shipment->payment_method) {
            case 'prepaid':
                $this->handlePrepaidPayment($shipment, $paymentType, $attachment);
                break;

            case 'partial_payment':
                if (is_null($paidAmount) || $paidAmount <= 0) {
                    throw new InvalidArgumentException('في حالة الدفع الجزئي، يجب إرسال المبلغ المدفوع.');
                }
                $this->handlePartialPayment($shipment, $paidAmount, $paymentType, $attachment);
                break;

            case 'customer_credit':
                $this->handleCustomerCreditPayment($shipment);
                break;

            case 'cod':
                $shipment->customer_debt_status = 'pending';
                $shipment->save();
                break;
        }
    }

    
    private function handlePrepaidPayment(Shipment $shipment, string $paymentType, ?UploadedFile $attachment): void
    {
        DB::transaction(function () use ($shipment, $paymentType, $attachment) {
            $this->createCustomerPaymentRecord(
                $shipment,
                $shipment->sender_customer_id,
                $shipment->sender_branch_code,
                $shipment->total_amount,
                $paymentType,
                $attachment,
                'دفعة مقدمة تلقائية للشحنة رقم ' . $shipment->bond_number
            );

            $shipment->customer_debt_status = 'fully_paid';
            $shipment->save();
        });
    }

   
    private function handlePartialPayment(Shipment $shipment, float $paidAmount, string $paymentType, ?UploadedFile $attachment): void
    {
        if ($paidAmount >= $shipment->total_amount) {
            throw new InvalidArgumentException('المبلغ المدفوع جزئيًا يجب أن يكون أقل من المبلغ الإجمالي.');
        }

        DB::transaction(function () use ($shipment, $paidAmount, $paymentType, $attachment) {
            $this->createCustomerPaymentRecord(
                $shipment,
                $shipment->sender_customer_id,
                $shipment->sender_branch_code,
                $paidAmount,
                $paymentType,
                $attachment,
                'دفعة جزئية تلقائية للشحنة رقم ' . $shipment->bond_number
            );

            $shipment->customer_debt_status = 'partially_paid';
            $shipment->save();
        });
    }

    private function handleCustomerCreditPayment(Shipment $shipment): void
    {
        $shipment->customer_debt_status = 'pending';
        $shipment->save();
    }

    private function createCustomerPaymentRecord(Shipment $shipment, int $customerId, string $branchCode, float $amount, string $paymentType, ?UploadedFile $attachment, string $notes): void
    {
        $paymentData = [
            'shipment_id' => $shipment->id,
            'customer_id' => $customerId,
            'branch_code' => $branchCode,
            'amount' => $amount,
            'payment_method' => $paymentType,
            'payment_date' => now(),
            'notes' => $notes,
            'attachment_path' => null, 
        ];

        
        if ($paymentType === 'bank_transfer' && $attachment) {

            $paymentData['attachment_path'] = $this->imageService->saveImage($attachment, 'payment_attachments');
        }

        CustomerPayment::create($paymentData);
    }
}
