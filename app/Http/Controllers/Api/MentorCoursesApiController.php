<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class MentorCoursesApiController extends Controller
{
    /**
     * Display a listing of the mentor's courses.
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

        // Get courses with pagination and filtering
        $query = Course::where('mentor_id', $mentor->id)
            ->with(['category', 'subCategories', 'reviews'])
            ->when($request->filled('status'), function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', "%{$request->search}%")
                      ->orWhere('description', 'like', "%{$request->search}%");
                });
            })
            ->orderBy('created_at', 'desc');

        $perPage = $request->get('per_page', 12);
        $courses = $query->paginate($perPage);

        // Transform courses for API response
        $courses->getCollection()->transform(function ($course) {
            return [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'thumbnail' => $course->thumbnail ? asset('storage/' . $course->thumbnail) : null,
                'cover_photo' => $course->cover_photo ? asset('storage/' . $course->cover_photo) : null,
                'price' => round($course->price, 2),
                'discount' => round($course->discount, 2),
                'final_price' => round($course->finalPrice, 2),
                'duration_days' => $course->duration_days,
                'status' => $course->status,
                'needs_reapproval' => $course->needs_reapproval,
                'average_rating' => round($course->averageRating(), 1),
                'reviews_count' => $course->reviews->count(),
                'enrolled_students_count' => $course->enrolledStudentsCount(),
                'category' => [
                    'id' => $course->category->id,
                    'name' => $course->category->name,
                ],
                'sub_categories' => $course->subCategories->map(function($subCategory) {
                    return [
                        'id' => $subCategory->id,
                        'name' => $subCategory->name,
                    ];
                }),
                'created_at' => $course->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $course->updated_at->format('Y-m-d H:i:s'),
            ];
        });

        // Get course statistics
        $stats = [
            'total_courses' => Course::where('mentor_id', $mentor->id)->count(),
            'approved_courses' => Course::where('mentor_id', $mentor->id)->where('status', 'approved')->count(),
            'pending_courses' => Course::where('mentor_id', $mentor->id)->where('status', 'pending')->count(),
            'rejected_courses' => Course::where('mentor_id', $mentor->id)->where('status', 'rejected')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'courses' => $courses->items(),
                'pagination' => [
                    'current_page' => $courses->currentPage(),
                    'last_page' => $courses->lastPage(),
                    'per_page' => $courses->perPage(),
                    'total' => $courses->total(),
                ],
                'statistics' => $stats,
            ]
        ]);
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $mentor = $user->mentor;

        if (!$mentor) {
            return response()->json([
                'success' => false,
                'message' => 'Mentor profile not found.'
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'category_id' => 'required|exists:categories,id',
            'sub_category_ids' => 'required|array|min:1',
            'sub_category_ids.*' => 'exists:sub_categories,id',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'duration_days' => 'required|integer|min:1|max:365',
            'course_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload and thumbnail creation
        $thumbnailPath = null;
        $coverPhotoPath = null;
        
        if ($request->hasFile('course_image')) {
            $image = $request->file('course_image');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
            
            // Store the original image as cover photo
            $coverPhotoPath = $image->storeAs('courses/covers', $imageName, 'public');
            
            // Create and store thumbnail (300x200)
            $thumbnailName = 'thumb_' . $imageName;
            $thumbnailImage = Image::read($image->getPathname())
                ->resize(300, 200);
            
            Storage::disk('public')->put('courses/thumbnails/' . $thumbnailName, $thumbnailImage->encode());
            $thumbnailPath = 'courses/thumbnails/' . $thumbnailName;
        }

        // Create the course
        $course = Course::create([
            'mentor_id' => $mentor->id,
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'thumbnail' => $thumbnailPath,
            'cover_photo' => $coverPhotoPath,
            'price' => $validated['price'],
            'discount' => $validated['discount'] ?? 0,
            'start_date' => null,
            'end_date' => null,
            'duration_days' => $validated['duration_days'],
            'status' => 'pending',
            'needs_reapproval' => false,
        ]);

        // Attach sub-categories
        $course->subCategories()->attach($validated['sub_category_ids']);

        // Create notification for admin users
        \App\Services\NotificationService::createCourseCreatedNotification($course);

        // Load relationships for response
        $course->load(['category', 'subCategories', 'reviews']);

        return response()->json([
            'success' => true,
            'message' => 'Course created successfully and is pending approval.',
            'data' => [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'thumbnail' => $course->thumbnail ? asset('storage/' . $course->thumbnail) : null,
                'cover_photo' => $course->cover_photo ? asset('storage/' . $course->cover_photo) : null,
                'price' => round($course->price, 2),
                'discount' => round($course->discount, 2),
                'final_price' => round($course->finalPrice, 2),
                'duration_days' => $course->duration_days,
                'status' => $course->status,
                'needs_reapproval' => $course->needs_reapproval,
                'category' => [
                    'id' => $course->category->id,
                    'name' => $course->category->name,
                ],
                'sub_categories' => $course->subCategories->map(function($subCategory) {
                    return [
                        'id' => $subCategory->id,
                        'name' => $subCategory->name,
                    ];
                }),
                'created_at' => $course->created_at->format('Y-m-d H:i:s'),
            ]
        ], 201);
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course)
    {
        $user = Auth::user();
        $mentor = $user->mentor;

        // Check if the course belongs to the current mentor
        if ($course->mentor_id != $mentor->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to course.'
            ], 403);
        }

        // Load relationships
        $course->load(['category', 'subCategories', 'reviews.user']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'thumbnail' => $course->thumbnail ? asset('storage/' . $course->thumbnail) : null,
                'cover_photo' => $course->cover_photo ? asset('storage/' . $course->cover_photo) : null,
                'price' => round($course->price, 2),
                'discount' => round($course->discount, 2),
                'final_price' => round($course->finalPrice, 2),
                'duration_days' => $course->duration_days,
                'status' => $course->status,
                'needs_reapproval' => $course->needs_reapproval,
                'average_rating' => round($course->averageRating(), 1),
                'reviews_count' => $course->reviews->count(),
                'enrolled_students_count' => $course->enrolledStudentsCount(),
                'category' => [
                    'id' => $course->category->id,
                    'name' => $course->category->name,
                ],
                'sub_categories' => $course->subCategories->map(function($subCategory) {
                    return [
                        'id' => $subCategory->id,
                        'name' => $subCategory->name,
                    ];
                }),
                'reviews' => $course->reviews->map(function($review) {
                    return [
                        'id' => $review->id,
                        'rating' => $review->rating,
                        'comment' => $review->comment,
                        'user' => [
                            'id' => $review->user->id,
                            'name' => $review->user->name,
                        ],
                        'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                    ];
                }),
                'created_at' => $course->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $course->updated_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    /**
     * Update the specified course in storage.
     */
    public function update(Request $request, Course $course)
    {
        $user = Auth::user();
        $mentor = $user->mentor;

        // Check if the course belongs to the current mentor
        if ($course->mentor_id != $mentor->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to course.'
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'category_id' => 'required|exists:categories,id',
            'sub_category_ids' => 'required|array|min:1',
            'sub_category_ids.*' => 'exists:sub_categories,id',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'duration_days' => 'required|integer|min:1|max:365',
            'course_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload and thumbnail creation
        if ($request->hasFile('course_image')) {
            // Delete old images
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            if ($course->cover_photo) {
                Storage::disk('public')->delete($course->cover_photo);
            }
            
            $image = $request->file('course_image');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
            
            // Store the original image as cover photo
            $coverPhotoPath = $image->storeAs('courses/covers', $imageName, 'public');
            
            // Create and store thumbnail (300x200)
            $thumbnailName = 'thumb_' . $imageName;
            $thumbnailImage = Image::read($image->getPathname())
                ->resize(300, 200);
            
            Storage::disk('public')->put('courses/thumbnails/' . $thumbnailName, $thumbnailImage->encode());
            $thumbnailPath = 'courses/thumbnails/' . $thumbnailName;
            
            $validated['thumbnail'] = $thumbnailPath;
            $validated['cover_photo'] = $coverPhotoPath;
        }

        // Determine if course needs reapproval
        $needsReapproval = false;
        if ($course->status == 'approved') {
            // Check if any significant changes were made
            $significantFields = ['title', 'description', 'price', 'duration_days'];
            foreach ($significantFields as $field) {
                if ($course->{$field} != $validated[$field]) {
                    $needsReapproval = true;
                    break;
                }
            }
            
            // Check if sub-categories changed
            $currentSubCategories = $course->subCategories->pluck('id')->sort()->values()->toArray();
            $newSubCategories = collect($validated['sub_category_ids'])->sort()->values()->toArray();
            if ($currentSubCategories != $newSubCategories) {
                $needsReapproval = true;
            }
            
            // Check if image was changed
            if ($request->hasFile('course_image')) {
                $needsReapproval = true;
            }
        }

        // Update the course
        $updateData = [
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'discount' => $validated['discount'] ?? 0,
            'start_date' => null,
            'end_date' => null,
            'duration_days' => $validated['duration_days'],
            'needs_reapproval' => $needsReapproval,
            'status' => $needsReapproval ? 'pending' : $course->status,
        ];

        if (isset($validated['thumbnail'])) {
            $updateData['thumbnail'] = $validated['thumbnail'];
        }

        if (isset($validated['cover_photo'])) {
            $updateData['cover_photo'] = $validated['cover_photo'];
        }

        $course->update($updateData);

        // Update sub-categories
        $course->subCategories()->sync($validated['sub_category_ids']);

        // Create notification for admin users
        \App\Services\NotificationService::createCourseUpdatedNotification($course, $needsReapproval);

        // Load relationships for response
        $course->load(['category', 'subCategories']);

        $message = $needsReapproval 
            ? 'Course updated successfully. Changes are pending admin approval.'
            : 'Course updated successfully.';

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'thumbnail' => $course->thumbnail ? asset('storage/' . $course->thumbnail) : null,
                'cover_photo' => $course->cover_photo ? asset('storage/' . $course->cover_photo) : null,
                'price' => round($course->price, 2),
                'discount' => round($course->discount, 2),
                'final_price' => round($course->finalPrice, 2),
                'duration_days' => $course->duration_days,
                'status' => $course->status,
                'needs_reapproval' => $course->needs_reapproval,
                'category' => [
                    'id' => $course->category->id,
                    'name' => $course->category->name,
                ],
                'sub_categories' => $course->subCategories->map(function($subCategory) {
                    return [
                        'id' => $subCategory->id,
                        'name' => $subCategory->name,
                    ];
                }),
                'updated_at' => $course->updated_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    /**
     * Remove the specified course from storage.
     */
    public function destroy(Course $course)
    {
        $user = Auth::user();
        $mentor = $user->mentor;

        // Check if the course belongs to the current mentor
        if ($course->mentor_id != $mentor->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to course.'
            ], 403);
        }

        // Delete associated files
        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        if ($course->cover_photo) {
            Storage::disk('public')->delete($course->cover_photo);
        }

        // Delete the course (this will also delete related pivot table entries due to cascade)
        $course->delete();

        return response()->json([
            'success' => true,
            'message' => 'Course deleted successfully.'
        ]);
    }

    /**
     * Get categories and subcategories for course creation/editing
     */
    public function getFormData()
    {
        $categories = Category::with('subCategories')->get()->map(function($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'sub_categories' => $category->subCategories->map(function($subCategory) {
                    return [
                        'id' => $subCategory->id,
                        'name' => $subCategory->name,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'categories' => $categories,
            ]
        ]);
    }

    /**
     * Get course statistics
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
            'total_courses' => Course::where('mentor_id', $mentor->id)->count(),
            'approved_courses' => Course::where('mentor_id', $mentor->id)->where('status', 'approved')->count(),
            'pending_courses' => Course::where('mentor_id', $mentor->id)->where('status', 'pending')->count(),
            'rejected_courses' => Course::where('mentor_id', $mentor->id)->where('status', 'rejected')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
