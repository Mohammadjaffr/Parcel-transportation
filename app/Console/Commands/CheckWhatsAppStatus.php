<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Models\App;
use App\Services\WhatsAppService;

class CheckWhatsAppStatus extends Command
{
    protected $signature = 'whatsapp:check-status';
    protected $description = 'فحص حالة اتصال واتساب لكل تطبيق وحفظها في الكاش عبر WhatsAppService';

    public function handle(WhatsAppService $whatsAppService)
    {
        // جلب جميع التطبيقات التي تمتلك مفاتيح واتساب
        $apps = App::whereNotNull('whatsapp_api_key')
            ->where('whatsapp_api_key', '!=', '')
            ->get();

        if ($apps->isEmpty()) {
            $this->warn('لا توجد تطبيقات مهيأة بإعدادات واتساب.');
            return Command::SUCCESS;
        }

        foreach ($apps as $app) {
            $cacheKey = 'whatsapp_status_' . $app->id;

            try {
                // استخدام السيرفيس المعزولة الخاصة بالتطبيق مع تقليل الـ timeout لـ 4 ثوان للفحص
                $service = $whatsAppService->forApp($app)->withConfig(['timeout' => 4]);

                $result = $service->getStatus();

                // التحقق من الاستجابة وحالة الاتصال
                $isOnline = false;

                if (!isset($result['error']) || !$result['error']) {
                    // التحقق من الحالات المختلفة الشائعة في Evolution API
                    $state = data_get($result, 'instance.state') 
                        ?? data_get($result, 'state') 
                        ?? data_get($result, 'status');

                    if (in_array(strtolower((string) $state), ['open', 'connected', 'connecting'])) {
                        $isOnline = true;
                    } elseif (isset($result['status']) && $result['status'] === 200) {
                        $isOnline = true;
                    }
                }

                if ($isOnline) {
                    Cache::put($cacheKey, 'online', now()->addMinutes(15));
                    $this->info("App [{$app->name} - ID: {$app->id}]: WhatsApp is ONLINE ✅");
                } else {
                    Cache::put($cacheKey, 'offline', now()->addMinutes(15));
                    $errorMsg = $result['message'] ?? 'Not connected';
                    $this->warn("App [{$app->name} - ID: {$app->id}]: WhatsApp is OFFLINE ❌ ({$errorMsg})");
                }
            } catch (\Exception $e) {
                Cache::put($cacheKey, 'offline', now()->addMinutes(15));
                $this->error("App [{$app->name} - ID: {$app->id}]: WhatsApp is OFFLINE ❌ ({$e->getMessage()})");
            }
        }

        return Command::SUCCESS;
    }
}