<?php

namespace App\Services\Receipts;

use App\Interfaces\ReceiptStrategyInterface;
use App\Services\Receipts\Types\ReceiverDeliveryReceipt;
use App\Services\Receipts\Types\SenderShipmentReceipt;
use App\Services\Receipts\Types\ShipmentDetection;
use App\Services\Receipts\Types\DriverDetection;

use Exception;

class ReceiptFactory
{
    public static function make(string $type): ReceiptStrategyInterface
    {
        return match ($type) {
            'sender'   => new SenderShipmentReceipt(),
            'receiver' => new ReceiverDeliveryReceipt(),
            'ShipmentDetection'     => new ShipmentDetection(),
            'DriverDetection'     => new DriverDetection(),
            default    => throw new Exception("نوع السند غير مدعوم: {$type}")
        };
    }
}
