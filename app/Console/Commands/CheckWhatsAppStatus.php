<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Models\App;

class CheckWhatsAppStatus extends Command
{
    protected $signature = 'whatsapp:check-status';
    protected $description = 'فحص حالة اتصال واتساب لكل تطبيق وحفظها في الكاش';

    public function handle()
    {
        // جلب التطبيقات المفعلة والتي تمتلك مفاتيح واتساب
        $apps = App::where('is_active', true)
            ->whereNotNull('whatsapp_base_url')
            ->whereNotNull('whatsapp_api_key')
            ->get();

        foreach ($apps as $app) {
            $cacheKey = 'whatsapp_status_' . $app->id;
            
            try {
                $baseUrl = rtrim($app->whatsapp_base_url, '/');
                $instance = $app->whatsapp_instance_id ?? $app->whatsapp_instance_name;

                // رابط فحص الحالة (قم بتعديل المسار حسب مزود خدمة الواتساب لديك، مثلاً /instance/status أو /sessions/status)
                $endpoint = $instance 
                    ? "{$baseUrl}/instance/status/{$instance}" 
                    : "{$baseUrl}/status";

                $response = Http::timeout(3)
                    ->connectTimeout(2)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $app->whatsapp_api_key,
                        'apikey'        => $app->whatsapp_api_key,
                    ])
                    ->get($endpoint);

                // فحص هل الرد سليم والخدمة متصلة (Connected)
                if ($response->successful()) {
                    Cache::put($cacheKey, 'online', now()->addMinutes(15));
                    $this->info("App [{$app->name} - ID: {$app->id}]: WhatsApp is ONLINE");
                } else {
                    Cache::put($cacheKey, 'offline', now()->addMinutes(15));
                    $this->warn("App [{$app->name} - ID: {$app->id}]: WhatsApp is OFFLINE (HTTP {$response->status()})");
                }
            } catch (\Exception $e) {
                // في حال عدم الاستجابة أو انتهاء وقت الاتصال Timeout
                Cache::put($cacheKey, 'offline', now()->addMinutes(15));
                $this->error("App [{$app->name} - ID: {$app->id}]: WhatsApp is UNREACHABLE ({$e->getMessage()})");
            }
        }

        return Command::SUCCESS;
    }
}