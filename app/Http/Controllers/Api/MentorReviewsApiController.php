<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mentor;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MentorReviewsApiController extends Controller
{
    /**
     * Display a listing of the mentor's reviews
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $mentor = $user->mentor;

        if (!$mentor) {
            return response()->json([
                'success' => false,
                'message' => 'Mentor profile not found.'
            ], 404);
        }

        // Get reviews with pagination
        $query = $mentor->reviews()
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
            ->orderBy('created_at', 'desc');

        $perPage = $request->get('per_page', 10);
        $reviews = $query->paginate($perPage);

        // Transform reviews for API response
        $reviews->getCollection()->transform(function ($review) {
            return [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'user' => [
                    'id' => $review->user->id,
                    'name' => $review->user->name,
                    'photo' => $review->user->photo ? asset('storage/' . $review->user->photo) : null,
                ],
                'reviewable_type' => class_basename($review->reviewable_type),
                'reviewable_id' => $review->reviewable_id,
                'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $review->updated_at->format('Y-m-d H:i:s'),
            ];
        });

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

        return response()->json([
            'success' => true,
            'data' => [
                'reviews' => $reviews->items(),
                'pagination' => [
                    'current_page' => $reviews->currentPage(),
                    'last_page' => $reviews->lastPage(),
                    'per_page' => $reviews->perPage(),
                    'total' => $reviews->total(),
                ],
                'statistics' => $stats,
            ]
        ]);
    }

    /**
     * Display the specified review
     */
    public function show(Review $review)
    {
        $user = Auth::user();
        $mentor = $user->mentor;

        if (!$mentor) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. This endpoint is only available for mentors.'
            ], 403);
        }

        // Check if the review belongs to the current mentor
        if ($review->reviewable_type != 'App\Models\Mentor' || $review->reviewable_id != $mentor->id) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found or unauthorized access.'
            ], 404);
        }

        $review->load('user');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'user' => [
                    'id' => $review->user->id,
                    'name' => $review->user->name,
                    'photo' => $review->user->photo ? asset('storage/' . $review->user->photo) : null,
                    'email' => $review->user->email,
                ],
                'reviewable_type' => class_basename($review->reviewable_type),
                'reviewable_id' => $review->reviewable_id,
                'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $review->updated_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    /**
     * Get review statistics
     */
    public function statistics()
    {
        $user = Auth::user();
        $mentor = $user->mentor;

        if (!$mentor) {
            return response()->json([
                'success' => false,
                'message' => 'Mentor profile not found.'
            ], 404);
        }

        $stats = [
            'total_reviews' => $mentor->totalReviews(),
            'average_rating' => $mentor->averageRating(),
            'formatted_average' => $mentor->formatted_average_rating,
            'star_rating' => $mentor->star_rating,
            'rating_distribution' => $mentor->ratingDistribution(),
            'five_star_percentage' => $mentor->five_star_percentage,
            'has_excellent_reviews' => $mentor->hasExcellentReviews(),
        ];

        // Get rating breakdown by stars
        $ratingBreakdown = [];
        for ($i = 1; $i <= 5; $i++) {
            $count = $mentor->reviews()->where('rating', $i)->count();
            $ratingBreakdown[] = [
                'stars' => $i,
                'count' => $count,
                'percentage' => $mentor->totalReviews() > 0 ? round(($count / $mentor->totalReviews()) * 100, 1) : 0,
            ];
        }

        // Get recent reviews (last 5)
        $recentReviews = $mentor->reviews()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($review) {
                return [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'user' => [
                        'id' => $review->user->id,
                        'name' => $review->user->name,
                        'photo' => $review->user->photo ? asset('storage/' . $review->user->photo) : null,
                    ],
                    'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'statistics' => $stats,
                'rating_breakdown' => $ratingBreakdown,
                'recent_reviews' => $recentReviews,
            ]
        ]);
    }

    /**
     * Get reviews by rating filter
     */
    public function getByRating(Request $request, $rating)
    {
        $user = Auth::user();
        $mentor = $user->mentor;

        if (!$mentor) {
            return response()->json([
                'success' => false,
                'message' => 'Mentor profile not found.'
            ], 404);
        }

        if ($rating < 1 || $rating > 5) {
            return response()->json([
                'success' => false,
                'message' => 'Rating must be between 1 and 5.'
            ], 400);
        }

        $perPage = $request->get('per_page', 10);
        $reviews = $mentor->reviews()
            ->with('user')
            ->where('rating', $rating)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Transform reviews for API response
        $reviews->getCollection()->transform(function ($review) {
            return [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'user' => [
                    'id' => $review->user->id,
                    'name' => $review->user->name,
                    'photo' => $review->user->photo ? asset('storage/' . $review->user->photo) : null,
                ],
                'created_at' => $review->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'rating' => $rating,
                'reviews' => $reviews->items(),
                'pagination' => [
                    'current_page' => $reviews->currentPage(),
                    'last_page' => $reviews->lastPage(),
                    'per_page' => $reviews->perPage(),
                    'total' => $reviews->total(),
                ],
            ]
        ]);
    }
}
