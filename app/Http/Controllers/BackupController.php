<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Spatie\DbDumper\Databases\MySql;
use Exception;

class BackupController extends Controller
{
    public function uploadBackup()
    {
        set_time_limit(300); 

        // 1. تحديد المسار
        $fileName = 'backup-' . date('Y-m-d-H-i-s') . '.sql';
        $tempPath = storage_path('app/backups/' . $fileName);

        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0775, true);
        }

        try {
            // 2. إنشاء النسخة (Dump)
            $dumper = MySql::create()
                ->setDbName(env('DB_DATABASE'))
                ->setUserName(env('DB_USERNAME'))
                ->setPassword(env('DB_PASSWORD'))
                ->setHost(env('DB_HOST'));

            // ✅ تصحيح 1: تفعيل المسار لـ Laragon (تأكد من المسار في جهازك)
            // هذا السطر ضروري جداً للوكل (Windows)
            $dumper->setDumpBinaryPath('C:/laragon/bin/mysql/mysql-8.0.30-winx64/bin'); 
            
            $dumper->dumpToFile($tempPath);

            // 3. الرفع للسيرفر
            $response = Http::withHeaders([
                'X-App-Secret' => 'a5508400-w29b-a414-d716-446655440000',
                'Accept' => 'application/json',
            ])
            ->timeout(60)
            ->connectTimeout(30)
            ->withoutVerifying() // جيد للوكل، لكن حاول استخدام SSL صحيح في الإنتاج
            ->attach('backup_file', file_get_contents($tempPath), $fileName)
            ->post('https://besat.tiyar.cc/api/receive-backup'); // تأكد أن هذا هو رابط الباك اب وليس PDF

            // ✅ تصحيح 2: حذف الملف أولاً قبل عمل Return
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

            // 4. إرجاع النتيجة
            if ($response->successful()) {
                // يمكنك إرجاع الرابط أو رسالة نجاح حسب تصميم الـ API المقابل
                return response()->json([
                    'status' => true,
                    'message' => 'تم الرفع بنجاح',
                    'data' => $response->json()
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'فشل الرفع للسيرفر البعيد',
                    'details' => $response->body()
                ], 500);
            }

        } catch (Exception $e) {
            // تنظيف الملف أيضاً في حال حدوث خطأ أثناء الرفع
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
            
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ في النظام',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}