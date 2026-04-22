<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $validKey = env('ADMIN_API_KEY');
        $providedKey = $request->header('X-Admin-Key');
        if (!$providedKey || $providedKey !== $validKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated: Invalid or missing API Key.'
            ], 401);
        }
        return $next($request);
    }
}