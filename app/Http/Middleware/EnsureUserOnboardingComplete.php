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
        }else{
            if ($hasPreference->category_id == null) {
                return redirect()->route('user.onboarding.category_service');
            }
            if ($hasPreference->education_type == 'online') {
                if($hasPreference->wants_courses == null && $hasPreference->wants_mentoring == null) {
                    return redirect()->route('user.onboarding.online_education');
                }
            }
            elseif ($hasPreference->education_type == 'in-person') {
                if($hasPreference->country == null && $hasPreference->area == null){
                    return redirect()->route('user.onboarding.in_person_education_location');
                }
            }else{
                return redirect()->route('user.onboarding.in_person_or_online');
            }
            
        }

        return $next($request);
    }
}
