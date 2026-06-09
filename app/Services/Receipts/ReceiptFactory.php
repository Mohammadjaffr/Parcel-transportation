<?php

namespace App\Services\Receipts;

use App\Interfaces\ReceiptStrategyInterface;
use App\Services\Receipts\Types\SenderShipmentReceipt;
use App\Services\Receipts\Types\ShipmentDetection;
use App\Services\Receipts\Types\DriverDetection;
use App\Services\Receipts\Types\ReceiverShipmentReceipt;
use App\Services\Receipts\Types\ThermalShipmentReceipt;
use App\Services\Receipts\Types\CustomerAccountStatementReceipt;
use App\Services\Receipts\Types\CustomerTransactionReceipt;
use App\Services\Receipts\Types\PassangerDetection;
use App\Services\Receipts\Types\ExternalOfficeDetection;
use App\Services\Receipts\Types\PassangersDetection;
use App\Services\Receipts\Types\TripDetection;
use App\Services\Receipts\Types\TripsDetection;

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
            'ExternalOfficeDetection'     => new ExternalOfficeDetection(),
            'thermal' => new ThermalShipmentReceipt(),
            'CustomerAccountStatementReceipt' => new CustomerAccountStatementReceipt(),
            'transaction' => new CustomerTransactionReceipt(),
            'passenger' => new PassangerDetection(),
            'all_passenger' => new PassangersDetection(),
            'trip' => new TripDetection(),
            'all_trips' => new TripsDetection(),
            default    => throw new Exception("نوع السند غير مدعوم: {$type}")
        };
    }
}