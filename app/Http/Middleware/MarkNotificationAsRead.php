<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarkNotificationAsRead
{
    public function handle(Request $request, Closure $next)
    {
        // نتحقق مما إذا كان المستخدم مسجلاً للدخول، والرابط يحتوي على معامل اسمه notify_id
        if (Auth::check() && $request->has('notify_id')) {
            
            // نبحث عن الإشعار الخاص بهذا المستخدم
            $notification = Auth::user()->unreadNotifications()->find($request->query('notify_id'));

            // إذا وجدناه (وهو غير مقروء)، نجعله مقروءاً
            if ($notification) {
                $notification->markAsRead();
            }
        }

        // نكمل مسار الطلب بشكل طبيعي جداً
        return $next($request);
    }
}