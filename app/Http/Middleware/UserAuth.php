<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role == 'user') {
            return $next($request);
        }
        
        // Store the intended URL before redirecting to login
        $request->session()->put('url.intended', $request->fullUrl());
        
        return redirect()->route('user.onboarding.login')->with('error', 'Please log in to continue.');
    }
}
