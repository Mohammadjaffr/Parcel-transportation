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
        $shipment = Shipment::withoutGlobalScopes()->where('id', $id)->orWhere('uuid', $id)->firstOrFail();
        return redirect()->route('receipt.generate', ['type' => 'sender', 'id' => $shipment->uuid ?? $shipment->id]);
    }

    /**
     * Print thermal sticker.
     */
    public function printThermal($id)
    {
        $shipment = Shipment::withoutGlobalScopes()->where('id', $id)->orWhere('uuid', $id)->firstOrFail();
        return redirect()->route('receipt.generate', ['type' => 'thermal', 'id' => $shipment->uuid ?? $shipment->id]);
    }
}
