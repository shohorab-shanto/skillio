<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
                'errors' => ['auth' => 'Authentication required']
            ], 401);
        }

        // Set the authenticated user for the request
        $user = Auth::guard('sanctum')->user();
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}
