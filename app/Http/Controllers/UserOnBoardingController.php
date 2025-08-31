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
        $selectedSubCategoryId = null;
        $customSubCategoryName = null;
        
        //send previously selected value if exists
        $user = Auth::user();
        if ($user) {
            $userPreference = UserPreference::where('user_id', $user->id)->latest()->first();
            if ($userPreference) {
                $selectedCategoryId = $userPreference->category_id;
                $selectedSubCategoryId = $userPreference->sub_category_id;
                
                // If "Others" category is selected and has a custom subcategory, get the name
                if ($selectedCategoryId && strtolower(Category::find($selectedCategoryId)->name) == 'others' && $selectedSubCategoryId) {
                    $customSubCategory = \App\Models\SubCategory::find($selectedSubCategoryId);
                    if ($customSubCategory) {
                        $customSubCategoryName = $customSubCategory->name;
                    }
                }
            }
        }

        return view('frontend.user.on-boarding.category_service', compact('categories', 'selectedCategoryId', 'selectedSubCategoryId', 'customSubCategoryName'));
    }


    /**
     * Handle the category selection form submission.
     */
    public function categoryServiceSubmit(Request $request)
    {
        
        try {
            $validated = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'custom_category_name' => 'nullable|string|max:50',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        }

        $user = Auth::user();
        $categoryId = $validated['category_id'];
        $subCategoryId = null;

        

        // Check if "Others" category is selected and custom name is provided
        $selectedCategory = Category::find($categoryId);
        
        if (strtolower($selectedCategory->name) == 'others' && !empty($validated['custom_category_name'])) {
            $customName = trim($validated['custom_category_name']);
            
            // Check if user already has a custom subcategory under "Others"
            $existingSubCategory = null;
            if ($user) {
                $existingPreference = UserPreference::where('user_id', $user->id)
                    ->where('category_id', $categoryId)
                    ->whereNotNull('sub_category_id')
                    ->first();
                
                if ($existingPreference && $existingPreference->sub_category_id) {
                    $existingSubCategory = \App\Models\SubCategory::find($existingPreference->sub_category_id);
                    // Verify it's actually under "Others" category
                    if ($existingSubCategory && $existingSubCategory->category_id == $categoryId) {
                        // Found existing custom subcategory
                    } else {
                        $existingSubCategory = null;
                    }
                }
            }
            
            if ($existingSubCategory) {
                // Update existing subcategory
                $existingSubCategory->update([
                    'name' => $customName,
                    'description' => 'Custom subcategory (updated)',
                ]);
                
                $subCategoryId = $existingSubCategory->id;
            } else {
                // Create new subcategory under "Others" category
                $customSubCategory = \App\Models\SubCategory::create([
                    'category_id' => $categoryId, // "Others" category ID
                    'name' => $customName,
                    'description' => 'Custom subcategory',
                    'image' => null,
                ]);

                // Use the new subcategory ID
                $subCategoryId = $customSubCategory->id;
            }
        }

        if ($user) {
            // Store or update user preference for category and subcategory
            $preference = UserPreference::firstOrNew(['user_id' => $user->id]);
            $preference->category_id = $categoryId;
            if ($subCategoryId) {
                $preference->sub_category_id = $subCategoryId;
            }
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
        if ($validated['education_type'] == 'in-person') {
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
        $wantsCourses = $option == 'courses' || $option == 'both';
        $wantsMentoring = $option == 'mentoring' || $option == 'both';

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