<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Mentor;
use App\Models\Review;
use App\Models\UserEnrollment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $popularCourses = $this->getPopularCourses();
        $newCourses = $this->getNewCourses();
        $featuredCourses = $this->getFeaturedCourses();
        $topReviews = $this->getTopReviews();
        return view('frontend.home.index', compact('topMentors', 'popularCourses', 'newCourses', 'featuredCourses', 'topReviews'));
    }

    /**
     * Get top 6 rated mentors with preference matching for logged-in users
     */
    private function getTopRatedMentors()
    {
        $preferredMentors = collect();
        $regularMentors = collect();
        
        // Base query for all available mentors
        $baseQuery = Mentor::where('availability', 'available')
            ->whereHas('user', function($q) {
                $q->where('role', 'mentor');
            })
            ->with(['user', 'sessionBookings' => function($q) {
                $q->select('id', 'mentor_id', 'fee', 'category_id');
            }, 'sessionBookings.category', 'sessionBookings.subCategories'])
            ->withCount(['reviews as total_reviews'])
            ->withAvg('reviews', 'rating');

        // If user is logged in, try to get preference-matched mentors first
        if (Auth::check()) {
            $user = Auth::user();
            $userPreferences = $this->getUserPreferences($user);
            
            if (!empty($userPreferences)) {
                $preferenceQuery = clone $baseQuery;
                
                // Apply mentor type filter at the mentor level
                if (!empty($userPreferences['mentor_type'])) {
                    $preferenceQuery->where('type', $userPreferences['mentor_type']);
                }
                
                // Apply category and sub-category filters at the sessionBookings level
                if (!empty($userPreferences['categories']) || !empty($userPreferences['sub_categories'])) {
                    $preferenceQuery->whereHas('sessionBookings', function($q) use ($userPreferences) {
                        $q->where(function($subQ) use ($userPreferences) {
                            // Match category preferences
                            if (!empty($userPreferences['categories'])) {
                                $subQ->whereIn('category_id', $userPreferences['categories']);
                            }
                        });
                        
                        // Match sub-category preferences through the pivot table
                        if (!empty($userPreferences['sub_categories'])) {
                            $q->whereHas('subCategories', function($subCatQ) use ($userPreferences) {
                                $subCatQ->whereIn('sub_categories.id', $userPreferences['sub_categories']);
                            });
                        }
                    });
                }
                
                // Get preference-matched mentors (up to 6)
                $preferredMentors = $preferenceQuery->orderBy('reviews_avg_rating', 'desc')
                    ->orderBy('total_reviews', 'desc')
                    ->limit(6)
                    ->get();
            }
        }
        
        // If we don't have 6 preference-matched mentors, get regular top mentors
        $remainingSlots = 6 - $preferredMentors->count();
        
        if ($remainingSlots > 0) {
            // Get regular top mentors, excluding already selected preferred mentors
            $excludeIds = $preferredMentors->pluck('id')->toArray();
            
            $regularQuery = clone $baseQuery;
            if (!empty($excludeIds)) {
                $regularQuery->whereNotIn('id', $excludeIds);
            }
            
            $regularMentors = $regularQuery->orderBy('reviews_avg_rating', 'desc')
                ->orderBy('total_reviews', 'desc')
                ->limit($remainingSlots)
                ->get();
        }
        
        // Combine preferred and regular mentors, with preferred ones first
        $allMentors = $preferredMentors->merge($regularMentors);
        
        // Add lowest session rate for each mentor
        $allMentors->each(function ($mentor) {
            // Debug: Check if sessionBookings are loaded
            if ($mentor->sessionBookings && $mentor->sessionBookings->isNotEmpty()) {
                // Filter out null fees and get the minimum
                $validFees = $mentor->sessionBookings
                    ->whereNotNull('fee')
                    ->where('fee', '>', 0)
                    ->pluck('fee');
                
                if ($validFees->isNotEmpty()) {
                    $mentor->lowest_session_rate = $validFees->min();
                    \Log::info("Mentor {$mentor->user->name}: Found valid fees: " . $validFees->implode(', ') . ", Lowest: {$mentor->lowest_session_rate}");
                } else {
                    $mentor->lowest_session_rate = 0; // No valid fees
                    \Log::info("Mentor {$mentor->user->name}: No valid fees found");
                }
            } else {
                $mentor->lowest_session_rate = 0; // No sessions
                \Log::info("Mentor {$mentor->user->name}: No sessionBookings loaded");
            }
        });
        
        return $allMentors;
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

    /**
     * Get top 6 popular courses with preference matching for logged-in users
     */
    private function getPopularCourses()
    {
        $preferredCourses = collect();
        $regularCourses = collect();
        
        // Base query for all approved courses
        $baseQuery = \App\Models\Course::with(['mentor.user', 'category', 'subCategories', 'reviews'])
            ->approved();

        // If user is logged in, try to get preference-matched courses first
        if (Auth::check()) {
            $user = Auth::user();
            $userPreferences = $this->getUserPreferences($user);
            
            if (!empty($userPreferences)) {
                $preferenceQuery = clone $baseQuery;
                
                // Apply category filter
                if (!empty($userPreferences['categories'])) {
                    $preferenceQuery->whereIn('category_id', $userPreferences['categories']);
                }
                
                // Apply sub-category filter
                if (!empty($userPreferences['sub_categories'])) {
                    $preferenceQuery->whereHas('subCategories', function($q) use ($userPreferences) {
                        $q->whereIn('sub_categories.id', $userPreferences['sub_categories']);
                    });
                }
                
                // Get preference-matched courses (up to 6)
                $preferredCourses = $preferenceQuery->get()->sortByDesc(function($course) {
                    $avgRating = $course->averageRating() ?? 0;
                    $reviewCount = $course->totalReviews();
                    return [$avgRating, $reviewCount, $course->created_at];
                })->take(6);
            }
        }
        
        // If we don't have 6 preference-matched courses, get regular popular courses
        $remainingSlots = 6 - $preferredCourses->count();
        
        if ($remainingSlots > 0) {
            // Get regular popular courses, excluding already selected preferred courses
            $excludeIds = $preferredCourses->pluck('id')->toArray();
            
            $regularQuery = clone $baseQuery;
            if (!empty($excludeIds)) {
                $regularQuery->whereNotIn('id', $excludeIds);
            }
            
            $regularCourses = $regularQuery->get()->sortByDesc(function($course) {
                $avgRating = $course->averageRating() ?? 0;
                $reviewCount = $course->totalReviews();
                return [$avgRating, $reviewCount, $course->created_at];
            })->take($remainingSlots);
        }
        
        // Combine preferred and regular courses, with preferred ones first
        $allCourses = $preferredCourses->merge($regularCourses);
        
        return $allCourses;
    }

    /**
     * Get top 6 newest courses with preference matching for logged-in users
     */
    private function getNewCourses()
    {
        $preferredCourses = collect();
        $regularCourses = collect();
        
        // Base query for all approved courses
        $baseQuery = \App\Models\Course::with(['mentor.user', 'category', 'subCategories', 'reviews'])
            ->approved();
        
        // If user is logged in, try to get preference-matched courses first
        if (Auth::check()) {
            $user = Auth::user();
            $userPreferences = $this->getUserPreferences($user);
            
            if (!empty($userPreferences)) {
                $preferenceQuery = clone $baseQuery;
                
                // Apply category filter
                if (!empty($userPreferences['categories'])) {
                    $preferenceQuery->whereIn('category_id', $userPreferences['categories']);
                }
                
                // Apply sub-category filter
                if (!empty($userPreferences['sub_categories'])) {
                    $preferenceQuery->whereHas('subCategories', function($q) use ($userPreferences) {
                        $q->whereIn('sub_categories.id', $userPreferences['sub_categories']);
                    });
                }
                
                // Get preference-matched courses (up to 6) - ordered by newest first
                $preferredCourses = $preferenceQuery->orderBy('created_at', 'desc')
                    ->limit(6)
                    ->get();
            }
        }
        
        // If we don't have 6 preference-matched courses, get regular new courses
        $remainingSlots = 6 - $preferredCourses->count();
        
        if ($remainingSlots > 0) {
            // Get regular new courses, excluding already selected preferred courses
            $excludeIds = $preferredCourses->pluck('id')->toArray();
            
            $regularQuery = clone $baseQuery;
            if (!empty($excludeIds)) {
                $regularQuery->whereNotIn('id', $excludeIds);
            }
            
            $regularCourses = $regularQuery->orderBy('created_at', 'desc')
                ->limit($remainingSlots)
                ->get();
        }
        
        // Combine preferred and regular courses, with preferred ones first
        $allCourses = $preferredCourses->merge($regularCourses);
        
        return $allCourses;
    }

    /**
     * Get featured courses (up to 6)
     */
    private function getFeaturedCourses()
    {
        return \App\Models\Course::with(['mentor.user', 'category', 'subCategories', 'reviews'])
            ->approved()
            ->featured()
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();
    }

    /**
     * Get latest 12 highest-rated reviews
     */
    private function getTopReviews()
    {
        return \App\Models\Review::with(['user', 'course.mentor.user'])
            ->where('rating', '>=', 4) // Only reviews with rating 4 or higher
            ->orderBy('rating', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(12)
            ->get();
    }
}
