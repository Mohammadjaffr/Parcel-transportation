<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToApp
{
    protected static function bootBelongsToApp()
    {
        static::addGlobalScope('app_id', function (Builder $builder) {
            
            // نتحقق إذا كان المستخدم مسجلاً للدخول ولديه app_id
            if (auth()->check() && auth()->user()->app_id) {
                
                // إذا كان المدير الرئيسي (super_admin)، نوقف الفلتر لكي يرى جميع الشركات
                if (auth()->user()->type === 'super_admin') {
                    return; 
                }

                // تطبيق الفلتر لباقي المستخدمين ليظهر لهم فقط ما يخص شركتهم
                $builder->where($builder->getModel()->getTable() . '.app_id', auth()->user()->app_id);
            }
        });
    }

    public function app()
    {
        return $this->belongsTo(\App\Models\App::class, 'app_id');
    }
}