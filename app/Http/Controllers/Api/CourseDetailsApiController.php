<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\UserEnrollment;
use App\Models\Review;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CourseDetailsApiController extends Controller
{
    /**
     * Get comprehensive course details
     *
     * @param Course $course
     * @return JsonResponse
     */
    public function getCourseDetails(Course $course): JsonResponse
    {
        try {
            // Only show approved courses to public
            if (!$course->isApproved()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course not found',
                    'error' => 'Course is not approved or does not exist'
                ], 404);
            }

            // Load all necessary relationships
            $course->load([
                'mentor.user', 
                'category', 
                'subCategories', 
                'reviews.user' => function($query) {
                    $query->orderBy('created_at', 'desc');
                }
            ]);

            // Check if current user is enrolled and get enrollment details
            $isEnrolled = false;
            $enrollmentDetails = null;
            $conversationDetails = null;
            
            $user = Auth::guard('sanctum')->user();
            if ($user) {
                $enrollment = UserEnrollment::where('user_id', $user->id)
                    ->where('enrollable_type', Course::class)
                    ->where('enrollable_id', $course->id)
                    ->whereIn('enrollment_status', ['active', 'completed'])
                    ->first();
                
                if ($enrollment) {
                    $isEnrolled = true;
                    
                    // Get enrollment details
                    $enrollmentDetails = [
                        'id' => $enrollment->id,
                        'enrollment_status' => $enrollment->enrollment_status,
                        'enrolled_at' => $enrollment->enrolled_at ? $enrollment->enrolled_at->format('Y-m-d H:i:s') : null,
                        'started_at' => $enrollment->started_at ? $enrollment->started_at->format('Y-m-d H:i:s') : null,
                        'completed_at' => $enrollment->completed_at ? $enrollment->completed_at->format('Y-m-d H:i:s') : null,
                        'progress_percentage' => round($enrollment->progress_percentage ?? 0, 2),
                        'amount' => round($enrollment->amount, 2),
                        'currency' => $enrollment->currency,
                        'payment_status' => $enrollment->payment_status,
                    ];
                    
                    // Get or create conversation with mentor
                    $conversation = Conversation::where('enrollment_id', $enrollment->id)
                        ->first();
                    
                    if ($conversation) {
                        $conversationDetails = [
                            'id' => $conversation->id,
                            'unique_code' => $conversation->unique_code,
                            'last_message_at' => $conversation->last_message_at ? $conversation->last_message_at->format('Y-m-d H:i:s') : null,
                        ];
                    }
                }
            }

            // Get course statistics
            $averageRating = $course->averageRating() ?? 0;
            $totalReviews = $course->totalReviews();
            $enrollmentCount = $course->enrolledStudentsCount();
            $currentlyEnrolledCount = $course->currentlyEnrolledCount();
            $totalIncome = $course->totalIncome();

            // Get review statistics
            $reviewStats = $this->getReviewStatistics($course);

            // Get enrolled students (paginated)
            $enrolledStudents = $course->enrolledStudents(10);

            // Format enrolled students data
            $enrolledStudentsData = $enrolledStudents->map(function ($enrollment) use ($course) {
                $enrollmentDate = $enrollment->enrolled_at ?: $enrollment->created_at;
                $courseDuration = $course->duration_days * 24 * 60; // Convert to minutes
                $elapsedMinutes = $enrollmentDate->diffInMinutes(now());
                $remainingMinutes = max(0, $courseDuration - $elapsedMinutes);
                
                if ($remainingMinutes > 0) {
                    $days = floor($remainingMinutes / (24 * 60));
                    $hours = floor(($remainingMinutes % (24 * 60)) / 60);
                    $minutes = $remainingMinutes % 60;
                    
                    $durationParts = [];
                    if ($days > 0) $durationParts[] = $days . 'd';
                    if ($hours > 0) $durationParts[] = $hours . 'h';
                    if ($minutes > 0) $durationParts[] = $minutes . 'm';
                    
                    $durationText = implode(' ', $durationParts);
                } else {
                    $durationText = 'Expired';
                }

                return [
                    'id' => $enrollment->id,
                    'user_id' => $enrollment->user_id,
                    'user_name' => $enrollment->user->name,
                    'enrolled_at' => $enrollment->enrolled_at ? $enrollment->enrolled_at->format('F d, Y g:i A') : $enrollment->created_at->format('F d, Y g:i A'),
                    'enrollment_status' => $enrollment->enrollment_status,
                    'duration_left' => $durationText,
                    'created_at' => $enrollment->created_at->toISOString(),
                    'updated_at' => $enrollment->updated_at->toISOString(),
                ];
            });

            // Format recent reviews
            $recentReviews = $course->reviews->take(5)->map(function ($review) {
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

            // Check if user can review (is enrolled and hasn't reviewed yet)
            $canReview = false;
            $existingReview = null;
            if ($user && $isEnrolled) {
                $existingReview = $user->reviews()->where('course_id', $course->id)->first();
                $canReview = !$existingReview;
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

            $courseData = [
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
                'type' => 'Online',
                'created_at' => $course->created_at->toISOString(),
                'updated_at' => $course->updated_at->toISOString(),
                
                // Statistics
                'statistics' => [
                    'average_rating' => round($averageRating, 1),
                    'total_reviews' => $totalReviews,
                    'enrollment_count' => $enrollmentCount,
                    'currently_enrolled_count' => $currentlyEnrolledCount,
                    'total_income' => $totalIncome,
                ],

                // Mentor information
                'mentor' => [
                    'id' => $course->mentor->id,
                    'name' => $course->mentor->user->name,
                    'photo' => $course->mentor->photo ? asset('storage/' . $course->mentor->photo) : null,
                    'bio' => $course->mentor->bio,
                    'work_experience' => $course->mentor->work_experience,
                ],

                // Category information
                'category' => [
                    'id' => $course->category->id,
                    'name' => $course->category->name,
                ],

                // Sub-categories
                'sub_categories' => $course->subCategories->map(function ($subCategory) {
                    return [
                        'id' => $subCategory->id,
                        'name' => $subCategory->name,
                    ];
                }),

                // Key points - Categories and Subcategories
                'key_points' => array_merge(
                    [$course->category->name],
                    $course->subCategories->pluck('name')->toArray()
                ),

                // User enrollment status
                'is_enrolled' => $isEnrolled,
                'enrollment_details' => $enrollmentDetails,
                'conversation' => $conversationDetails,
                'can_review' => $canReview,
                'existing_review' => $existingReview,

                // Review statistics
                'review_statistics' => $reviewStats,

                // Recent reviews
                'recent_reviews' => $recentReviews,

                // Enrolled students (for mentors/admins)
                'enrolled_students' => [
                    'data' => $enrolledStudentsData,
                    'pagination' => [
                        'current_page' => $enrolledStudents->currentPage(),
                        'last_page' => $enrolledStudents->lastPage(),
                        'per_page' => $enrolledStudents->perPage(),
                        'total' => $enrolledStudents->total(),
                        'has_more_pages' => $enrolledStudents->hasMorePages(),
                    ]
                ],
            ];

            return response()->json([
                'success' => true,
                'message' => 'Course details retrieved successfully',
                'data' => $courseData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve course details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get course reviews with pagination
     *
     * @param Course $course
     * @param Request $request
     * @return JsonResponse
     */
    public function getCourseReviews(Course $course, Request $request): JsonResponse
    {
        try {
            // Only show approved courses
            if (!$course->isApproved()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course not found',
                    'error' => 'Course is not approved or does not exist'
                ], 404);
            }

            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);

            $reviews = Review::where('course_id', $course->id)
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
                'message' => 'Course reviews retrieved successfully',
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
                'message' => 'Failed to retrieve course reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get enrolled students for a course (mentor/admin only)
     *
     * @param Course $course
     * @param Request $request
     * @return JsonResponse
     */
    public function getEnrolledStudents(Course $course, Request $request): JsonResponse
    {
        try {
            // Check if user is mentor of this course or admin
            if (Auth::check()) {
                $user = Auth::user();
                $isMentor = $user->mentor && $user->mentor->id == $course->mentor_id;
                $isAdmin = $user->isAdmin();
                
                if (!$isMentor && !$isAdmin) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized',
                        'error' => 'Only the course mentor or admin can view enrolled students'
                    ], 403);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                    'error' => 'Authentication required'
                ], 401);
            }

            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);
            $status = $request->get('status', 'all'); // all, active, inactive

            $query = UserEnrollment::where('enrollable_type', Course::class)
                ->where('enrollable_id', $course->id)
                ->with('user');

            if ($status !== 'all') {
                $query->where('enrollment_status', $status);
            }

            $enrolledStudents = $query->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            $enrolledStudentsData = $enrolledStudents->map(function ($enrollment) use ($course) {
                $enrollmentDate = $enrollment->enrolled_at ?: $enrollment->created_at;
                $courseDuration = $course->duration_days * 24 * 60; // Convert to minutes
                $elapsedMinutes = $enrollmentDate->diffInMinutes(now());
                $remainingMinutes = max(0, $courseDuration - $elapsedMinutes);
                
                if ($remainingMinutes > 0) {
                    $days = floor($remainingMinutes / (24 * 60));
                    $hours = floor(($remainingMinutes % (24 * 60)) / 60);
                    $minutes = $remainingMinutes % 60;
                    
                    $durationParts = [];
                    if ($days > 0) $durationParts[] = $days . 'd';
                    if ($hours > 0) $durationParts[] = $hours . 'h';
                    if ($minutes > 0) $durationParts[] = $minutes . 'm';
                    
                    $durationText = implode(' ', $durationParts);
                } else {
                    $durationText = 'Expired';
                }

                return [
                    'id' => $enrollment->id,
                    'user_id' => $enrollment->user_id,
                    'user_name' => $enrollment->user->name,
                    'enrolled_at' => $enrollment->enrolled_at ? $enrollment->enrolled_at->format('F d, Y g:i A') : $enrollment->created_at->format('F d, Y g:i A'),
                    'enrollment_status' => $enrollment->enrollment_status,
                    'duration_left' => $durationText,
                    'created_at' => $enrollment->created_at->toISOString(),
                    'updated_at' => $enrollment->updated_at->toISOString(),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Enrolled students retrieved successfully',
                'data' => $enrolledStudentsData,
                'pagination' => [
                    'current_page' => $enrolledStudents->currentPage(),
                    'last_page' => $enrolledStudents->lastPage(),
                    'per_page' => $enrolledStudents->perPage(),
                    'total' => $enrolledStudents->total(),
                    'has_more_pages' => $enrolledStudents->hasMorePages(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve enrolled students',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get course statistics (mentor/admin only)
     *
     * @param Course $course
     * @return JsonResponse
     */
    public function getCourseStatistics(Course $course): JsonResponse
    {
        try {
            // Check if user is mentor of this course or admin
            if (Auth::ch===k()) {
                $user = Auth::user();
                $isMentor = $user->mentor && $user->mentor->id == $course->mentor_id;
                $isAdmin = $user->isAdmin();
                
                if (!$isMentor && !$isAdmin) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized',
                        'error' => 'Only the course mentor or admin can view course statistics'
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
                'enrollment_count' => $course->enrolledStudentsCount(),
                'currently_enrolled_count' => $course->currentlyEnrolledCount(),
                'total_income' => $course->totalIncome(),
                'average_rating' => round($course->averageRating() ?? 0, 1),
                'total_reviews' => $course->totalReviews(),
                'review_statistics' => $this->getReviewStatistics($course),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Course statistics retrieved successfully',
                'data' => $statistics,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve course statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get review statistics for a course
     *
     * @param Course $course
     * @return array
     */
    private function getReviewStatistics(Course $course): array
    {
        $reviews = $course->reviews;
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
