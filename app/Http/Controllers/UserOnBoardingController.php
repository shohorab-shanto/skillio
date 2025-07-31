<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Helpers\CountryList;
use Illuminate\Http\Request;
use App\Models\UserPreference;
use Illuminate\Support\Facades\Auth;


class UserOnBoardingController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function login()
    {
        return view('frontend.user.on-boarding.login');
    }
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function register()
    {
        return view('frontend.user.on-boarding.register');
    }
    public function categoryService()
    {
        $categories = Category::with('subCategories')->get();
        $selectedCategoryId = null;
        //send previously selected value if exists
        $user = Auth::user();
        if ($user) {
            $userPreference = UserPreference::where('user_id', $user->id)->latest()->first();
            if ($userPreference) {
                $selectedCategoryId = $userPreference->category_id;
            }
        }

        return view('frontend.user.on-boarding.category_service', compact('categories', 'selectedCategoryId'));
    }


    /**
     * Handle the category selection form submission.
     */
    public function categoryServiceSubmit(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
        ]);

        $user = Auth::user();
        if ($user) {
            // Store or update user preference for category, keeping only one row per user
            $preference = UserPreference::firstOrNew(['user_id' => $user->id]);
            $preference->category_id = $validated['category_id'];
            $preference->save();
        }

        return redirect()->route('user.onboarding.in_person_or_online');
    }

    public function inPersonOrOnline()
    {
        $selectedEducationType = null;
        $user = Auth::user();
        if ($user) {
            $userPreference = UserPreference::where('user_id', $user->id)->latest()->first();
            if ($userPreference) {
            $selectedEducationType = $userPreference->education_type;
            }
        }
        return view('frontend.user.on-boarding.in_person_or_online', compact('selectedEducationType'));
    }


    /**
     * Handle the in-person or online education selection form submission.
     */
    public function inPersonOrOnlineSubmit(Request $request)
    {
        $validated = $request->validate([
            'education_type' => 'required|in:in-person,online',
        ]);

        $user = Auth::user();
        if ($user) {
            // Store or update user preference for education_type, keeping only one row per user
            $preference = UserPreference::firstOrNew(['user_id' => $user->id]);
            $preference->education_type = (string) $validated['education_type'];
            $preference->save();
        }

        // Redirect to the next onboarding step
        if ($validated['education_type'] === 'in-person') {
            return redirect()->route('user.onboarding.in_person_education_location');
        } else {
            return redirect()->route('user.onboarding.online_education');
        }
    }

    public function inPersonEducationLocation()
    {
        $countries = CountryList::all();
        $selectedCountry = null;
        $selectedCity = null;
        $user = Auth::user();
        if ($user) {
            $userPreference = UserPreference::where('user_id', $user->id)->latest()->first();
            if ($userPreference) {
                $selectedCountry = $userPreference->country ?? null;
                $selectedCity = $userPreference->city ?? null;
            }
        }
        return view('frontend.user.on-boarding.in_person_education_location', compact('countries', 'selectedCountry', 'selectedCity'));
    }

    public function onlineEducation()
    {
        $selectedEducationOption = null;
        $user = Auth::user();
        if ($user) {
            $userPreference = UserPreference::where('user_id', $user->id)->latest()->first();
            if ($userPreference) {
                // Determine which option was previously selected
                if ($userPreference->wants_courses && $userPreference->wants_mentoring) {
                    $selectedEducationOption = 'both';
                } elseif ($userPreference->wants_courses) {
                    $selectedEducationOption = 'courses';
                } elseif ($userPreference->wants_mentoring) {
                    $selectedEducationOption = 'mentoring';
                }
            }
        }
        return view('frontend.user.on-boarding.online_education', compact('selectedEducationOption'));
    }

    /**
     * Handle the in-person education location form submission.
     */
    public function inPersonEducationLocationSubmit(Request $request)
    {
        $validated = $request->validate([
            'country' => 'required|string|max:100',
            'city' => 'required|string|max:100',
        ]);

        $user = Auth::user();
        if ($user) {
            $preference = UserPreference::firstOrNew(['user_id' => $user->id]);
            $preference->country = $validated['country'];
            $preference->city = $validated['city'];
            $preference->save();
        }

        // Redirect to the next onboarding step (customize as needed)
        return redirect()->route('user.dashboard');
    }

    /**
     * Handle the online education options form submission.
     */
    public function onlineEducationSubmit(Request $request)
    {
        $validated = $request->validate([
            'education_option' => 'required|in:courses,mentoring,both',
        ]);

        $option = $request->input('education_option');
        $wantsCourses = $option === 'courses' || $option === 'both';
        $wantsMentoring = $option === 'mentoring' || $option === 'both';

        $user = Auth::user();
        if ($user) {
            $preference = UserPreference::firstOrNew(['user_id' => $user->id]);
            $preference->wants_courses = $wantsCourses;
            $preference->wants_mentoring = $wantsMentoring;
            $preference->save();
        }

        // Redirect to dashboard after onboarding is complete
        return redirect()->route('user.dashboard');
    }
}