<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Receipts\ReceiptFactory;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    public function generate(Request $request, $type, $uuid)

    {
        try {
            $strategy = ReceiptFactory::make($type);
            $data = $strategy->fetchData($uuid);
            $template = $strategy->getTemplatePath();
            $size = $strategy->sizepage();

            // 2. إرجاع الـ Blade كعرض (HTML) للطباعة عبر المتصفح (Web Print)
            return view($template, $data);

        } catch (\Exception $e) {
           return response("حدث خطأ: " . $e->getMessage(), 404);
        }
    }

    /**
     * تنزيل السند كملف PDF مع دعم كامل للغة العربية
     */
    public function downloadPdf(Request $request, $type, $uuid)
    {
        try {
            $strategy = ReceiptFactory::make($type);
            $data = $strategy->fetchData($uuid);
            $template = $strategy->getTemplatePath();
            $size = $strategy->sizepage();

            // تحديد اتجاه الصفحة
            $orientation = 'portrait';
            if (is_string($size) && str_contains(strtolower($size), 'landscape')) {
                $orientation = 'landscape';
            }

            // إضافة متغير لإخفاء أزرار الطباعة في PDF
            $data['is_pdf'] = true;

            // توليد HTML من القالب
            $html = view($template, $data)->render();

            // إنشاء PDF باستخدام DomPDF
            $pdf = Pdf::loadHTML($html)
                ->setPaper('a4', $orientation)
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', true)
                ->setOption('defaultFont', 'DejaVu Sans');

            $fileName = $strategy->getFileName($data);

            // التحقق من طلب المشاركة (إرجاع الملف كـ inline) أو التنزيل
            if ($request->has('inline')) {
                return $pdf->stream($fileName);
            }

            return $pdf->download($fileName);

        } catch (\Exception $e) {
            return response("حدث خطأ: " . $e->getMessage(), 500);
        }
    }
}