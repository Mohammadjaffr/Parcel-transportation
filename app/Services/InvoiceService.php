<?php

namespace App\Services;

use App\Models\Shipment;
use TCPDF;

class InvoiceService
{
    /**
     * Generate standard A4 invoice PDF.
     */
    public function generateStandardInvoice(Shipment $shipment)
    {
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        $pdf->SetMargins(5, 5, 5);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->setRTL(true);
        $pdf->SetFont('dejavusans', '', 12);

        $html = view('pages.shipment.invoice', compact('shipment'))->render();

        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');

        return $pdf->Output('invoice-' . $shipment->id . '.pdf', 'I');
    }

    /**
     * Generate thermal sticker PDF.
     */
    public function generateThermalSticker(Shipment $shipment)
    {
        $pdf = new \TCPDF('L', 'mm', [70, 100], true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(2, 2, 2);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->setRTL(true);
        $pdf->SetFont('dejavusans', '', 10);

        $pdf->AddPage();

        $html = view('pages.shipment.thermal', compact('shipment'))->render();

        $pdf->writeHTML($html, true, false, true, false, '');

        return $pdf->Output('Sticker-' . $shipment->bond_number . '.pdf', 'I');
    }
}
