<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserEnrollment;
use App\Models\Course;
use App\Models\Category;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserCoursesApiController extends Controller
{
    /**
     * Display user's enrolled courses
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Base query for user's course enrollments
        $query = UserEnrollment::with([
            'enrollable' => function($query) {
                $query->with(['mentor.user', 'category', 'subCategories', 'reviews']);
            }
        ])
        ->where('user_id', $user->id)
        ->where('enrollable_type', Course::class)
        ->whereIn('enrollment_status', ['active', 'completed']);

        // Apply filters using whereExists to avoid polymorphic issues
        if ($request->filled('category')) {
            $query->whereExists(function($q) use ($request) {
                $q->select(DB::raw(1))
                  ->from('courses')
                  ->whereColumn('courses.id', 'user_enrollments.enrollable_id')
                  ->where('courses.category_id', $request->category);
            });
        }

        if ($request->filled('status')) {
            if ($request->status == 'active') {
                $query->where('enrollment_status', 'active');
            } elseif ($request->status == 'completed') {
                $query->where('enrollment_status', 'completed');
            } else {
                $query->where('enrollment_status', $request->status);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereExists(function($q) use ($search) {
                $q->select(DB::raw(1))
                  ->from('courses')
                  ->whereColumn('courses.id', 'user_enrollments.enrollable_id')
                  ->where(function($courseQuery) use ($search) {
                      $courseQuery->where('courses.title', 'like', '%' . $search . '%')
                                  ->orWhere('courses.description', 'like', '%' . $search . '%');
                  });
            });
        }

        // Get paginated results
        $perPage = $request->get('per_page', 9);
        $enrollments = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Add conversation data to each enrollment
        $enrollments->getCollection()->transform(function ($enrollment) use ($user) {
            $conversation = Conversation::where('mentor_id', $enrollment->enrollable->mentor->id)
                ->where('user_id', $user->id)
                ->first();
            
            $course = $enrollment->enrollable;
            $mentor = $course->mentor->user;
            
            return [
                'id' => $enrollment->id,
                'course' => [
                    'id' => $course->id,
                    'title' => $course->title,
                    'description' => $course->description,
                    'thumbnail' => $course->thumbnail ? asset('storage/' . $course->thumbnail) : null,
                    'cover_photo' => $course->cover_photo ? asset('storage/' . $course->cover_photo) : null,
                    'price' => round($course->price, 2),
                    'discount' => round($course->discount, 2),
                    'final_price' => round($course->finalPrice, 2),
                    'type' => 'Online',
                    'average_rating' => round($course->averageRating(), 1),
                    'reviews_count' => $course->reviews->count(),
                    'enrolled_students_count' => $course->enrolledStudentsCount(),
                    'category' => [
                        'id' => $course->category->id,
                        'name' => $course->category->name,
                    ],
                    'sub_categories' => $course->subCategories->map(function($subCategory) {
                        return [
                            'id' => $subCategory->id,
                            'name' => $subCategory->name,
                        ];
                    }),
                    'duration_days' => $course->duration_days,
                    'start_date' => $course->start_date ? $course->start_date->format('Y-m-d') : null,
                    'end_date' => $course->end_date ? $course->end_date->format('Y-m-d') : null,
                ],
                'mentor' => [
                    'id' => $mentor->id,
                    'name' => $mentor->name,
                    'photo' => $mentor->photo ? asset('storage/' . $mentor->photo) : null,
                ],
                'enrollment' => [
                    'status' => $enrollment->enrollment_status,
                    'progress_percentage' => round($enrollment->progress_percentage, 2),
                    'enrolled_at' => $enrollment->enrolled_at ? $enrollment->enrolled_at->format('Y-m-d H:i:s') : null,
                    'started_at' => $enrollment->started_at ? $enrollment->started_at->format('Y-m-d H:i:s') : null,
                    'completed_at' => $enrollment->completed_at ? $enrollment->completed_at->format('Y-m-d H:i:s') : null,
                    'last_accessed_at' => $enrollment->last_accessed_at ? $enrollment->last_accessed_at->format('Y-m-d H:i:s') : null,
                ],
                'conversation' => $conversation ? [
                    'id' => $conversation->id,
                    'unique_code' => $conversation->unique_code,
                ] : null,
            ];
        });

        // Get statistics
        $stats = [
            'total_courses' => UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', Course::class)
                ->whereIn('enrollment_status', ['active', 'completed'])
                ->count(),
            'active_courses' => UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', Course::class)
                ->where('enrollment_status', 'active')
                ->count(),
            'completed_courses' => UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', Course::class)
                ->where('enrollment_status', 'completed')
                ->count(),
            'total_spent_courses' => UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', Course::class)
                ->whereHas('paymentTransaction')
                ->with('paymentTransaction')
                ->get()
                ->sum(function($enrollment) {
                    return $enrollment->paymentTransaction->gross_amount ?? 0;
                })
        ];

        // Get all categories for filter dropdown
        $categories = Category::all()->map(function($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'enrollments' => $enrollments->items(),
                'pagination' => [
                    'current_page' => $enrollments->currentPage(),
                    'last_page' => $enrollments->lastPage(),
                    'per_page' => $enrollments->perPage(),
                    'total' => $enrollments->total(),
                ],
                'statistics' => $stats,
                'categories' => $categories,
            ]
        ]);
    }

    /**
     * Display specific enrolled course details
     */
    public function show(UserEnrollment $enrollment)
    {
        $user = Auth::user();
        
        // Ensure this enrollment belongs to the current user and is a course
        if ($enrollment->user_id != $user->id || $enrollment->enrollable_type != Course::class) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to course.'
            ], 403);
        }

        // Load relationships
        $enrollment->load([
            'enrollable' => function($query) {
                $query->with(['mentor.user', 'category', 'subCategories', 'reviews.user']);
            },
            'paymentTransaction'
        ]);

        // Get conversation between user and mentor
        $conversation = Conversation::where('mentor_id', $enrollment->enrollable->mentor->id)
            ->where('user_id', $user->id)
            ->first();

        $course = $enrollment->enrollable;
        $mentor = $course->mentor->user;

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $enrollment->id,
                'course' => [
                    'id' => $course->id,
                    'title' => $course->title,
                    'description' => $course->description,
                    'thumbnail' => $course->thumbnail ? asset('storage/' . $course->thumbnail) : null,
                    'cover_photo' => $course->cover_photo ? asset('storage/' . $course->cover_photo) : null,
                    'price' => round($course->price, 2),
                    'discount' => round($course->discount, 2),
                    'final_price' => round($course->finalPrice, 2),
                    'type' => 'Online',
                    'average_rating' => round($course->averageRating(), 1),
                    'reviews_count' => $course->reviews->count(),
                    'enrolled_students_count' => $course->enrolledStudentsCount(),
                    'category' => [
                        'id' => $course->category->id,
                        'name' => $course->category->name,
                    ],
                    'sub_categories' => $course->subCategories->map(function($subCategory) {
                        return [
                            'id' => $subCategory->id,
                            'name' => $subCategory->name,
                        ];
                    }),
                    'duration_days' => $course->duration_days,
                    'start_date' => $course->start_date ? $course->start_date->format('Y-m-d') : null,
                    'end_date' => $course->end_date ? $course->end_date->format('Y-m-d') : null,
                    'reviews' => $course->reviews->map(function($review) {
                        return [
                            'id' => $review->id,
                            'rating' => $review->rating,
                            'comment' => $review->comment,
                            'user' => [
                                'id' => $review->user->id,
                                'name' => $review->user->name,
                            ],
                            'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                        ];
                    }),
                ],
                'mentor' => [
                    'id' => $mentor->id,
                    'name' => $mentor->name,
                    'photo' => $mentor->photo ? asset('storage/' . $mentor->photo) : null,
                ],
                'enrollment' => [
                    'status' => $enrollment->enrollment_status,
                    'progress_percentage' => round($enrollment->progress_percentage, 2),
                    'enrolled_at' => $enrollment->enrolled_at ? $enrollment->enrolled_at->format('Y-m-d H:i:s') : null,
                    'started_at' => $enrollment->started_at ? $enrollment->started_at->format('Y-m-d H:i:s') : null,
                    'completed_at' => $enrollment->completed_at ? $enrollment->completed_at->format('Y-m-d H:i:s') : null,
                    'last_accessed_at' => $enrollment->last_accessed_at ? $enrollment->last_accessed_at->format('Y-m-d H:i:s') : null,
                    'amount' => round($enrollment->amount, 2),
                    'currency' => $enrollment->currency,
                    'payment_status' => $enrollment->payment_status,
                ],
                'conversation' => $conversation ? [
                    'id' => $conversation->id,
                    'unique_code' => $conversation->unique_code,
                ] : null,
            ]
        ]);
    }

    /**
     * Get course statistics
     */
    public function statistics(Request $request)
    {
        $user = Auth::user();
        
        $stats = [
            'total_courses' => UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', Course::class)
                ->whereIn('enrollment_status', ['active', 'completed'])
                ->count(),
            'active_courses' => UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', Course::class)
                ->where('enrollment_status', 'active')
                ->count(),
            'completed_courses' => UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', Course::class)
                ->where('enrollment_status', 'completed')
                ->count(),
            'total_spent_courses' => UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', Course::class)
                ->whereHas('paymentTransaction')
                ->with('paymentTransaction')
                ->get()
                ->sum(function($enrollment) {
                    return $enrollment->paymentTransaction->gross_amount ?? 0;
                })
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
