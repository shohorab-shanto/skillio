<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MentorAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and has 'mentor' role in the 'role' column
        if (auth()->check() && auth()->user()->role == 'mentor') {
            return $next($request);
        }

        // Redirect to login or show unauthorized
        return redirect()->route('user.onboarding.login')->with('error', 'Unauthorized access.');
    }
}
