<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Support\Facades\Log;

class BackupController extends Controller
{
    public function uploadBackup()
    {
        set_time_limit(300);

        // 1. تنظيف وتجهيز مسار mysqldump
        // لاحظ: وضعنا قيمة افتراضية للمسار في حال لم يقرأها من الـ env
        $mysqldumpPath = env('DB_DUMP_PATH', 'C:/xampp/mysql/bin/mysqldump.exe');
        $mysqldumpPath = str_replace(['"', "'"], '', $mysqldumpPath);

        if (!file_exists($mysqldumpPath)) {
            return response()->json(['status' => false, 'message' => "ملف mysqldump غير موجود في المسار: $mysqldumpPath"], 500);
        }

        // 2. تجهيز بيانات الاتصال مع قيم افتراضية لـ XAMPP
        // إذا لم يجد DB_USERNAME سيستخدم root
        $dbUser = env('DB_USERNAME', 'root');
        $dbPass = env('DB_PASSWORD', '');
        $dbName = env('DB_DATABASE', 'laravel');
        $dbHost = env('DB_HOST', '127.0.0.1');

        // تحقق هام: لا يمكن عمل نسخة بدون اسم قاعدة بيانات
        if (empty($dbName)) {
            return response()->json(['status' => false, 'message' => "اسم قاعدة البيانات غير موجود في ملف .env"], 500);
        }

        // 3. تجهيز مسار الملف الناتج
        $fileName = 'backup-' . date('Y-m-d-H-i-s') . '.sql';
        $storageDir = storage_path('app/backups');
        $tempPath = $storageDir . '/' . $fileName;

        if (!file_exists($storageDir)) {
            mkdir($storageDir, 0775, true);
        }

        try {
            // تجهيز علم كلمة المرور
            $passwordFlag = '';
            if (!empty($dbPass)) {
                $passwordFlag = "--password=\"$dbPass\"";
            }

            // تحويل مسار الملف الناتج لصيغة ويندوز
            $windowsTempPath = str_replace('/', '\\', $tempPath);

            // بناء الأمر
            // ملاحظة: --user=\"$dbUser\" الآن لن يكون فارغاً أبداً بفضل القيمة الافتراضية
            $command = "\"$mysqldumpPath\" --user=\"$dbUser\" $passwordFlag --host=$dbHost --port=3306 --result-file=\"$windowsTempPath\" \"$dbName\"";

            Log::info("Backup Command Running: " . $command);

            $output = [];
            $resultCode = null;

            // تنفيذ الأمر
            exec($command . " 2>&1", $output, $resultCode);

            if ($resultCode !== 0) {
                $fullError = implode("\n", $output);
                throw new Exception("خطأ mysqldump (Code: $resultCode).\nالسبب: $fullError");
            }

            if (!file_exists($tempPath) || filesize($tempPath) === 0) {
                throw new Exception("تم إنشاء الملف لكنه فارغ.");
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

            // التنظيف
            if (file_exists($tempPath)) {
                @fclose(fopen($tempPath, 'r'));
                @unlink($tempPath);
            }

            if ($response->successful()) {
                return response()->json(['status' => true, 'message' => 'تم الرفع بنجاح']);
            } else {
                return response()->json(['status' => false, 'message' => 'فشل الرفع للسيرفر البعيد', 'details' => $response->body()], 500);
            }
        } catch (Exception $e) {
            if (isset($tempPath) && file_exists($tempPath)) {
                @unlink($tempPath);
            }
            return response()->json(['status' => false, 'message' => 'حدث خطأ', 'error' => $e->getMessage()], 500);
        }
    }
}
