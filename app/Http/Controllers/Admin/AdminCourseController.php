<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class AdminCourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with(['mentor.user', 'category', 'subCategories']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('mentor.user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Sub-category filter
        if ($request->filled('sub_category_id')) {
            $query->whereHas('subCategories', function($q) use ($request) {
                $q->where('sub_category_id', $request->sub_category_id);
            });
        }

        // Price range filter
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Date filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $courses = $query->latest()->paginate(15);

        // Get categories and sub-categories for filters
        $categories = Category::orderBy('name')->get();
        $subCategories = SubCategory::orderBy('name')->get();

        return view('admin.courses.index', compact('courses', 'categories', 'subCategories'));
    }

    public function show(Course $course)
    {
        $course->load(['mentor.user', 'category', 'subCategories', 'reviews.user']);
        
        return view('admin.courses.show', compact('course'));
    }

    public function approve(Request $request, Course $course)
    {
        $course->approve();
        
        // Create notification for mentor
        \App\Services\NotificationService::createCourseApprovalNotification($course, 'course_approved');
        
        return response()->json([
            'success' => true,
            'message' => 'Course approved successfully',
            'status' => 'approved'
        ]);
    }

    public function reject(Request $request, Course $course)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $course->reject($request->rejection_reason);
        
        // Create notification for mentor
        \App\Services\NotificationService::createCourseApprovalNotification($course, 'course_rejected');
        
        return response()->json([
            'success' => true,
            'message' => 'Course rejected successfully',
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]);
    }

    public function toggleStatus(Request $request, Course $course)
    {
        if ($course->status == 'approved') {
            $course->update(['status' => 'pending']);
            $status = 'pending';
            $message = 'Course status changed to pending';
        } else {
            $course->approve();
            $status = 'approved';
            $message = 'Course approved successfully';
            
            // Create notification for mentor
            \App\Services\NotificationService::createCourseApprovalNotification($course, 'course_approved');
        }
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'status' => $status
        ]);
    }

    public function toggleFeatured(Request $request, Course $course)
    {
        $course->toggleFeatured();
        
        return response()->json([
            'success' => true,
            'message' => $course->isFeatured() ? 'Course marked as featured' : 'Course removed from featured',
            'featured' => $course->isFeatured()
        ]);
    }
}
