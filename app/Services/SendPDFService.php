<?php

namespace App\Services;

use App\Services\SavePDFService;

class SendPDFService
{
    public function generateAndUploadManifest($entity, $viewName, $variableName = 'package', $orientation = 'L')
    {
        // 1. إعدادات TCPDF الموحدة
        $pdf = new \TCPDF($orientation, 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->setRTL(true);
        $pdf->SetFont('dejavusans', '', 10);

        // 2. جلب المحتوى من الـ View المحدد
        $html = view($viewName, [$variableName => $entity])->render();

        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdfContent = $pdf->Output('ملف-' . ($entity->tracking_number ?? time()) . '.pdf', 'S');

        // 4. إرسال المحتوى للخدمة واسترجاع الرابط
        // تأكد من استدعاء الكلاس بشكل صحيح
        return SavePDFService::generateAndSendPdf($pdfContent);
    }
}
