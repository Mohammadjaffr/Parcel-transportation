<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Exception;

class BackupController extends Controller
{
   public function uploadBackup()
    {
        set_time_limit(300);

        // 1. جلب المسار وتنظيفه
        $mysqldumpPath = env('DB_DUMP_PATH');
        
        // إزالة أي علامات تنصيص زائدة قد تأتي من الـ env
        $mysqldumpPath = trim($mysqldumpPath, '"');

        // التأكد من إحاطة المسار بعلامات تنصيص مزدوجة للتعامل مع المسافات في الويندوز
        $executable = '"' . $mysqldumpPath . '"';

        $fileName = 'backup-' . date('Y-m-d-H-i-s') . '.sql';
        $tempPath = storage_path('app/backups/' . $fileName);

        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0775, true);
        }

        try {
            $dbUser = env('DB_USERNAME');
            $dbPass = env('DB_PASSWORD');
            $dbName = env('DB_DATABASE');
            $dbHost = env('DB_HOST', '127.0.0.1');

            // بناء الأمر بدقة
            // استخدمنا --password مباشرة مع القيمة المحاطة بـ ""
            $command = "$executable --user=\"$dbUser\" --password=\"$dbPass\" --host=$dbHost --port=3306 --protocol=tcp --column-statistics=0 \"$dbName\" > \"$tempPath\"";

            putenv('SystemRoot=C:\Windows');
            
            $output = [];
            $resultCode = null;
            
            // تنفيذ الأمر مع دمج الخطأ 2>&1 لرؤية تفاصيل الفشل في الأوت بوت إذا لزم الأمر
            exec($command . " 2>&1", $output, $resultCode);

            if ($resultCode !== 0) {
                // في حال الفشل، سنعيد رسالة الخطأ التي أرجعها النظام (السطر الأول من الأوت بوت)
                $systemError = isset($output[0]) ? $output[0] : 'Unknown System Error';
                throw new Exception("فشل النظام: $systemError (Code: $resultCode)");
            }

            if (!file_exists($tempPath) || filesize($tempPath) === 0) {
                 throw new Exception("تم تنفيذ الأمر لكن ملف النسخة الاحتياطية فارغ.");
            }

            // الرفع للسيرفر
            $response = Http::withHeaders([
                'X-App-Secret' => 'a5508400-w29b-a414-d716-446655440000',
                'Accept' => 'application/json',
            ])
            ->timeout(600)
            ->withoutVerifying()
            ->attach('backup_file', fopen($tempPath, 'r'), $fileName)
            ->post('https://besat.tiyar.cc/api/receive-backup', [
                'client_name' => env('CLIENT_NAME', 'unknown_client')
            ]);

            if (file_exists($tempPath)) unlink($tempPath);

            if ($response->successful()) {
                return response()->json(['status' => true, 'message' => 'تم الرفع بنجاح']);
            } else {
                return response()->json(['status' => false, 'message' => 'فشل الرفع للسيرفر البعيد', 'details' => $response->body()], 500);
            }

        } catch (Exception $e) {
            if (file_exists($tempPath)) unlink($tempPath);
            return response()->json(['status' => false, 'message' => 'حدث خطأ', 'error' => $e->getMessage()], 500);
        }
    }
}