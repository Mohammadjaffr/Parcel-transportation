<?php


namespace App\Services;

use Illuminate\Support\Facades\Http;


class SavePDFService
{
    public static function generateAndSendPdf($pdf)
    {
        $pdfContent = $pdf;

        try {
            $response = Http::withHeaders([
                'X-App-Secret' => 'a5508400-w29b-a414-d716-446655440000',
            ])
                ->timeout(60)
                ->connectTimeout(30)
                ->withoutVerifying()
                ->attach('file', $pdfContent, 'document.pdf')
                ->post('https://besat.tiyar.cc/api/upload-pdf');

            if ($response->successful()) {
                return $response->json()['url'];
            }

            \Illuminate\Support\Facades\Log::error('PDF Upload Failed: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('PDF Upload Exception: ' . $e->getMessage());
            return null;
        }
    }
}
