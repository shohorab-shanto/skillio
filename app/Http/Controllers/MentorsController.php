<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mentor;
use App\Models\User;

class MentorsController extends Controller
{
    /**
     * Show the mentors page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
        public function index(Request $request)
    {
        $query = Mentor::with(['user', 'reviews', 'sessionBookings.category', 'sessionBookings.subCategories'])
            ->verified();

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('user', function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%");
            })
            ->orWhereHas('sessionBookings.category', function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%");
            })
            ->orWhereHas('sessionBookings.subCategories', function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%");
            });
        }

        // Get mentors with pagination (12 per page)
        $mentors = $query->get()
            ->map(function ($mentor) {
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
                            return $session->category->name === $topCategory;
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
                    'photo' => $mentor->photo?? null,
                    'top_category' => $topCategory,
                    'top_category_sub_categories' => $topCategorySubCategories,
                    'bio' => $mentor->bio ?? '',
                    'total_reviews' => $totalReviews,
                    'average_rating' => $averageRating,
                    'formatted_rating' => number_format($averageRating, 1),
                    'star_rating' => $this->getStarRating($averageRating),
                ];
            })
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
}
