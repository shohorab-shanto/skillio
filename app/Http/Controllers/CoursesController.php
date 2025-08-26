<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Mentor;
use App\Models\Category;
use App\Models\UserEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CoursesController extends Controller
{
    /**
     * Show the mentors page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $preferredCourses = collect();
        $regularCourses = collect();
        
        // Base query for all approved courses
        $baseQuery = Course::with(['mentor.user', 'category', 'subCategories', 'reviews'])
                      ->approved();
        
        // If user is logged in, try to get preference-matched courses first
        if (Auth::check()) {
            $user = Auth::user();
            $userPreferences = $this->getUserPreferences($user);
            
            if (!empty($userPreferences)) {
                $preferenceQuery = clone $baseQuery;
                
                // Apply category filter from preferences (if not overridden by request)
                if (!empty($userPreferences['categories']) && !$request->filled('category')) {
                    $preferenceQuery->whereIn('category_id', $userPreferences['categories']);
                }
                
                // Apply sub-category filter from preferences
                if (!empty($userPreferences['sub_categories'])) {
                    $preferenceQuery->whereHas('subCategories', function($q) use ($userPreferences) {
                        $q->whereIn('id', $userPreferences['sub_categories']);
                    });
                }
                
                // Apply mentor type filter from preferences
                if (!empty($userPreferences['mentor_type'])) {
                    $preferenceQuery->whereHas('mentor', function($q) use ($userPreferences) {
                        $q->where('type', $userPreferences['mentor_type']);
                    });
                }
                
                // Get preference-matched courses
                $preferredCourses = $preferenceQuery->get();
            }
        }
        
        // Get regular courses (excluding already selected preferred courses)
        $excludeIds = $preferredCourses->pluck('id')->toArray();
        
        $regularQuery = clone $baseQuery;
        if (!empty($excludeIds)) {
            $regularQuery->whereNotIn('id', $excludeIds);
        }
        
        // Apply category filtering from request
        if ($request->filled('category')) {
            $regularQuery->where('category_id', $request->get('category'));
        }
        
        // Apply search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $regularQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhereHas('category', function ($categoryQuery) use ($search) {
                      $categoryQuery->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('mentor.user', function ($mentorQuery) use ($search) {
                      $mentorQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Apply featured filter
        if ($request->filled('featured')) {
            $regularQuery->featured();
        }
        
        // Get regular courses with proper ordering
        $regularQuery->leftJoin(\DB::raw('(SELECT course_id, AVG(rating) as avg_rating, COUNT(id) as review_count FROM reviews GROUP BY course_id) as review_stats'), 
                               'courses.id', '=', 'review_stats.course_id')
                    ->select('courses.*', 'review_stats.avg_rating', 'review_stats.review_count')
                    ->orderByRaw('CASE WHEN review_stats.avg_rating IS NULL THEN 1 ELSE 0 END')
                    ->orderBy('review_stats.avg_rating', 'desc')
                    ->orderByRaw('CASE WHEN review_stats.review_count IS NULL THEN 1 ELSE 0 END')
                    ->orderBy('review_stats.review_count', 'desc')
                    ->orderBy('courses.created_at', 'desc');
        
        $regularCourses = $regularQuery->paginate(12);
        
        // Combine preferred and regular courses, with preferred ones first
        $allCourses = $preferredCourses->merge($regularCourses->items());
        
        // Create a custom paginator with the combined results
        $courses = new \Illuminate\Pagination\LengthAwarePaginator(
            $allCourses,
            $regularCourses->total() + $preferredCourses->count(),
            $regularCourses->perPage(),
            $regularCourses->currentPage(),
            [
                'path' => $regularCourses->path(),
                'pageName' => $regularCourses->getPageName(),
            ]
        );
        
        $categories = Category::all();
        
        return view('frontend.courses.index', compact('courses', 'categories'));
    }

    /**
     * Show a specific course details.
     *
     * @param Course $course
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show(Course $course)
    {
        // Load all necessary relationships with reviews ordered by latest first
        $course->load(['mentor.user', 'category', 'subCategories', 'reviews.user' => function($query) {
            $query->orderBy('created_at', 'desc');
        }]);
        
        // Only show approved courses to public
        if (!$course->isApproved()) {
            abort(404, 'Course not found');
        }
        
        // Check if current user is already enrolled in this course
        $isEnrolled = false;
        if (auth()->check()) {
            $isEnrolled = UserEnrollment::where('user_id', auth()->id())
                ->where('enrollable_type', Course::class)
                ->where('enrollable_id', $course->id)
                ->whereIn('enrollment_status', ['active', 'completed'])
                ->exists();
        }
        
        return view('frontend.courses.show', compact('course', 'isEnrolled'));
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
