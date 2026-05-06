<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckServiceAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $serviceSlug): Response
    {
        // التحقق مما إذا كان المستخدم مسجلاً ولديه مكتب (App)
        if (auth()->check() && auth()->user()->App) {
            
            // استدعاء دالة hasService من موديل App للتحقق من تفعيل الميزة
            if (!auth()->user()->App->hasService($serviceSlug)) {
                
                // إذا لم تكن مفعلة، قم بإيقاف العملية وعرض رسالة الخطأ 403
                abort(403, 'هذه الميزة غير مفعلة في باقة مكتبك الحالية. يرجى التواصل مع الإدارة المركزية.');
            }
        }

        // إذا كانت الميزة مفعلة، اسمح للمستخدم بالمرور
        return $next($request);
    }
}