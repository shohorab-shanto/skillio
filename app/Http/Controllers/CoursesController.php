<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Mentor;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CoursesController extends Controller
{
    /**
     * Show the mentors page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $query = Course::with(['mentor.user', 'category', 'subCategories', 'reviews'])
                      ->approved(); // Only show approved courses
        
        // Category filtering
        if ($request->filled('category')) {
            $query->where('category_id', $request->get('category'));
        }
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhereHas('category', function ($categoryQuery) use ($search) {
                      $categoryQuery->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('mentor', function ($mentorQuery) use ($search) {
                      $mentorQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        }
        
        // Order by: 1) Average rating (DESC), 2) Rating count (DESC), 3) Created at (DESC)
        $query->leftJoin(\DB::raw('(SELECT course_id, AVG(rating) as avg_rating, COUNT(id) as review_count FROM reviews GROUP BY course_id) as review_stats'), 
                         'courses.id', '=', 'review_stats.course_id')
              ->select('courses.*', 'review_stats.avg_rating', 'review_stats.review_count')
              ->orderByRaw('CASE WHEN review_stats.avg_rating IS NULL THEN 1 ELSE 0 END')
              ->orderBy('review_stats.avg_rating', 'desc')
              ->orderByRaw('CASE WHEN review_stats.review_count IS NULL THEN 1 ELSE 0 END')
              ->orderBy('review_stats.review_count', 'desc')
              ->orderBy('courses.created_at', 'desc');
        
        $courses = $query->paginate(12);
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
        // Load all necessary relationships
        $course->load(['mentor', 'category', 'subCategories', 'reviews.user']);
        
        // Only show approved courses to public
        if (!$course->isApproved()) {
            abort(404, 'Course not found');
        }
        
        return view('frontend.courses.show', compact('course'));
    }

}
