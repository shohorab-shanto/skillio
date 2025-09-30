<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mentor;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MentorApiController extends Controller
{
    /**
     * Get all mentors with filtering and pagination
     */
    public function getAllMentors(Request $request): JsonResponse
    {
        try {
            $query = Mentor::with(['user'])
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->withCount('approvedCourses');

            // Apply filters
            if ($request->has('verified') && $request->verified !== null) {
                if ($request->verified) {
                    $query->verified();
                }
            }

            if ($request->has('available') && $request->available !== null) {
                if ($request->available) {
                    $query->available();
                }
            }

            if ($request->has('min_rating') && $request->min_rating) {
                $query->having('reviews_avg_rating', '>=', $request->min_rating);
            }

            if ($request->has('min_courses') && $request->min_courses) {
                $query->having('approved_courses_count', '>=', $request->min_courses);
            }

            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('bio', 'like', "%{$search}%")
                      ->orWhere('work_experience', 'like', "%{$search}%")
                      ->orWhereHas('user', function($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%");
                      });
                });
            }

            if ($request->has('category_id') && $request->category_id) {
                $query->whereHas('courses', function($courseQuery) use ($request) {
                    $courseQuery->where('category_id', $request->category_id)
                               ->where('status', 'approved');
                });
            }

            if ($request->has('type') && $request->type) {
                $query->where('type', $request->type);
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            switch ($sortBy) {
                case 'rating':
                    $query->orderBy('reviews_avg_rating', $sortOrder);
                    break;
                case 'reviews':
                    $query->orderBy('reviews_count', $sortOrder);
                    break;
                case 'courses':
                    $query->orderBy('approved_courses_count', $sortOrder);
                    break;
                case 'name':
                    $query->join('users', 'mentors.user_id', '=', 'users.id')
                          ->orderBy('users.name', $sortOrder)
                          ->select('mentors.*');
                    break;
                case 'popular':
                    $query->orderBy('reviews_count', 'desc')
                          ->orderBy('reviews_avg_rating', 'desc');
                    break;
                case 'verified':
                    $query->orderBy('verified', 'desc')
                          ->orderBy('reviews_avg_rating', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', $sortOrder);
            }

            // Pagination
            $perPage = $request->get('per_page', 12);
            $mentors = $query->paginate($perPage);

            // Transform the data
            $mentors->getCollection()->transform(function ($mentor) {
                return [
                    'id' => $mentor->user_id,
                    'name' => $mentor->user->name,
                    'email' => $mentor->user->email,
                    'bio' => $mentor->bio,
                    'photo' => $mentor->photo ? asset('storage/' . $mentor->photo) : null,
                    'work_experience' => $mentor->work_experience,
                    'certifications' => $mentor->certifications ?? [],
                    'availability' => $mentor->availability,
                    'working_hours' => $mentor->working_hours ?? [],
                    'verified' => $mentor->verified,
                    'type' => $mentor->type,
                    'average_rating' => round($mentor->reviews_avg_rating ?? 0, 1),
                    'total_reviews' => $mentor->reviews_count,
                    'total_courses' => $mentor->approved_courses_count,
                    'star_rating' => $this->getStarRating($mentor->reviews_avg_rating ?? 0),
                    'five_star_percentage' => $this->getFiveStarPercentage($mentor),
                    'has_excellent_reviews' => ($mentor->reviews_avg_rating ?? 0) >= 4.0,
                    'profile_summary' => [
                        'name' => $mentor->user->name,
                        'verified' => $mentor->verified,
                        'available' => $mentor->isAvailable(),
                        'total_courses' => $mentor->approved_courses_count,
                        'total_reviews' => $mentor->reviews_count,
                        'average_rating' => round($mentor->reviews_avg_rating ?? 0, 1),
                    ],
                    'created_at' => $mentor->created_at->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Mentors retrieved successfully',
                'data' => $mentors,
                'filters' => [
                    'verified' => $request->verified,
                    'available' => $request->available,
                    'min_rating' => $request->min_rating,
                    'min_courses' => $request->min_courses,
                    'search' => $request->search,
                    'category_id' => $request->category_id,
                    'type' => $request->type,
                    'sort_by' => $sortBy,
                    'sort_order' => $sortOrder,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve mentors',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get filter options for mentors
     */
    public function getFilterOptions(): JsonResponse
    {
        try {
            $categories = Category::all();
            $ratingRange = Mentor::selectRaw('MIN(reviews_avg_rating) as min_rating, MAX(reviews_avg_rating) as max_rating')
                ->whereHas('reviews')
                ->first();
            
            $courseCountRange = Mentor::selectRaw('MIN(approved_courses_count) as min_courses, MAX(approved_courses_count) as max_courses')
                ->whereHas('courses', function($query) {
                    $query->where('status', 'approved');
                })
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Filter options retrieved successfully',
                'data' => [
                    'categories' => $categories->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                        ];
                    }),
                    'rating_range' => [
                        'min' => round($ratingRange->min_rating ?? 0, 1),
                        'max' => round($ratingRange->max_rating ?? 5, 1),
                    ],
                    'course_count_range' => [
                        'min' => $courseCountRange->min_courses ?? 0,
                        'max' => $courseCountRange->max_courses ?? 100,
                    ],
                    'types' => [
                        ['value' => 'individual', 'label' => 'Individual Mentor'],
                        ['value' => 'institution', 'label' => 'Institution'],
                    ],
                    'availability_options' => [
                        ['value' => 'available', 'label' => 'Available'],
                        ['value' => 'busy', 'label' => 'Busy'],
                        ['value' => 'unavailable', 'label' => 'Unavailable'],
                    ],
                    'sort_options' => [
                        ['value' => 'created_at', 'label' => 'Newest First'],
                        ['value' => 'rating', 'label' => 'Highest Rated'],
                        ['value' => 'reviews', 'label' => 'Most Reviewed'],
                        ['value' => 'courses', 'label' => 'Most Courses'],
                        ['value' => 'name', 'label' => 'Name A-Z'],
                        ['value' => 'popular', 'label' => 'Most Popular'],
                        ['value' => 'verified', 'label' => 'Verified First'],
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve filter options',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get star rating display
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
     * Get five star percentage
     */
    private function getFiveStarPercentage($mentor)
    {
        $totalReviews = $mentor->reviews_count;
        if ($totalReviews == 0) return 0;
        
        $fiveStarReviews = $mentor->reviews()->where('rating', 5)->count();
        return round(($fiveStarReviews / $totalReviews) * 100, 1);
    }
}
