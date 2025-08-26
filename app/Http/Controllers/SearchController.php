<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Mentor;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /**
     * Search for courses and session bookings
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $results = collect();

        // Search in Courses
        $courses = Course::with(['mentor.user', 'category', 'subCategories'])
            ->where('status', 'approved')
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhereHas('category', function($catQ) use ($query) {
                      $catQ->where('name', 'like', "%{$query}%");
                  })
                  ->orWhereHas('subCategories', function($subQ) use ($query) {
                      $subQ->where('name', 'like', "%{$query}%");
                  });
            })
            ->limit(5)
            ->get()
            ->map(function($course) {
                return [
                    'id' => $course->id,
                    'type' => 'course',
                    'title' => $course->title,
                    'category' => $course->category->name ?? 'N/A',
                    'sub_categories' => $course->subCategories->pluck('name')->implode(', '),
                    'mentor' => $course->mentor->user->name ?? 'N/A',
                    'thumbnail' => $course->thumbnail,
                    'url' => route('courses.show', $course->id),
                    'rating' => $course->averageRating(),
                    'reviews_count' => $course->totalReviews(),
                    'price' => $course->price,
                    'discount' => $course->discount
                ];
            });

        // Search in Mentors
        $mentors = Mentor::with(['user', 'reviews', 'sessionBookings.category', 'sessionBookings.subCategories'])
            ->where('availability', 'available')
            ->where(function($q) use ($query) {
                $q->whereHas('user', function($userQ) use ($query) {
                    $userQ->where('name', 'like', "%{$query}%");
                })
                  ->orWhere('bio', 'like', "%{$query}%")
                  ->orWhere('work_experience', 'like', "%{$query}%")
                  ->orWhereHas('sessionBookings.category', function($catQ) use ($query) {
                      $catQ->where('name', 'like', "%{$query}%");
                  })
                  ->orWhereHas('sessionBookings.subCategories', function($subQ) use ($query) {
                      $subQ->where('name', 'like', "%{$query}%");
                  });
            })
            ->limit(5)
            ->get()
            ->map(function($mentor) {
                return [
                    'id' => $mentor->id,
                    'type' => 'mentor',
                    'title' => $mentor->user->name,
                    'category' => $mentor->sessionBookings->pluck('category.name')->unique()->first() ?? 'N/A',
                    'sub_categories' => $mentor->sessionBookings->flatMap->subCategories->pluck('name')->unique()->implode(', '),
                    'mentor' => $mentor->user->name,
                    'thumbnail' => $mentor->photo,
                    'url' => route('mentor.sessions', $mentor->id),
                    'rating' => $mentor->reviews()->avg('rating'),
                    'reviews_count' => $mentor->reviews()->count(),
                    'experience' => $mentor->work_experience,
                    'bio' => $mentor->bio
                ];
            });

        // Combine and sort results by relevance
        $results = $courses->concat($mentors)->take(8);

        return response()->json(['results' => $results]);
    }

    /**
     * Get search suggestions (categories and sub-categories)
     */
    public function suggestions(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json(['suggestions' => []]);
        }

        $suggestions = collect();

        // Get category suggestions
        $categories = Category::where('name', 'like', "%{$query}%")
            ->limit(3)
            ->get()
            ->map(function($category) {
                return [
                    'type' => 'category',
                    'name' => $category->name,
                    'url' => route('courses.index', ['category' => $category->id])
                ];
            });

        // Get sub-category suggestions
        $subCategories = SubCategory::where('name', 'like', "%{$query}%")
            ->limit(3)
            ->get()
            ->map(function($subCategory) {
                return [
                    'type' => 'sub_category',
                    'name' => $subCategory->name,
                    'category' => $subCategory->category->name ?? 'N/A',
                    'url' => route('courses.index', ['sub_category' => $subCategory->id])
                ];
            });

        $suggestions = $categories->concat($subCategories)->take(5);

        return response()->json(['suggestions' => $suggestions]);
    }
}
