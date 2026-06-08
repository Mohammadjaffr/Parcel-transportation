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
use App\Services\Receipts\Types\ReportPassanger;
use App\Services\Receipts\Types\ExternalOfficeDetection;
use App\Services\Receipts\Types\ReportAllPassanger;
use App\Services\Receipts\Types\ReportTrip;
use App\Services\Receipts\Types\ReportAllTrips;

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
            'passenger' => new ReportPassanger(),
            'all_passenger' => new ReportAllPassanger(),
            'trip' => new ReportTrip(),
            'all_trips' => new ReportAllTrips(),
            default    => throw new Exception("نوع السند غير مدعوم: {$type}")
        };
    }
}