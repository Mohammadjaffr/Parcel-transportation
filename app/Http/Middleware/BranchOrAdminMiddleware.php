<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BranchOrAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            abort(403, 'Unauthorized access.');
        }

        $user = auth()->user();

        // Check if user is company admin, super admin, or branch admin
        if (!in_array($user->type, ['admin', 'super_admin']) && !$user->is_branch_admin) {
            abort(403, 'Access denied.');
        }

        return $next($request);
    }
}
