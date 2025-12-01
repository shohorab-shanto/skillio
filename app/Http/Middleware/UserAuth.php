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
        // Check if user is authenticated
        if (auth()->check()) {
            // Check if user has correct role
            if (auth()->user()->role == 'user') {
                return $next($request);
            }
            
            // User is logged in but not a 'user' role - redirect to their dashboard
            if (auth()->user()->role == 'admin') {
                return redirect()->route('admin.dashboard')->with('error', 'Access denied. Admin accounts cannot book sessions.');
            } elseif (auth()->user()->role == 'mentor') {
                return redirect()->route('mentor.dashboard')->with('error', 'Access denied. Mentors cannot book their own sessions.');
            }
        }
        
        // User is not authenticated - store intended URL and redirect to login
        $request->session()->put('url.intended', $request->fullUrl());
        
        return redirect()->route('user.onboarding.login')->with('error', 'Please log in to continue.');
    }
}
