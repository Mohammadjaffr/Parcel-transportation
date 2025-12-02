<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyAppAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $appSecret = $request->header('X-App-Secret');
        
        if (!$appSecret || $appSecret !== config('app.mobile_app_secret')) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 401);
        }

        return $next($request);
    }
}
