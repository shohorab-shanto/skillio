<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mentor;

class MentorSessionController extends Controller
{
    /**
     * Display the specified mentor's sessions page.
     *
     * @param  \App\Models\Mentor  $mentor
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show(Request $request, Mentor $mentor)
    {
        // Get mentor data with user info, reviews, and session bookings
        $mentorData = Mentor::with(['user', 'reviews', 'sessionBookings.category', 'sessionBookings.subCategories'])
            ->where('id', $mentor->id)
            ->first();

        if (!$mentorData) {
            abort(404);
        }

        // Calculate rating and reviews
        $totalReviews = $mentorData->totalReviews();
        $averageRating = $mentorData->averageRating() ?? 0;
        
        // Get top category and its sub-categories
        $topCategory = $mentorData->sessionBookings
            ->pluck('category.name')
            ->unique()
            ->first();
        
        $topCategorySubCategories = [];
        if ($topCategory) {
            $topCategorySubCategories = $mentorData->sessionBookings
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

        // Build query for active session bookings with date filtering
        $sessionsQuery = $mentorData->sessionBookings()
            ->with(['category', 'subCategories', 'mentor.user'])
            ->where('status', 'active');

        // Apply date range filter if provided
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            
            $sessionsQuery->whereBetween('date', [$startDate, $endDate]);
        }

        // Get paginated results with 9 items per page
        $activeSessions = $sessionsQuery
            ->orderBy('date')
            ->orderBy('start_time')
            ->paginate(9);

        // Prepare mentor data for view
        $mentorInfo = [
            'id' => $mentorData->id,
            'name' => $mentorData->user->name ?? 'Unknown',
            'photo' => $mentorData->photo?? null,
            'role' => $mentorData->work_experience,
            'bio' => $mentorData->bio,
            'experience_years' => $mentorData->work_experience,
            'total_reviews' => $totalReviews,
            'average_rating' => $averageRating,
            'formatted_rating' => number_format($averageRating, 1, '.', ''),
            'star_rating' => $this->getStarRating($averageRating),
            'top_category' => $topCategory,
            'top_category_sub_categories' => $topCategorySubCategories,
            'availability' => $mentorData->availability ?? 'available',
            'total_sessions' => $mentorData->sessionBookings->count(),
        ];

        return view('frontend.mentors.profile-and-sessions', compact('mentorInfo', 'activeSessions'));
    }

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
        ];
    }
}
