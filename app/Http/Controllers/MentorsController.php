<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mentor;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MentorsController extends Controller
{
    /**
     * Show the mentors page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
        public function index(Request $request)
    {
        $preferredMentors = collect();
        $regularMentors = collect();
        
        // Base query for all verified mentors
        $baseQuery = Mentor::with(['user', 'reviews', 'sessionBookings.category', 'sessionBookings.subCategories'])
            ->verified();

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
                            
                            // Match sub-category preferences
                            if (!empty($userPreferences['sub_categories'])) {
                                $subQ->whereIn('sub_category_id', $userPreferences['sub_categories']);
                            }
                        });
                    });
                }
                
                // Get preference-matched mentors
                $preferredMentors = $preferenceQuery->get();
            } else {
                $preferredMentors = collect();
            }
        } else {
            $preferredMentors = collect();
        }
        
        // Get regular mentors (excluding already selected preferred mentors)
        $excludeIds = $preferredMentors->pluck('id')->toArray();
        
        $regularQuery = clone $baseQuery;
        if (!empty($excludeIds)) {
            $regularQuery->whereNotIn('id', $excludeIds);
        }
        
        $regularMentors = $regularQuery->get();
        
        // Combine preferred and regular mentors, with preferred ones first
        $allMentors = $preferredMentors->merge($regularMentors);
        
        // Apply search functionality to combined results
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $allMentors = $allMentors->filter(function ($mentor) use ($searchTerm) {
                return stripos($mentor->user->name ?? '', $searchTerm) != false ||
                       stripos($mentor->top_category ?? '', $searchTerm) != false ||
                       stripos(implode(' ', $mentor->top_category_sub_categories ?? []), $searchTerm) != false;
            });
        }
        
        // Process mentors data
        $mentors = $allMentors->map(function ($mentor) use ($preferredMentors) {
            $totalReviews = $mentor->totalReviews();
            $averageRating = $mentor->averageRating() ?? 0;
            
            // Get top/latest category from session bookings
            $topCategory = $mentor->sessionBookings
                ->pluck('category.name')
                ->unique()
                ->first();
            
            // Get sub-categories of that top category
            $topCategorySubCategories = [];
            if ($topCategory) {
                $topCategorySubCategories = $mentor->sessionBookings
                    ->filter(function ($session) use ($topCategory) {
                        return $session->category->name == $topCategory;
                    })
                    ->flatMap(function ($session) {
                        return $session->subCategories;
                    })
                    ->unique('id')
                    ->pluck('name')
                    ->toArray();
            }
            
            return [
                'id' => $mentor->id,
                'user_id' => $mentor->user_id,
                'name' => $mentor->user->name ?? 'Unknown',
                'photo' => $mentor->photo ?? null,
                'work_experience' => $mentor->work_experience ?? '',
                'top_category' => $topCategory,
                'top_category_sub_categories' => $topCategorySubCategories,
                'bio' => $mentor->bio ?? '',
                'total_reviews' => $totalReviews,
                'average_rating' => $averageRating,
                'formatted_rating' => number_format($averageRating, 1),
                'star_rating' => $this->getStarRating($averageRating),
                'lowest_session_rate' => $this->getLowestSessionRate($mentor),
                'is_preferred' => $preferredMentors->contains('id', $mentor->id), // Add flag for preferred mentors
            ];
        })
        ->sortByDesc('is_preferred') // Sort preferred mentors first
        ->sortByDesc('total_reviews')
        ->sortByDesc('average_rating')
        ->values();

        // Manual pagination since we're processing data after fetching
        $perPage = 12;
        $currentPage = $request->get('page', 1);
        $total = $mentors->count();
        $mentors = $mentors->forPage($currentPage, $perPage);
        
        // Create paginator instance
        $mentors = new \Illuminate\Pagination\LengthAwarePaginator(
            $mentors,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        return view('frontend.mentors.index', compact('mentors'));
    }

    /**
     * Get star rating display for a given rating.
     */
    private function getStarRating($rating)
    {
        $fullStars = floor($rating);
        $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0;
        $emptyStars = 5 - $fullStars - $halfStar;

        return [
            'full_stars' => $fullStars,
            'half_star' => $halfStar,
            'empty_stars' => $emptyStars,
            'rating' => $rating,
            'formatted_rating' => number_format($rating, 1),
        ];
    }

    /**
     * Get the lowest session rate for a mentor.
     */
    private function getLowestSessionRate($mentor)
    {
        $lowestRate = $mentor->sessionBookings
            ->where('fee', '>', 0)
            ->min('fee');
        
        return $lowestRate ? number_format($lowestRate, 2) : 'N/A';
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
