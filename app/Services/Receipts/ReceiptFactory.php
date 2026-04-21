<?php

namespace App\Services\Receipts;

use App\Interfaces\ReceiptStrategyInterface;
use App\Services\Receipts\Types\SenderShipmentReceipt;
use App\Services\Receipts\Types\ShipmentDetection;
use App\Services\Receipts\Types\DriverDetection;
use App\Services\Receipts\Types\ReceiverShipmentReceipt;
use App\Services\Receipts\Types\ThermalShipmentReceipt;
use App\Services\Receipts\Types\CustomerAccountStatementReceipt;
use Exception;

class ReceiptFactory
{
    public static function make(string $type): ReceiptStrategyInterface
    {
        return match ($type) {
            'sender'   => new SenderShipmentReceipt(),
            'receiver' => new ReceiverShipmentReceipt(),
            'ShipmentDetection'     => new ShipmentDetection(),
            'DriverDetection'     => new DriverDetection(),
            'thermal' => new ThermalShipmentReceipt(),
            'CustomerAccountStatementReceipt' => new CustomerAccountStatementReceipt(),
            default    => throw new Exception("نوع السند غير مدعوم: {$type}")
        };
    }
}
