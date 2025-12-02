<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Driver;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DriverMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $Driver = auth('sanctum')->user();
        if ($Driver && $Driver instanceof Driver) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized. Driver access required.'], 403);
    }
}
