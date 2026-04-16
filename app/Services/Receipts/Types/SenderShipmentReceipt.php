<?php

namespace App\Services\Receipts\Types;

use App\Models\Shipment;
use App\Interfaces\ReceiptStrategyInterface;

class SenderShipmentReceipt implements ReceiptStrategyInterface
{
    public function fetchData(int $referenceId): array
    {
        $shipment = Shipment::with(['senderCustomer', 'creator'])->findOrFail($referenceId);

        $paymentMethods = [
            'prepaid' => 'مدفوع مقدماً',
            'cod' => 'الدفع عند الاستلام',
            'partial_payment' => 'دفع جزئي',
            'customer_credit' => 'آجل (ذمة)'
        ];

        return [
            'title'          => 'سند استلام طرد',
            'bond_number'    => $shipment->code,
            'date'           => $shipment->created_at->format('Y-m-d h:i A'),
            'customer_name'  => $shipment->senderCustomer->name ?? 'عميل غير مسجل',
            'customer_phone' => $shipment->senderCustomer->phone ?? '-',
            'amount'         => number_format($shipment->total_amount, 2),
            'payment_method' => $paymentMethods[$shipment->payment_method] ?? $shipment->payment_method,
            'package_type'   => $shipment->package_type,
            'creator_name'   => $shipment->creator->name ?? 'النظام',
        ];
    }

    public function getTemplatePath(): string
    {
        return 'receipts.templates.SenderShipmentReceipt';
    }

    public function getFileName(array $data): string
    {
        return 'Sender_Receipt_' . $data['bond_number'] . '.pdf';
    }
}