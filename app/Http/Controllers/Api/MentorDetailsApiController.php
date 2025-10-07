<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mentor;
use App\Models\SessionBooking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MentorDetailsApiController extends Controller
{
    /**
     * Get comprehensive mentor details
     *
     * @param Mentor $mentor
     * @return JsonResponse
     */
    public function getMentorDetails(Mentor $mentor): JsonResponse
    {
        try {
            // Load all necessary relationships
            $mentor->load([
                'user', 
                'reviews.user', 
                'sessionBookings.category', 
                'sessionBookings.subCategories',
                'courses' => function($query) {
                    $query->where('status', 'approved');
                }
            ]);

            // Calculate rating and reviews
            $totalReviews = $mentor->totalReviews();
            $averageRating = $mentor->averageRating() ?? 0;
            
            // Get top category and its sub-categories
            $topCategory = $mentor->sessionBookings
                ->pluck('category.name')
                ->unique()
                ->first();
            
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

            // Get star rating breakdown
            $starRating = $this->getStarRating($averageRating);

            // Get review statistics
            $reviewStats = $this->getReviewStatistics($mentor);

            // Get recent reviews
            $recentReviews = $mentor->reviews()
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
                ->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'rating' => $review->rating,
                        'comment' => $review->comment,
                        'user_name' => $review->user ? $review->user->name : 'Anonymous Student',
                        'user_photo' => $review->user && $review->user->photo ? asset('storage/' . $review->user->photo) : null,
                        'created_at' => $review->created_at->toISOString(),
                        'updated_at' => $review->updated_at->toISOString(),
                    ];
                });

            // Get courses summary
            $coursesSummary = [
                'total_courses' => $mentor->courses->count(),
                'approved_courses' => $mentor->courses->where('status', 'approved')->count(),
                'featured_courses' => $mentor->courses->where('featured', true)->count(),
            ];

            // Get sessions summary
            $sessionsSummary = [
                'total_sessions' => $mentor->sessionBookings->count(),
                'active_sessions' => $mentor->sessionBookings->where('status', 'active')->count(),
                'booked_sessions' => $mentor->sessionBookings->where('status', 'booked')->count(),
            ];

            // Check if user can review this mentor
            $canReview = false;
            $existingReview = null;
            
            // Check if request has authentication token
            $token = request()->bearerToken();
            if ($token && Auth::guard('sanctum')->check()) {
                $userHasBookedSession = \App\Models\UserEnrollment::where('user_id', Auth::guard('sanctum')->id())
                    ->where('enrollable_type', SessionBooking::class)
                    ->whereHas('enrollable', function($query) use ($mentor) {
                        $query->where('mentor_id', $mentor->id);
                    })
                    ->exists();
                
                $canReview = $userHasBookedSession;
                $existingReview = Auth::guard('sanctum')->user()->reviews()->where('mentor_id', $mentor->id)->first();
            }

            // Format existing review if user has one
            if ($existingReview) {
                $existingReview = [
                    'id' => $existingReview->id,
                    'rating' => $existingReview->rating,
                    'comment' => $existingReview->comment,
                    'created_at' => $existingReview->created_at->toISOString(),
                    'updated_at' => $existingReview->updated_at->toISOString(),
                ];
            }

            $mentorData = [
                'id' => $mentor->id,
                'name' => $mentor->user->name ?? 'Unknown',
                'email' => $mentor->user->email ?? null,
                'photo' => $mentor->photo ? asset('storage/' . $mentor->photo) : null,
                'bio' => $mentor->bio,
                'work_experience' => $mentor->work_experience,
                'certifications' => $mentor->certifications ?? [],
                'availability' => $mentor->availability ?? 'available',
                'working_hours' => $mentor->working_hours ?? [],
                'verified' => $mentor->verified,
                'type' => $mentor->type,
                'created_at' => $mentor->created_at->toISOString(),
                'updated_at' => $mentor->updated_at->toISOString(),

                // Statistics
                'statistics' => [
                    'average_rating' => round($averageRating, 1),
                    'total_reviews' => $totalReviews,
                    'formatted_rating' => number_format($averageRating, 1),
                    'star_rating' => $starRating,
                ],

                // Categories
                'top_category' => $topCategory,
                'top_category_sub_categories' => $topCategorySubCategories,

                // Summary data
                'courses_summary' => $coursesSummary,
                'sessions_summary' => $sessionsSummary,

                // Review data
                'can_review' => $canReview,
                'existing_review' => $existingReview,
                'review_statistics' => $reviewStats,
                'recent_reviews' => $recentReviews,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Mentor details retrieved successfully',
                'data' => $mentorData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve mentor details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get mentor sessions with pagination and filtering
     *
     * @param Mentor $mentor
     * @param Request $request
     * @return JsonResponse
     */
    public function getMentorSessions(Mentor $mentor, Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 9);
            $page = $request->get('page', 1);
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $categoryId = $request->get('category_id');
            $subCategoryId = $request->get('sub_category_id');

            // Build query for active session bookings
            $sessionsQuery = $mentor->sessionBookings()
                ->with(['category', 'subCategories', 'mentor.user'])
                ->where('status', 'active');

            // Apply date range filter if provided
            if ($startDate && $endDate) {
                $sessionsQuery->whereBetween('date', [$startDate, $endDate]);
            }

            // Apply category filter if provided
            if ($categoryId) {
                $sessionsQuery->where('category_id', $categoryId);
            }

            // Apply subcategory filter if provided
            if ($subCategoryId) {
                $sessionsQuery->whereHas('subCategories', function($query) use ($subCategoryId) {
                    $query->where('sub_categories.id', $subCategoryId);
                });
            }

            // Get paginated results
            $sessions = $sessionsQuery
                ->orderBy('date')
                ->orderBy('start_time')
                ->paginate($perPage, ['*'], 'page', $page);

            $sessionsData = $sessions->map(function ($session) {
                return [
                    'id' => $session->id,
                    'title' => $session->title,
                    'description' => $session->description,
                    'date' => $session->date,
                    'start_time' => $session->start_time,
                    'end_time' => $session->end_time,
                    'duration' => $session->duration,
                    'price' => $session->price,
                    'status' => $session->status,
                    'max_participants' => $session->max_participants,
                    'current_participants' => $session->current_participants,
                    'category' => [
                        'id' => $session->category->id,
                        'name' => $session->category->name,
                    ],
                    'sub_categories' => $session->subCategories->map(function ($subCategory) {
                        return [
                            'id' => $subCategory->id,
                            'name' => $subCategory->name,
                        ];
                    }),
                    'mentor' => [
                        'id' => $session->mentor->id,
                        'name' => $session->mentor->user->name,
                        'photo' => $session->mentor->photo ? asset('storage/' . $session->mentor->photo) : null,
                    ],
                    'created_at' => $session->created_at->toISOString(),
                    'updated_at' => $session->updated_at->toISOString(),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Mentor sessions retrieved successfully',
                'data' => $sessionsData,
                'pagination' => [
                    'current_page' => $sessions->currentPage(),
                    'last_page' => $sessions->lastPage(),
                    'per_page' => $sessions->perPage(),
                    'total' => $sessions->total(),
                    'has_more_pages' => $sessions->hasMorePages(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve mentor sessions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get mentor reviews with pagination
     *
     * @param Mentor $mentor
     * @param Request $request
     * @return JsonResponse
     */
    public function getMentorReviews(Mentor $mentor, Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);

            $reviews = $mentor->reviews()
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            $reviewsData = $reviews->map(function ($review) {
                return [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'user_name' => $review->user ? $review->user->name : 'Anonymous Student',
                    'user_photo' => $review->user && $review->user->photo ? asset('storage/' . $review->user->photo) : null,
                    'created_at' => $review->created_at->toISOString(),
                    'updated_at' => $review->updated_at->toISOString(),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Mentor reviews retrieved successfully',
                'data' => $reviewsData,
                'pagination' => [
                    'current_page' => $reviews->currentPage(),
                    'last_page' => $reviews->lastPage(),
                    'per_page' => $reviews->perPage(),
                    'total' => $reviews->total(),
                    'has_more_pages' => $reviews->hasMorePages(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve mentor reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get mentor courses
     *
     * @param Mentor $mentor
     * @param Request $request
     * @return JsonResponse
     */
    public function getMentorCourses(Mentor $mentor, Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);
            $status = $request->get('status', 'all'); // all, approved, pending, rejected
            $categoryId = $request->get('category_id');
            $subCategoryId = $request->get('sub_category_id');

            $query = $mentor->courses()->with(['category', 'subCategories']);

            if ($status != 'all') {
                $query->where('status', $status);
            }

            // Apply category filter if provided
            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }

            // Apply subcategory filter if provided
            if ($subCategoryId) {
                $query->whereHas('subCategories', function($query) use ($subCategoryId) {
                    $query->where('sub_categories.id', $subCategoryId);
                });
            }

            $courses = $query->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            $coursesData = $courses->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'description' => $course->description,
                    'price' => $course->price,
                    'discount' => $course->discount,
                    'discounted_price' => $course->discount > 0 ? $course->discounted_price : null,
                    'thumbnail' => $course->thumbnail_url,
                    'cover_photo' => $course->cover_photo_url,
                    'duration_days' => $course->duration_days,
                    'start_date' => $course->start_date ? $course->start_date->toDateString() : null,
                    'end_date' => $course->end_date ? $course->end_date->toDateString() : null,
                    'status' => $course->status,
                    'featured' => $course->featured,
                    'rating' => round($course->averageRating() ?? 0, 1),
                    'total_reviews' => $course->totalReviews(),
                    'enrollment_count' => $course->enrolledStudentsCount(),
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
                    'created_at' => $course->created_at->toISOString(),
                    'updated_at' => $course->updated_at->toISOString(),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Mentor courses retrieved successfully',
                'data' => $coursesData,
                'pagination' => [
                    'current_page' => $courses->currentPage(),
                    'last_page' => $courses->lastPage(),
                    'per_page' => $courses->perPage(),
                    'total' => $courses->total(),
                    'has_more_pages' => $courses->hasMorePages(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve mentor courses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get mentor statistics (mentor/admin only)
     *
     * @param Mentor $mentor
     * @return JsonResponse
     */
    public function getMentorStatistics(Mentor $mentor): JsonResponse
    {
        try {
            // Check if user is the mentor or admin
            if (Auth::check()) {
                $user = Auth::user();
                $isMentor = $user->mentor && $user->mentor->id == $mentor->id;
                $isAdmin = $user->isAdmin();
                
                if (!$isMentor && !$isAdmin) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized',
                        'error' => 'Only the mentor or admin can view mentor statistics'
                    ], 403);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                    'error' => 'Authentication required'
                ], 401);
            }

            $statistics = [
                'total_reviews' => $mentor->totalReviews(),
                'average_rating' => round($mentor->averageRating() ?? 0, 1),
                'total_courses' => $mentor->courses->count(),
                'approved_courses' => $mentor->courses->where('status', 'approved')->count(),
                'total_sessions' => $mentor->sessionBookings->count(),
                'active_sessions' => $mentor->sessionBookings->where('status', 'active')->count(),
                'booked_sessions' => $mentor->sessionBookings->where('status', 'booked')->count(),
                'review_statistics' => $this->getReviewStatistics($mentor),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Mentor statistics retrieved successfully',
                'data' => $statistics,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve mentor statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get star rating breakdown
     *
     * @param float $rating
     * @return array
     */
    private function getStarRating($rating): array
    {
        $fullStars = floor($rating);
        $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0;
        $emptyStars = 5 - $fullStars - $halfStar;
        
        return [
            'full_stars' => $fullStars,
            'half_star' => $halfStar,
            'empty_stars' => $emptyStars,
            'rating' => $rating,
        ];
    }

    /**
     * Get review statistics for a mentor
     *
     * @param Mentor $mentor
     * @return array
     */
    private function getReviewStatistics(Mentor $mentor): array
    {
        $reviews = $mentor->reviews;
        $totalReviews = $reviews->count();
        
        if ($totalReviews == 0) {
            return [
                'average_rating' => 0,
                'total_reviews' => 0,
                'rating_breakdown' => [
                    '5_star' => 0,
                    '4_star' => 0,
                    '3_star' => 0,
                    '2_star' => 0,
                    '1_star' => 0,
                ],
                'rating_percentages' => [
                    '5_star' => 0,
                    '4_star' => 0,
                    '3_star' => 0,
                    '2_star' => 0,
                    '1_star' => 0,
                ]
            ];
        }

        $averageRating = $reviews->avg('rating');
        $ratingBreakdown = [
            '5_star' => $reviews->where('rating', 5)->count(),
            '4_star' => $reviews->where('rating', 4)->count(),
            '3_star' => $reviews->where('rating', 3)->count(),
            '2_star' => $reviews->where('rating', 2)->count(),
            '1_star' => $reviews->where('rating', 1)->count(),
        ];

        $ratingPercentages = [
            '5_star' => round(($ratingBreakdown['5_star'] / $totalReviews) * 100, 1),
            '4_star' => round(($ratingBreakdown['4_star'] / $totalReviews) * 100, 1),
            '3_star' => round(($ratingBreakdown['3_star'] / $totalReviews) * 100, 1),
            '2_star' => round(($ratingBreakdown['2_star'] / $totalReviews) * 100, 1),
            '1_star' => round(($ratingBreakdown['1_star'] / $totalReviews) * 100, 1),
        ];

        return [
            'average_rating' => round($averageRating, 1),
            'total_reviews' => $totalReviews,
            'rating_breakdown' => $ratingBreakdown,
            'rating_percentages' => $ratingPercentages,
        ];
    }
}
