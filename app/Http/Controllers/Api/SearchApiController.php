<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Mentor;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SearchApiController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        try {
            $query = trim($request->get('q', ''));
            $limit = (int) $request->get('limit', 8);
            $type = $request->get('type');
            $categoryId = $request->get('category_id');
            $minRating = $request->get('min_rating');
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            if (mb_strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'message' => 'No results. Query too short.',
                    'data' => [
                        'results' => [],
                        'query' => $query,
                    ],
                ]);
            }

            $coursesQuery = Course::with(['mentor.user', 'category', 'subCategories'])
                ->approved()
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhereHas('category', function ($catQ) use ($query) {
                          $catQ->where('name', 'like', "%{$query}%");
                      })
                      ->orWhereHas('subCategories', function ($subQ) use ($query) {
                          $subQ->where('name', 'like', "%{$query}%");
                      });
                });

            if ($categoryId) {
                $coursesQuery->where('category_id', $categoryId);
            }
            if ($minRating) {
                $coursesQuery->having('reviews_avg_rating', '>=', $minRating);
            }

            switch ($sortBy) {
                case 'rating':
                    $coursesQuery->orderBy('reviews_avg_rating', $sortOrder);
                    break;
                case 'reviews':
                    $coursesQuery->orderBy('reviews_count', $sortOrder);
                    break;
                case 'title':
                    $coursesQuery->orderBy('title', $sortOrder);
                    break;
                case 'popular':
                    $coursesQuery->orderBy('reviews_count', 'desc')->orderBy('reviews_avg_rating', 'desc');
                    break;
                default:
                    $coursesQuery->orderBy('created_at', $sortOrder);
            }

            $courses = $coursesQuery
                ->limit($type === 'course' ? $limit : 5)
                ->get()
                ->map(function ($course) {
                    return [
                        'id' => $course->id,
                        'type' => 'course',
                        'title' => $course->title,
                        'category' => $course->category->name ?? 'N/A',
                        'sub_categories' => $course->subCategories->pluck('name')->implode(', '),
                        'mentor' => $course->mentor->user->name ?? 'N/A',
                        'thumbnail' => $course->thumbnail_url,
                        'web_url' => route('courses.show', $course->id),
                        'average_rating' => round($course->reviews_avg_rating ?? ($course->averageRating() ?? 0), 1),
                        'reviews_count' => $course->reviews_count ?? $course->totalReviews(),
                        'price' => $course->price,
                        'discount' => $course->discount,
                    ];
                });

            $mentorsQuery = Mentor::with(['user', 'reviews', 'sessionBookings.category', 'sessionBookings.subCategories'])
                ->available()
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->where(function ($q) use ($query) {
                    $q->whereHas('user', function ($userQ) use ($query) {
                        $userQ->where('name', 'like', "%{$query}%");
                    })
                      ->orWhere('bio', 'like', "%{$query}%")
                      ->orWhere('work_experience', 'like', "%{$query}%")
                      ->orWhereHas('sessionBookings.category', function ($catQ) use ($query) {
                          $catQ->where('name', 'like', "%{$query}%");
                      })
                      ->orWhereHas('sessionBookings.subCategories', function ($subQ) use ($query) {
                          $subQ->where('name', 'like', "%{$query}%");
                      });
                });

            if ($categoryId) {
                $mentorsQuery->whereHas('sessionBookings', function ($q) use ($categoryId) {
                    $q->where('category_id', $categoryId);
                });
            }
            if ($minRating) {
                $mentorsQuery->having('reviews_avg_rating', '>=', $minRating);
            }

            switch ($sortBy) {
                case 'rating':
                    $mentorsQuery->orderBy('reviews_avg_rating', $sortOrder);
                    break;
                case 'reviews':
                    $mentorsQuery->orderBy('reviews_count', $sortOrder);
                    break;
                case 'name':
                    $mentorsQuery->join('users', 'mentors.user_id', '=', 'users.id')
                                 ->orderBy('users.name', $sortOrder)
                                 ->select('mentors.*');
                    break;
                case 'popular':
                    $mentorsQuery->orderBy('reviews_count', 'desc')->orderBy('reviews_avg_rating', 'desc');
                    break;
                default:
                    $mentorsQuery->orderBy('created_at', $sortOrder);
            }

            $mentors = $mentorsQuery
                ->limit($type === 'mentor' ? $limit : 5)
                ->get()
                ->map(function ($mentor) {
                    return [
                        'id' => $mentor->id,
                        'type' => 'mentor',
                        'title' => $mentor->user->name,
                        'category' => $mentor->sessionBookings->pluck('category.name')->unique()->first() ?? 'N/A',
                        'sub_categories' => $mentor->sessionBookings->flatMap->subCategories->pluck('name')->unique()->implode(', '),
                        'mentor' => $mentor->user->name,
                        'photo' => $mentor->photo ? asset('storage/' . $mentor->photo) : null,
                        'web_url' => route('mentor.sessions', $mentor->id),
                        'average_rating' => round($mentor->reviews_avg_rating ?? ($mentor->averageRating() ?? 0), 1),
                        'reviews_count' => $mentor->reviews_count ?? $mentor->reviews()->count(),
                        'experience' => $mentor->work_experience,
                        'bio' => $mentor->bio,
                    ];
                });

            if ($type === 'course') {
                $results = $courses->take($limit)->values();
            } elseif ($type === 'mentor') {
                $results = $mentors->take($limit)->values();
            } else {
                $results = $courses->concat($mentors)->take($limit)->values();
            }

            return response()->json([
                'success' => true,
                'message' => 'Search results retrieved successfully',
                'data' => [
                    'results' => $results,
                    'query' => $query,
                ],
                'counts' => [
                    'courses' => $courses->count(),
                    'mentors' => $mentors->count(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform search',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    
    public function suggestions(Request $request): JsonResponse
    {
        try {
            $query = trim($request->get('q', ''));
            $limit = (int) $request->get('limit', 5);

            if (mb_strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'message' => 'No suggestions. Query too short.',
                    'data' => [
                        'suggestions' => [],
                        'query' => $query,
                    ],
                ]);
            }

            $categories = Category::where('name', 'like', "%{$query}%")
                ->limit($limit)
                ->get()
                ->map(function ($category) {
                    return [
                        'type' => 'category',
                        'id' => $category->id,
                        'name' => $category->name,
                        'web_url' => route('courses.index', ['category' => $category->id]),
                    ];
                });

            $subCategories = SubCategory::with('category')
                ->where('name', 'like', "%{$query}%")
                ->limit($limit)
                ->get()
                ->map(function ($subCategory) {
                    return [
                        'type' => 'sub_category',
                        'id' => $subCategory->id,
                        'name' => $subCategory->name,
                        'category' => $subCategory->category->name ?? 'N/A',
                        'web_url' => route('courses.index', ['sub_category' => $subCategory->id]),
                    ];
                });

            $suggestions = $categories->concat($subCategories)->take($limit)->values();

            return response()->json([
                'success' => true,
                'message' => 'Suggestions retrieved successfully',
                'data' => [
                    'suggestions' => $suggestions,
                    'query' => $query,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve suggestions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

