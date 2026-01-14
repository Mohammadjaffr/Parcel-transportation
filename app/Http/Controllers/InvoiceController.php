<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Print standard invoice.
     */
    public function printInvoice($id)
    {
        $shipment = Shipment::findOrFail($id);
        return $this->invoiceService->generateStandardInvoice($shipment);
    }

    /**
     * Print thermal sticker.
     */
    public function printThermal($id)
    {
        $shipment = Shipment::findOrFail($id);
        return $this->invoiceService->generateThermalSticker($shipment);
    }
}
