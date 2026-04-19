<?php

namespace App\Services\Receipts\Types;

use App\Models\Shipment;
use App\Interfaces\ReceiptStrategyInterface;

class ReceiverDeliveryReceipt implements ReceiptStrategyInterface
{
    public function sizepage(): string|array
    {
        return array(80, 150); // Default size
    }
    public function fetchData(string $referenceId): array
    {
        $shipment = Shipment::with(['receiverCustomer', 'creator'])->where('uuid', $referenceId)->firstOrFail();

        $amountDue = $shipment->payment_method === 'cod' ? $shipment->total_amount : 0;

        return [
            'title'          => 'سند تسليم طرد',
            'bond_number'    => $shipment->code,
            'date'           => now()->format('Y-m-d h:i A'),
            'customer_name'  => $shipment->receiverCustomer->name ?? 'عميل غير مسجل',
            'customer_phone' => $shipment->receiverCustomer->phone ?? '-',
            'amount'         => number_format($amountDue, 2) . ' (المطلوب سداده)',
            'payment_method' => $amountDue > 0 ? 'الدفع عند الاستلام' : 'خالص (مدفوع)',
            'package_type'   => $shipment->package_type,
            'creator_name'   => auth()->user()->name ?? 'موظف التسليم',
        ];
    }

    public function getTemplatePath(): string
    {
        return 'receipts.templates.SenderShipmentReceipt';
    }

    public function getFileName(array $data): string
    {
        return 'Receiver_Receipt_' . $data['bond_number'] . '.pdf';
    }
}