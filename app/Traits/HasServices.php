<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait HasServices
{
    /**
     * التحقق مما إذا كانت الخدمة مفعلة للتطبيق الحالي
     */
    public function hasService(string $serviceSlug): bool
    {
        // 1. جلب الخدمات المفعلة من الكاش (أو الداتابيز إذا لم تكن موجودة)
        $activeServices = Cache::rememberForever("app_{$this->id}_active_services", function () {
            return $this->services()
                        ->where('is_global_active', true) // يجب أن تكون مفعلة عالمياً أولاً
                        ->wherePivot('is_active', true)   // ومفعلة لهذا التطبيق تحديداً
                        ->pluck('slug')
                        ->toArray();
        });

        // 2. التحقق هل الـ Slug المطلوب موجود ضمن الخدمات المفعلة؟
        return in_array($serviceSlug, $activeServices);
    }

    /**
     * دالة لتنظيف الكاش عند إعطاء أو سحب صلاحية من لوحة تحكم الإدارة
     */
    public function flushServicesCache(): void
    {
        Cache::forget("app_{$this->id}_active_services");
    }
}