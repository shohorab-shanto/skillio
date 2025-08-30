<?php

namespace App\Http\Controllers\backend\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserEnrollment;
use App\Models\Course;
use App\Models\Category;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserCoursesController extends Controller
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
        $enrollments = $query->orderBy('created_at', 'desc')->paginate(9);

        // Add conversation data to each enrollment
        foreach ($enrollments as $enrollment) {
            $conversation = Conversation::where('mentor_id', $enrollment->enrollable->mentor->id)
                ->where('user_id', $user->id)
                ->first();
            $enrollment->conversation = $conversation;
        }

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
        $categories = Category::all();

        return view('backend.user.courses.index', compact('enrollments', 'stats', 'categories'));
    }

    /**
     * Display specific enrolled course details
     */
    public function show(UserEnrollment $enrollment)
    {
        $user = Auth::user();
        
        // Ensure this enrollment belongs to the current user and is a course
        if ($enrollment->user_id != $user->id || $enrollment->enrollable_type != Course::class) {
            abort(403, 'Unauthorized access to course.');
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

        return view('backend.user.courses.show', compact('enrollment', 'conversation'));
    }
}
