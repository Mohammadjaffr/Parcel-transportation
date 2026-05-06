<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckServiceAccess
{
    public function handle(Request $request, Closure $next, string $serviceSlug)
    {
        // افترض أنك تجلب التطبيق الحالي (مثلاً من الـ Auth أو الدومين)
        $currentApp = auth()->user()->app; // عدل هذا السطر حسب منطق نظامك

        if (! $currentApp || ! $currentApp->hasService($serviceSlug)) {
            abort(403, 'عفواً، هذه الخدمة غير مفعلة لتطبيقك. يرجى التواصل مع الإدارة.');
        }

        return $next($request);
    }
}