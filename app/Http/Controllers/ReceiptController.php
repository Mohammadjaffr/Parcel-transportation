<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Receipts\ReceiptFactory;

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
}