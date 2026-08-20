<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (auth()->user()->role !== 'user') {
            return response()->json(['message' => 'Unauthorized. User access required.'], 403);
        }

        if (!auth()->user()->is_active) {
            return response()->json(['message' => 'Account is deactivated. Please contact support.'], 403);
        }

        return $next($request);
    }
}
