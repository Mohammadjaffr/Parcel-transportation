<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CheckActiveSubscription
{
    public function handle(Request $request, Closure $next)
    {
      if (Auth::check() && Auth::user()->type === 'super_admin') {
            return $next($request);
        }

        // 2. فحص الاشتراكات للمكاتب والشركات العادية
        $app = auth()->user()->App;
        
        if (!$app || !$app->hasActiveSubscription()) {
            
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'اشتراكك منتهي أو غير مفعل. يرجى اختيار باقة لتجديد الاشتراك.',
                    'redirect_to' => '/pricing'
                ], 402); 
            }

            return redirect()->route('pricing.page')->with('error', 'يرجى تجديد باقتك.');
        }

        return $next($request);
    }
}