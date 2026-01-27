<?php


namespace App\Services;

use Illuminate\Support\Facades\Http;


class SavePDFService
{
  public static function generateAndSendPdf($pdf)
    {
        $pdfContent = $pdf;

        $response = Http::withHeaders([
            'X-App-Secret' => 'a5508400-w29b-a414-d716-446655440000',
        ])
        ->attach('file', $pdfContent, 'document.pdf')
        ->post('https://besat.tiyar.cc/api/upload-pdf');

        if ($response->successful()) {
            return $response->json()['url'];
        }
        return 'Error: ' . $response->body();
    }
}