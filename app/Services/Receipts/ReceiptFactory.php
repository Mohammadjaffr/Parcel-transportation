<?php

namespace App\Services\Receipts;

use App\Interfaces\ReceiptStrategyInterface;
use App\Services\Receipts\Types\ReceiverDeliveryReceipt;
use App\Services\Receipts\Types\SenderShipmentReceipt;
use Exception;

class ReceiptFactory
{
    public static function make(string $type): ReceiptStrategyInterface
    {
        return match ($type) {
            'sender'   => new SenderShipmentReceipt(),
            'receiver' => new ReceiverDeliveryReceipt(),
            default    => throw new Exception("نوع السند غير مدعوم: {$type}")
        };
    }
}