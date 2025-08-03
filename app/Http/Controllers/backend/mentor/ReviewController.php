<?php

namespace App\Http\Controllers\backend\mentor;

use App\Http\Controllers\Controller;
use App\Models\Mentor;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display a listing of the mentor's reviews.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $mentor = $user->mentor;

        if (!$mentor) {
            return redirect()->route('mentor.dashboard')->with('error', 'Mentor profile not found.');
        }

        // Get reviews with pagination
        $reviews = $mentor->reviews()
            ->with('user')
            ->when($request->rating, function ($query, $rating) {
                return $query->where('rating', $rating);
            })
            ->when($request->search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('comment', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get review statistics
        $stats = [
            'total_reviews' => $mentor->totalReviews(),
            'average_rating' => $mentor->averageRating(),
            'formatted_average' => $mentor->formatted_average_rating,
            'star_rating' => $mentor->star_rating,
            'rating_distribution' => $mentor->ratingDistribution(),
            'five_star_percentage' => $mentor->five_star_percentage,
            'has_excellent_reviews' => $mentor->hasExcellentReviews(),
        ];

        return view('backend.mentor.reviews.index', compact('mentor', 'reviews', 'stats', 'request'));
    }
}
