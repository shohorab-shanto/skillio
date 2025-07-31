<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserPreference;

class EnsureUserOnboardingComplete
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('user.onboarding.login');
        }

        $hasPreference = UserPreference::where('user_id', $user->id)->latest()->first();
        if (!$hasPreference) {
            // Redirect to the first onboarding step
            return redirect()->route('user.onboarding.category_service');
        }

        return $next($request);
    }
}
