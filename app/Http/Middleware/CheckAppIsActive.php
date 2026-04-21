<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAppIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. التأكد أن المستخدم مسجل دخول أصلاً
        if (auth()->check()) {
            
            // 2. جلب الشركة/التطبيق الخاص بالمستخدم
            $app = auth()->user()->App; 

            // 3. إذا كان الحساب غير مفعل (false)
            if ($app && !$app->is_active) {
                
                // التأكد أننا لسنا في صفحة الانتظار بالفعل (لمنع حلقة إعادة التوجيه اللانهائية)
                if (!$request->routeIs('account.pending') && !$request->routeIs('logout')) {
                    return redirect()->route('account.pending');
                }
            }
        }

        return $next($request);
    }
}