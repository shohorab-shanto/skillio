<?php

namespace App\Http\Controllers\backend\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserEnrollment;
use App\Models\Course;
use App\Models\SessionBooking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search', '');
        
        // Get user enrollments
        $enrollments = UserEnrollment::where('user_id', $user->id)
            ->where('enrollment_status', 'active')
            ->with(['enrollable' => function($query) {
                $query->with('mentor.user');
            }])
            ->get();
        
        // Filter enrollments by search
        if ($search) {
            $enrollments = $enrollments->filter(function($enrollment) use ($search) {
                if ($enrollment->enrollable_type === Course::class) {
                    return stripos($enrollment->enrollable->title, $search) !== false ||
                           stripos($enrollment->enrollable->mentor->user->name, $search) !== false;
                }
                return false;
            });
        }
        
        // Calculate statistics
        $activeCourses = $enrollments->where('enrollable_type', Course::class)->count();
        $completedCourses = UserEnrollment::where('user_id', $user->id)
            ->where('enrollment_status', 'completed')
            ->where('enrollable_type', Course::class)
            ->count();
        
        // Get upcoming courses (courses starting in the future)
        $upcomingCourses = $enrollments->where('enrollable_type', Course::class)
            ->filter(function($enrollment) {
                $course = $enrollment->enrollable;
                return $course->start_date && $course->start_date->isFuture();
            });
        
        $upcomingCount = $upcomingCourses->count();
        
        // Calculate learning hours (this month)
        $thisMonth = Carbon::now()->startOfMonth();
        $learningHours = UserEnrollment::where('user_id', $user->id)
            ->where('enrollment_status', 'active')
            ->where('enrollable_type', Course::class)
            ->where('created_at', '>=', $thisMonth)
            ->count() * 2; // Assuming 2 hours per course per month
        
        // Get current courses for display
        $currentCourses = $enrollments->where('enrollable_type', Course::class)
            ->take(3)
            ->values();
        
        // Get upcoming courses for display
        $upcomingCoursesDisplay = $upcomingCourses->take(3)->values();
        
        return view('backend.user.dashboard.index', compact(
            'activeCourses',
            'completedCourses', 
            'upcomingCount',
            'learningHours',
            'currentCourses',
            'upcomingCoursesDisplay',
            'search'
        ));
    }
}
