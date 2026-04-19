<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Receipts\ReceiptFactory;
use TCPDF;

class ReceiptController extends Controller
{
    public function generate(Request $request, $type, $uuid)

    {
        try {
            $strategy = ReceiptFactory::make($type);
            $data = $strategy->fetchData($uuid);
            $template = $strategy->getTemplatePath();
            $size = $strategy->sizepage();

            // 2. تحويل الـ Blade إلى HTML
            $html = view($template, $data)->render();

            // 3. تهيئة إعدادات الطابعة الحرارية (عرض 80mm، طول ديناميكي نضع له 150mm كمثال)
            $pageFormat = $size; 
            $pdf = new TCPDF('P', 'mm', $pageFormat, true, 'UTF-8', false);

            // إزالة الهيدر والفوتر والهوامش الكبيرة لتناسب الطابعة
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetMargins(3, 5, 3); // هوامش ضيقة 3 ملم يمين ويسار
            $pdf->SetAutoPageBreak(TRUE, 5);

            // إعدادات اللغة العربية
            $pdf->setRTL(true);
            // استخدم خط aealarabiya المدمج مع TCPDF للغة العربية
            $pdf->SetFont('aealarabiya', '', 10); 

            $pdf->AddPage();
            $pdf->writeHTML($html, true, false, true, false, '');

            // 4. إخراج الـ PDF للعرض المباشر (للطباعة فوراً)
            return response($pdf->Output($strategy->getFileName($data), 'I'))
                    ->header('Content-Type', 'application/pdf');

        } catch (\Exception $e) {
            return abort(404, "حدث خطأ: " . $e->getMessage());
        }
    }
}