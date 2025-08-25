<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Mentor;
use App\Models\Review;
use App\Models\UserEnrollment;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Show the application home page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $topMentors = $this->getTopRatedMentors();
        return view('frontend.home.index', compact('topMentors'));
    }

    /**
     * Get top 6 rated mentors with preference matching for logged-in users
     */
    private function getTopRatedMentors()
    {
        $query = Mentor::where('availability', 'available')
            ->whereHas('user', function($q) {
                $q->where('role', 'mentor');
            })
            ->with(['user', 'sessionBookings.category', 'sessionBookings.subCategories'])
            ->withCount(['reviews as total_reviews'])
            ->withAvg('reviews', 'rating');

        // If user is logged in, apply preference matching
        if (Auth::check()) {
            $user = Auth::user();
            $userPreferences = $this->getUserPreferences($user);
            
            if (!empty($userPreferences)) {
                // Apply mentor type filter at the mentor level
                if (!empty($userPreferences['mentor_type'])) {
                    $query->where('type', $userPreferences['mentor_type']);
                }
                
                // Apply category and sub-category filters at the sessionBookings level
                if (!empty($userPreferences['categories']) || !empty($userPreferences['sub_categories'])) {
                    $query->whereHas('sessionBookings', function($q) use ($userPreferences) {
                        $q->where(function($subQ) use ($userPreferences) {
                            // Match category preferences
                            if (!empty($userPreferences['categories'])) {
                                $subQ->whereIn('category_id', $userPreferences['categories']);
                            }
                            
                            // Match sub-category preferences
                            if (!empty($userPreferences['sub_categories'])) {
                                $subQ->whereIn('sub_category_id', $userPreferences['sub_categories']);
                            }
                        });
                    });
                }
            }
        }

        // Get top rated mentors, ordered by rating and review count
        return $query->orderBy('reviews_avg_rating', 'desc')
            ->orderBy('total_reviews', 'desc')
            ->limit(6)
            ->get();
    }

    /**
     * Get user preferences from user_preferences table
     */
    private function getUserPreferences($user)
    {
        $preferences = [];
        
        // Get user's preferences from user_preferences table
        $userPreference = $user->userPreferences;
        
        if (!$userPreference) {
            return $preferences;
        }

        // Get category preference
        if ($userPreference->category_id) {
            $preferences['categories'] = [$userPreference->category_id];
        }

        // Get sub-category preference
        if ($userPreference->sub_category_id) {
            $preferences['sub_categories'] = [$userPreference->sub_category_id];
        }

        // Get education type preference (online/in-person)
        if ($userPreference->education_type) {
            switch ($userPreference->education_type) {
                case 'online':
                    $preferences['mentor_type'] = 'online';
                    break;
                case 'in-person':
                    $preferences['mentor_type'] = 'in-person';
                    break;
                case 'both':
                    // For 'both', we don't restrict by type
                    break;
            }
        }

        return $preferences;
    }
}
