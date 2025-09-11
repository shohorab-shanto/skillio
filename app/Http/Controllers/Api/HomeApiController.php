<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Mentor;
use App\Models\Review;
use App\Models\UserEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeApiController extends Controller
{
    /**
     * Get top rated mentors for home page
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTopMentors(Request $request)
    {
        try {
            $categoryId = $request->get('category_id');
            $mentors = $this->getTopRatedMentors($categoryId);
            
            $formattedMentors = $mentors->map(function ($mentor) {
                return [
                    'id' => $mentor->id,
                    'name' => $mentor->user->name,
                    'email' => $mentor->user->email,
                    'photo' => $mentor->photo ? asset('storage/' . $mentor->photo) : null,
                    'bio' => $mentor->bio,
                    'work_experience' => $mentor->work_experience,
                    'type' => $mentor->type,
                    'availability' => $mentor->availability,
                    'rating' => round($mentor->reviews_avg_rating ?? 0, 1),
                    'total_reviews' => $mentor->total_reviews ?? 0,
                    'lowest_session_rate' => $mentor->lowest_session_rate ?? '0',
                    'skills' => $mentor->sessionBookings->pluck('category.name')
                        ->merge($mentor->sessionBookings->pluck('subCategories.*.name')->flatten())
                        ->unique()
                        ->values()
                        ->take(4),
                    'created_at' => $mentor->created_at,
                    'updated_at' => $mentor->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Top mentors retrieved successfully',
                'data' => $formattedMentors,
                'count' => $formattedMentors->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve top mentors',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get popular courses for home page
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPopularCourses(Request $request)
    {
        try {
            $categoryId = $request->get('category_id');
            $courses = $this->getPopularCoursesData($categoryId);
            
            $formattedCourses = $courses->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'description' => $course->description,
                    'price' => $course->price,
                    'thumbnail' => $course->thumbnail_url,
                    'cover_photo' => $course->cover_photo_url,
                    'level' => $course->level,
                    'duration' => $course->duration,
                    'is_featured' => $course->is_featured,
                    'status' => $course->status,
                    'rating' => round($course->averageRating() ?? 0, 1),
                    'total_reviews' => $course->totalReviews(),
                    'enrollment_count' => $course->enrolledStudentsCount(),
                    'mentor' => [
                        'id' => $course->mentor->id,
                        'name' => $course->mentor->user->name,
                        'photo' => $course->mentor->photo ? asset('storage/' . $course->mentor->photo) : null,
                    ],
                    'category' => [
                        'id' => $course->category->id,
                        'name' => $course->category->name,
                    ],
                    'sub_categories' => $course->subCategories->map(function ($subCategory) {
                        return [
                            'id' => $subCategory->id,
                            'name' => $subCategory->name,
                        ];
                    }),
                    'created_at' => $course->created_at,
                    'updated_at' => $course->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Popular courses retrieved successfully',
                'data' => $formattedCourses,
                'count' => $formattedCourses->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve popular courses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get new courses for home page
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNewCourses(Request $request)
    {
        try {
            $categoryId = $request->get('category_id');
            $courses = $this->getNewCoursesData($categoryId);
            
            $formattedCourses = $courses->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'description' => $course->description,
                    'price' => $course->price,
                    'thumbnail' => $course->thumbnail_url,
                    'cover_photo' => $course->cover_photo_url,
                    'level' => $course->level,
                    'duration' => $course->duration,
                    'is_featured' => $course->is_featured,
                    'status' => $course->status,
                    'rating' => round($course->averageRating() ?? 0, 1),
                    'total_reviews' => $course->totalReviews(),
                    'enrollment_count' => $course->enrolledStudentsCount(),
                    'mentor' => [
                        'id' => $course->mentor->id,
                        'name' => $course->mentor->user->name,
                        'photo' => $course->mentor->photo ? asset('storage/' . $course->mentor->photo) : null,
                    ],
                    'category' => [
                        'id' => $course->category->id,
                        'name' => $course->category->name,
                    ],
                    'sub_categories' => $course->subCategories->map(function ($subCategory) {
                        return [
                            'id' => $subCategory->id,
                            'name' => $subCategory->name,
                        ];
                    }),
                    'created_at' => $course->created_at,
                    'updated_at' => $course->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'New courses retrieved successfully',
                'data' => $formattedCourses,
                'count' => $formattedCourses->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve new courses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get featured courses for home page
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFeaturedCourses(Request $request)
    {
        try {
            $categoryId = $request->get('category_id');
            $courses = $this->getFeaturedCoursesData($categoryId);
            
            $formattedCourses = $courses->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'description' => $course->description,
                    'price' => $course->price,
                    'thumbnail' => $course->thumbnail_url,
                    'cover_photo' => $course->cover_photo_url,
                    'level' => $course->level,
                    'duration' => $course->duration,
                    'is_featured' => $course->is_featured,
                    'status' => $course->status,
                    'rating' => round($course->averageRating() ?? 0, 1),
                    'total_reviews' => $course->totalReviews(),
                    'enrollment_count' => $course->enrolledStudentsCount(),
                    'mentor' => [
                        'id' => $course->mentor->id,
                        'name' => $course->mentor->user->name,
                        'photo' => $course->mentor->photo ? asset('storage/' . $course->mentor->photo) : null,
                    ],
                    'category' => [
                        'id' => $course->category->id,
                        'name' => $course->category->name,
                    ],
                    'sub_categories' => $course->subCategories->map(function ($subCategory) {
                        return [
                            'id' => $subCategory->id,
                            'name' => $subCategory->name,
                        ];
                    }),
                    'created_at' => $course->created_at,
                    'updated_at' => $course->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Featured courses retrieved successfully',
                'data' => $formattedCourses,
                'count' => $formattedCourses->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve featured courses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get top reviews for home page
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTopReviews()
    {
        try {
            $reviews = $this->getTopReviewsData();
            
            $formattedReviews = $reviews->map(function ($review) {
                return [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'user' => [
                        'id' => $review->user->id,
                        'name' => $review->user->name,
                        'profile_photo' => $review->user->profile_photo ? asset('storage/' . $review->user->profile_photo) : null,
                    ],
                    'course' => $review->course ? [
                        'id' => $review->course->id,
                        'title' => $review->course->title,
                        'mentor_name' => $review->course->mentor->user->name,
                    ] : null,
                    'created_at' => $review->created_at,
                    'updated_at' => $review->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Top reviews retrieved successfully',
                'data' => $formattedReviews,
                'count' => $formattedReviews->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve top reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // Private helper methods (copied from HomeController)

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
            ->with(['user', 'sessionBookings.category', 'sessionBookings.subCategories'])
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
        
        // Add lowest session rate for each mentor (converted to hourly rate)
        $allMentors->each(function ($mentor) {
            $lowestHourlyRate = null;
            
            if ($mentor->sessionBookings && $mentor->sessionBookings->isNotEmpty()) {
                foreach ($mentor->sessionBookings as $session) {
                    if ($session->fee > 0 && $session->duration_in_minutes > 0) {
                        // Calculate hourly rate: (fee / duration_in_minutes) * 60
                        $hourlyRate = ($session->fee / $session->duration_in_minutes) * 60;
                        
                        if ($lowestHourlyRate == null || $hourlyRate < $lowestHourlyRate) {
                            $lowestHourlyRate = $hourlyRate;
                        }
                    }
                }
            }
            
            $mentor->lowest_session_rate = $lowestHourlyRate ? number_format($lowestHourlyRate, 2) : '0';
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
    private function getPopularCoursesData($categoryId = null)
    {
        $preferredCourses = collect();
        $regularCourses = collect();
        
        // Base query for all approved courses
        $baseQuery = \App\Models\Course::with(['mentor.user', 'category', 'subCategories', 'reviews'])
            ->approved();

        // Apply category filter if provided
        if ($categoryId) {
            $baseQuery->where('category_id', $categoryId);
        }

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
    private function getNewCoursesData($categoryId = null)
    {
        $preferredCourses = collect();
        $regularCourses = collect();
        
        // Base query for all approved courses
        $baseQuery = \App\Models\Course::with(['mentor.user', 'category', 'subCategories', 'reviews'])
            ->approved();

        // Apply category filter if provided
        if ($categoryId) {
            $baseQuery->where('category_id', $categoryId);
        }
        
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
    private function getFeaturedCoursesData($categoryId = null)
    {
        $query = \App\Models\Course::with(['mentor.user', 'category', 'subCategories', 'reviews'])
            ->approved()
            ->featured();

        // Apply category filter if provided
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return $query->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();
    }

    /**
     * Get latest 12 highest-rated reviews
     */
    private function getTopReviewsData()
    {
        return \App\Models\Review::with(['user', 'course.mentor.user'])
            ->where('rating', '>=', 4) // Only reviews with rating 4 or higher
            ->orderBy('rating', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(12)
            ->get();
    }

    /**
     * Get all home page data in a single request
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllHomeData(Request $request)
    {
        try {
            $categoryId = $request->get('category_id');
            $topMentors = $this->getTopRatedMentors($categoryId);
            $popularCourses = $this->getPopularCoursesData($categoryId);
            $newCourses = $this->getNewCoursesData($categoryId);
            $featuredCourses = $this->getFeaturedCoursesData($categoryId);
            $topReviews = $this->getTopReviewsData();

            // Format mentors data
            $formattedMentors = $topMentors->map(function ($mentor) {
                return [
                    'id' => $mentor->id,
                    'name' => $mentor->user->name,
                    'photo' => $mentor->photo ? asset('storage/' . $mentor->photo) : null,
                    'bio' => $mentor->bio,
                    'work_experience' => $mentor->work_experience,
                    'rating' => round($mentor->reviews_avg_rating ?? 0, 1),
                    'total_reviews' => $mentor->total_reviews ?? 0,
                    'lowest_session_rate' => $mentor->lowest_session_rate ?? '0',
                    'skills' => $mentor->sessionBookings->pluck('category.name')
                        ->merge($mentor->sessionBookings->pluck('subCategories.*.name')->flatten())
                        ->unique()
                        ->values()
                        ->take(4),
                ];
            });

            // Format courses data
            $formatCourse = function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'description' => $course->description,
                    'price' => $course->price,
                    'thumbnail' => $course->thumbnail_url,
                    'cover_photo' => $course->cover_photo_url,
                    'rating' => round($course->averageRating() ?? 0, 1),
                    'total_reviews' => $course->totalReviews(),
                    'enrollment_count' => $course->enrolledStudentsCount(),
                    'mentor_name' => $course->mentor->user->name,
                    'category_name' => $course->category->name,
                ];
            };

            // Format reviews data
            $formattedReviews = $topReviews->map(function ($review) {
                return [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'user_name' => $review->user ? $review->user->name : 'Anonymous Student',
                    'user_photo' => $review->user && $review->user->photo ? asset('storage/' . $review->user->photo) : null,
                    'course_title' => $review->course ? $review->course->title : null,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'All home page data retrieved successfully',
                'data' => [
                    'top_mentors' => $formattedMentors,
                    'popular_courses' => $popularCourses->map($formatCourse),
                    'new_courses' => $newCourses->map($formatCourse),
                    'featured_courses' => $featuredCourses->map($formatCourse),
                    'top_reviews' => $formattedReviews,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve all home page data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
