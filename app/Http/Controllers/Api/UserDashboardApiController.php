<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserEnrollment;
use App\Models\Course;
use App\Models\SessionBooking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserDashboardApiController extends Controller
{
    /**
     * Get user dashboard overview
     */
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
        
        // Get upcoming sessions (sessions starting in the future)
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
            ->values()
            ->map(function($enrollment) {
                $course = $enrollment->enrollable;
                $mentor = $course->mentor->user;
                
                return [
                    'id' => $enrollment->id,
                    'course' => [
                        'id' => $course->id,
                        'title' => $course->title,
                        'thumbnail' => $course->thumbnail ? asset('storage/' . $course->thumbnail) : null,
                        'average_rating' => round($course->averageRating(), 1),
                        'start_date' => $course->start_date ? $course->start_date->format('M d, Y') : null,
                        'duration' => $this->calculateDuration($course->start_date, $course->end_date),
                    ],
                    'mentor' => [
                        'id' => $mentor->id,
                        'name' => $mentor->name,
                        'photo' => $mentor->photo ? asset('storage/' . $mentor->photo) : null,
                    ],
                    'enrollment_status' => $enrollment->enrollment_status,
                    'progress_percentage' => round($enrollment->progress_percentage, 2),
                    'enrolled_at' => $enrollment->enrolled_at ? $enrollment->enrolled_at->format('Y-m-d H:i:s') : null,
                ];
            });
        
        // Get upcoming sessions for display
        $upcomingSessionsDisplay = $upcomingSessions->take(3)->values()
            ->map(function($enrollment) {
                $session = $enrollment->enrollable;
                $mentor = $session->mentor->user;
                
                return [
                    'id' => $enrollment->id,
                    'session' => [
                        'id' => $session->id,
                        'date' => $session->date ? $session->date->format('M d, Y') : null,
                        'start_time' => $session->start_time ? Carbon::parse($session->start_time)->format('g:i A') : null,
                        'formatted_time_slot' => $session->formatted_time_slot,
                        'sub_categories' => $session->subCategories->pluck('name')->toArray(),
                    ],
                    'mentor' => [
                        'id' => $mentor->id,
                        'name' => $mentor->name,
                        'photo' => $mentor->photo ? asset('storage/' . $mentor->photo) : null,
                    ],
                    'enrollment_status' => $enrollment->enrollment_status,
                    'enrolled_at' => $enrollment->enrolled_at ? $enrollment->enrolled_at->format('Y-m-d H:i:s') : null,
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => [
                'statistics' => [
                    'active_courses' => $activeCourses,
                    'completed_courses' => $completedCourses,
                    'upcoming_sessions' => $upcomingCount,
                    'learning_hours' => $learningHours,
                ],
                'current_courses' => $currentCourses,
                'upcoming_sessions' => $upcomingSessionsDisplay,
                'search' => $search,
            ]
        ]);
    }
    
    /**
     * Get detailed dashboard statistics
     */
    public function statistics(Request $request)
    {
        $user = Auth::user();
        
        // Course statistics
        $courseStats = [
            'total_courses' => UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', Course::class)
                ->whereIn('enrollment_status', ['active', 'completed'])
                ->count(),
            'active_courses' => UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', Course::class)
                ->where('enrollment_status', 'active')
                ->count(),
            'completed_courses' => UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', Course::class)
                ->where('enrollment_status', 'completed')
                ->count(),
            'total_spent_courses' => UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', Course::class)
                ->whereHas('paymentTransaction')
                ->with('paymentTransaction')
                ->get()
                ->sum(function($enrollment) {
                    return $enrollment->paymentTransaction->gross_amount ?? 0;
                })
        ];
        
        // Session statistics
        $sessionStats = [
            'total_sessions' => UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', SessionBooking::class)
                ->whereIn('enrollment_status', ['active', 'completed'])
                ->count(),
            'upcoming_sessions' => UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', SessionBooking::class)
                ->where('enrollment_status', 'active')
                ->whereExists(function($query) {
                    $query->select(DB::raw(1))
                          ->from('session_bookings')
                          ->whereColumn('session_bookings.id', 'user_enrollments.enrollable_id')
                          ->where('session_bookings.date', '>', now());
                })
                ->count(),
            'completed_sessions' => UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', SessionBooking::class)
                ->where('enrollment_status', 'completed')
                ->count(),
            'total_spent_sessions' => UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', SessionBooking::class)
                ->whereIn('enrollment_status', ['active', 'completed'])
                ->sum('amount')
        ];
        
        // Learning hours by month (last 6 months)
        $learningHoursByMonth = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $hours = UserEnrollment::where('user_id', $user->id)
                ->where('enrollment_status', 'active')
                ->where('enrollable_type', Course::class)
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count() * 2;
            
            $learningHoursByMonth[] = [
                'month' => $month->format('M Y'),
                'hours' => $hours
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'course_statistics' => $courseStats,
                'session_statistics' => $sessionStats,
                'learning_hours_by_month' => $learningHoursByMonth,
                'total_learning_hours' => array_sum(array_column($learningHoursByMonth, 'hours')),
            ]
        ]);
    }
    
    /**
     * Calculate course duration
     */
    private function calculateDuration($startDate, $endDate)
    {
        if (!$startDate || !$endDate) {
            return 'Ongoing';
        }
        
        $diff = $startDate->diff($endDate);
        $months = $diff->m + ($diff->y * 12);
        $days = $diff->d;
        
        $duration = '';
        if ($months > 0) {
            $duration .= $months . ' Month' . ($months > 1 ? 's' : '');
        }
        if ($months > 0 && $days > 0) {
            $duration .= ', ';
        }
        if ($days > 0) {
            $duration .= $days . ' Day' . ($days > 1 ? 's' : '');
        }
        
        return $duration ?: 'Ongoing';
    }
}
