<?php

namespace App\Http\Controllers\backend\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserEnrollment;
use App\Models\Course;
use App\Models\SessionBooking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                if ($enrollment->enrollable_type == Course::class) {
                    return stripos($enrollment->enrollable->title, $search) != false ||
                           stripos($enrollment->enrollable->mentor->user->name, $search) != false;
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
        
        // Get upcoming sessions (sessions starting in the future) - using proper approach
        $upcomingSessions = UserEnrollment::where('user_id', $user->id)
            ->where('enrollment_status', 'active')
            ->where('enrollable_type', SessionBooking::class)
            ->whereExists(function($query) {
                $query->select(DB::raw(1))
                      ->from('session_bookings')
                      ->whereColumn('session_bookings.id', 'user_enrollments.enrollable_id')
                      ->where('session_bookings.date', '>', now());
            })
            ->with(['enrollable' => function($query) {
                $query->with('mentor.user', 'subCategories');
            }])
            ->get();
        
        $upcomingCount = $upcomingSessions->count();
        
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
        
        // Get upcoming sessions for display
        $upcomingSessionsDisplay = $upcomingSessions->take(3)->values();
        
        return view('backend.user.dashboard.index', compact(
            'activeCourses',
            'completedCourses', 
            'upcomingCount',
            'learningHours',
            'currentCourses',
            'upcomingSessionsDisplay',
            'search'
        ));
    }
}
