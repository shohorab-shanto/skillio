<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CourseApiController extends Controller
{
    /**
     * Get all courses with filtering and pagination
     */
    public function getAllCourses(Request $request): JsonResponse
    {
        try {
            $query = Course::with(['mentor.user', 'category', 'subCategories'])
                ->approved()
                ->withCount('reviews')
                ->withAvg('reviews', 'rating');

            // Apply filters
            if ($request->has('category_id') && $request->category_id) {
                $query->byCategory($request->category_id);
            }

            if ($request->has('sub_category_ids') && $request->sub_category_ids) {
                $subCategoryIds = is_array($request->sub_category_ids) 
                    ? $request->sub_category_ids 
                    : explode(',', $request->sub_category_ids);
                $query->bySubCategories($subCategoryIds);
            }

            if ($request->has('min_price') && $request->min_price) {
                $query->where('price', '>=', $request->min_price);
            }

            if ($request->has('max_price') && $request->max_price) {
                $query->where('price', '<=', $request->max_price);
            }

            if ($request->has('min_rating') && $request->min_rating) {
                $query->having('reviews_avg_rating', '>=', $request->min_rating);
            }

            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhereHas('mentor.user', function($mentorQuery) use ($search) {
                          $mentorQuery->where('name', 'like', "%{$search}%");
                      });
                });
            }

            if ($request->has('featured') && $request->featured) {
                $query->featured();
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            switch ($sortBy) {
                case 'price':
                    $query->orderBy('price', $sortOrder);
                    break;
                case 'rating':
                    $query->orderBy('reviews_avg_rating', $sortOrder);
                    break;
                case 'reviews':
                    $query->orderBy('reviews_count', $sortOrder);
                    break;
                case 'title':
                    $query->orderBy('title', $sortOrder);
                    break;
                case 'popular':
                    $query->orderBy('reviews_count', 'desc')
                          ->orderBy('reviews_avg_rating', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', $sortOrder);
            }

            // Pagination
            $perPage = $request->get('per_page', 12);
            $courses = $query->paginate($perPage);

            // Transform the data
            $courses->getCollection()->transform(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'description' => $course->description,
                    'thumbnail' => $course->thumbnail_url,
                    'cover_photo' => $course->cover_photo_url,
                    'price' => $course->price,
                    'currency' => $course->currency ?? 'USD',
                    'discount' => $course->discount,
                    'discounted_price' => $course->discounted_price,
                    'duration_type' => $course->duration_type ?? 'days',
                    'duration_days' => $course->duration_days,
                    'duration_hours' => $course->duration_hours,
                    'start_date' => $course->start_date?->format('Y-m-d'),
                    'end_date' => $course->end_date?->format('Y-m-d'),
                    'featured' => $course->featured,
                    'type' => 'Online',
                    'average_rating' => round($course->reviews_avg_rating ?? 0, 1),
                    'total_reviews' => $course->reviews_count,
                    'enrolled_students_count' => $course->enrolledStudentsCount(),
                    'mentor' => [
                        'id' => $course->mentor->user_id,
                        'name' => $course->mentor->user->name,
                        'photo' => $course->mentor->photo ? asset('storage/' . $course->mentor->photo) : null,
                        'verified' => $course->mentor->verified,
                        'average_rating' => round($course->mentor->averageRating() ?? 0, 1),
                    ],
                    'category' => [
                        'id' => $course->category->id,
                        'name' => $course->category->name,
                    ],
                    'sub_categories' => $course->subCategories->map(function ($subCategory) {
                        return [
                            'id' => $subCategory->id,
                            'name' => $subCategory->name,
                        ];
                    }),
                    'created_at' => $course->created_at->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Courses retrieved successfully',
                'data' => $courses,
                'filters' => [
                    'category_id' => $request->category_id,
                    'sub_category_ids' => $request->sub_category_ids,
                    'min_price' => $request->min_price,
                    'max_price' => $request->max_price,
                    'min_rating' => $request->min_rating,
                    'search' => $request->search,
                    'featured' => $request->featured,
                    'sort_by' => $sortBy,
                    'sort_order' => $sortOrder,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve courses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get filter options for courses
     */
    public function getFilterOptions(): JsonResponse
    {
        try {
            $categories = Category::with('subCategories')->get();
            $priceRange = Course::approved()
                ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Filter options retrieved successfully',
                'data' => [
                    'categories' => $categories->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                            'sub_categories' => $category->subCategories->map(function ($subCategory) {
                                return [
                                    'id' => $subCategory->id,
                                    'name' => $subCategory->name,
                                ];
                            }),
                        ];
                    }),
                    'price_range' => [
                        'min' => $priceRange->min_price ?? 0,
                        'max' => $priceRange->max_price ?? 1000,
                    ],
                    'sort_options' => [
                        ['value' => 'created_at', 'label' => 'Newest First'],
                        ['value' => 'price', 'label' => 'Price: Low to High'],
                        ['value' => 'price_desc', 'label' => 'Price: High to Low'],
                        ['value' => 'rating', 'label' => 'Highest Rated'],
                        ['value' => 'reviews', 'label' => 'Most Reviewed'],
                        ['value' => 'title', 'label' => 'Title A-Z'],
                        ['value' => 'popular', 'label' => 'Most Popular'],
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve filter options',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
