<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Receipts\ReceiptFactory;
use Spatie\Browsershot\Browsershot;

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
     * تنزيل السند كملف PDF مع دعم كامل للغة العربية باستخدام Browsershot
     */
    public function downloadPdf(Request $request, $type, $uuid)
    {
        try {
            $strategy = ReceiptFactory::make($type);
            $data = $strategy->fetchData($uuid);
            $template = $strategy->getTemplatePath();
            $size = $strategy->sizepage();

            $landscape = false;
            if (is_string($size) && str_contains(strtolower($size), 'landscape')) {
                $landscape = true;
            }

            // إضافة متغير لإخفاء أزرار الطباعة في PDF
            $data['is_pdf'] = true;

            // توليد HTML من القالب
            $html = view($template, $data)->render();

            $fileName = $strategy->getFileName($data);

            // إنشاء PDF باستخدام Browsershot
            $browsershot = Browsershot::html($html)
                ->margins(0, 0, 0, 0)
                ->showBackground()
                ->waitUntilNetworkIdle()
                ->noSandbox();

            if ($landscape) {
                $browsershot->landscape();
            }

            // للورق الحراري أو مقاس A5
            if (is_string($size) && str_contains(strtolower($size), 'a5')) {
                $browsershot->format('A5');
            } else {
                $browsershot->format('A4'); // أو حسب الحجم الافتراضي
            }

            $pdf = $browsershot->pdf();

            return response($pdf)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="' . $fileName . '"');
        } catch (\Exception $e) {
            return response("حدث خطأ: " . $e->getMessage(), 500);
        }
    }
}
