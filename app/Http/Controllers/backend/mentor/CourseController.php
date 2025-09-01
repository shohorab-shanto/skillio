<?php

namespace App\Http\Controllers\backend\mentor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Laravel\Facades\Image;

class CourseController extends Controller
{
    /**
     * Display a listing of the mentor's courses.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $mentor = $user->mentor;

        if (!$mentor) {
            return redirect()->route('mentor.dashboard')->with('error', 'Mentor profile not found.');
        }

        // Get courses with pagination and filtering
        $courses = Course::where('mentor_id', $mentor->id)
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
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Get course statistics
        $stats = [
            'total_courses' => Course::where('mentor_id', $mentor->id)->count(),
            'approved_courses' => Course::where('mentor_id', $mentor->id)->where('status', 'approved')->count(),
            'pending_courses' => Course::where('mentor_id', $mentor->id)->where('status', 'pending')->count(),
            'rejected_courses' => Course::where('mentor_id', $mentor->id)->where('status', 'rejected')->count(),
            'total_enrolled_students' => 0, // Will be calculated when enrollment system is implemented
            'total_revenue' => 0, // Will calculate based on enrollment system later
        ];

        return view('backend.mentor.courses.index', compact('courses', 'stats', 'request'));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create()
    {
        $categories = Category::with('subCategories')->get();
        $subCategories = SubCategory::all();
        
        return view('backend.mentor.courses.create', compact('categories', 'subCategories'));
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $mentor = $user->mentor;

        if (!$mentor) {
            return redirect()->route('mentor.dashboard')->with('error', 'Mentor profile not found.');
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

        return redirect()->route('mentor.courses.index')->with('success', 'Course created successfully and is pending approval.');
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
            return redirect()->route('mentor.courses.index')->with('error', 'Unauthorized access to course.');
        }

        // Load relationships
        $course->load(['category', 'subCategories', 'reviews']);

        return view('backend.mentor.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Course $course)
    {
        $user = Auth::user();
        $mentor = $user->mentor;

        // Check if the course belongs to the current mentor
        if ($course->mentor_id != $mentor->id) {
            return redirect()->route('mentor.courses.index')->with('error', 'Unauthorized access to course.');
        }

        $categories = Category::with('subCategories')->get();
        $subCategories = SubCategory::all();
        $selectedSubCategories = $course->subCategories->pluck('id')->toArray();

        return view('backend.mentor.courses.edit', compact('course', 'categories', 'subCategories', 'selectedSubCategories'));
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
            return redirect()->route('mentor.courses.index')->with('error', 'Unauthorized access to course.');
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
            'status' => 'pending',
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

        $message = $needsReapproval 
            ? 'Course updated successfully. Changes are pending admin approval.'
            : 'Course updated successfully.';

        return redirect()->route('mentor.courses.index')->with('success', $message);
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
            return redirect()->route('mentor.courses.index')->with('error', 'Unauthorized access to course.');
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

        return redirect()->route('mentor.courses.index')->with('success', 'Course deleted successfully.');
    }
}
