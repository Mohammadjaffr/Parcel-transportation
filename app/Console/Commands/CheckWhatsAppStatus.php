<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Models\Branch;
use App\Services\WhatsAppService;

class CheckWhatsAppStatus extends Command
{
    protected $signature = 'whatsapp:check-status';
    protected $description = 'فحص حالة اتصال واتساب لكل فرع وحفظها في الكاش عبر WhatsAppService';

    public function handle(WhatsAppService $whatsAppService)
    {
        // جلب جميع الفروع التي تمتلك مفاتيح واتساب
        $branches = Branch::whereNotNull('whatsapp_api_key')
            ->where('whatsapp_api_key', '!=', '')
            ->get();

        if ($branches->isEmpty()) {
            $this->warn('لا توجد فروع مهيأة بإعدادات واتساب.');
            return Command::SUCCESS;
        }

        foreach ($branches as $branch) {
            $cacheKey = 'whatsapp_status_branch_' . $branch->id;

            try {
                // استخدام السيرفيس المعزولة الخاصة بالفرع مع timeout سريع للفحص
                $service = $whatsAppService->forBranch($branch)->withConfig(['timeout' => 4]);

                $result = $service->getStatus();

                // 1. إذا كان الرد يحمل خطأ صريحاً
                if (isset($result['error']) && $result['error'] === true) {
                    Cache::put($cacheKey, 'offline', now()->addMinutes(15));
                    $errorMsg = $result['message'] ?? 'Connection error';
                    $this->warn("Branch [{$branch->name} - ID: {$branch->id}]: WhatsApp is OFFLINE ❌ ({$errorMsg})");
                    continue;
                }

                // 2. استخراج حالة الاتصال من كافة الحقول المحتملة
                $state = data_get($result, 'instance.state') 
                    ?? data_get($result, 'state') 
                    ?? data_get($result, 'connection') 
                    ?? data_get($result, 'status') 
                    ?? data_get($result, 'message') 
                    ?? '';

                $stateLower = strtolower(trim((string) $state));

                // 3. الحالات المعتمدة التي تؤكد أن الخدمة متصلة وتعمل
                $onlineStates = ['open', 'connected', 'connecting', 'success', 'online', '200'];

                $isOnline = in_array($stateLower, $onlineStates, true) 
                    || data_get($result, 'status') === 200;

                // 4. حفظ النتيجة في الكاش
                if ($isOnline) {
                    Cache::put($cacheKey, 'online', now()->addMinutes(15));
                    $this->info("Branch [{$branch->name} - ID: {$branch->id}]: WhatsApp is ONLINE ✅ (State: {$state})");
                } else {
                    Cache::put($cacheKey, 'offline', now()->addMinutes(15));
                    $this->warn("Branch [{$branch->name} - ID: {$branch->id}]: WhatsApp is OFFLINE ❌ (State: {$state})");
                }

            } catch (\Exception $e) {
                Cache::put($cacheKey, 'offline', now()->addMinutes(15));
                $this->error("Branch [{$branch->name} - ID: {$branch->id}]: WhatsApp is OFFLINE ❌ ({$e->getMessage()})");
            }
        }

        return Command::SUCCESS;
    }
}