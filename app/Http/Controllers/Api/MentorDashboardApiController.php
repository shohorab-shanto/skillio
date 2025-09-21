<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\UserEnrollment;
use App\Models\PaymentTransaction;
use App\Models\SessionBooking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MentorDashboardApiController extends Controller
{
    /**
     * Get mentor dashboard overview
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

        // Get course statistics
        $totalCourses = Course::where('mentor_id', $mentor->id)->count();
        $activeCourses = Course::where('mentor_id', $mentor->id)->where('status', 'approved')->count();
        
        // Get user/student statistics
        $totalUsers = UserEnrollment::whereHas('enrollable', function($query) use ($mentor) {
            $query->where('mentor_id', $mentor->id);
        })->distinct('user_id')->count();
        
        // Get income statistics
        $mentorIncome = PaymentTransaction::whereHas('enrollments.enrollable', function($query) use ($mentor) {
            $query->where('mentor_id', $mentor->id);
        })->where('transaction_status', 'completed')->sum('mentor_amount');
        
        // Get current month earnings
        $currentMonthEarnings = PaymentTransaction::whereHas('enrollments.enrollable', function($query) use ($mentor) {
            $query->where('mentor_id', $mentor->id);
        })->where('transaction_status', 'completed')
          ->whereMonth('created_at', Carbon::now()->month)
          ->whereYear('created_at', Carbon::now()->year)
          ->sum('mentor_amount');
        
        // Get today's earnings
        $todayEarnings = PaymentTransaction::whereHas('enrollments.enrollable', function($query) use ($mentor) {
            $query->where('mentor_id', $mentor->id);
        })->where('transaction_status', 'completed')
          ->whereDate('created_at', Carbon::today())
          ->sum('mentor_amount');

        // Get recent activity summary (last 5 enrollments)
        $recentActivity = UserEnrollment::whereHas('enrollable', function($query) use ($mentor) {
            $query->where('mentor_id', $mentor->id);
        })->with(['user', 'enrollable'])
          ->orderBy('created_at', 'desc')
          ->limit(5)
          ->get()
          ->map(function($enrollment) {
              $student = $enrollment->user;
              $service = $enrollment->enrollable;
              
              return [
                  'student_name' => $student->name,
                  'service_title' => $enrollment->enrollable_type == Course::class ? $service->title : 'Session',
                  'service_type' => $enrollment->enrollable_type == Course::class ? 'Course' : 'Session',
                  'enrolled_at' => $enrollment->created_at->format('M d, Y'),
                  'status' => $enrollment->enrollment_status,
              ];
          });

        return response()->json([
            'success' => true,
            'data' => [
                'statistics' => [
                    'total_courses' => $totalCourses,
                    'active_courses' => $activeCourses,
                    'total_users' => $totalUsers,
                    'mentor_income' => round($mentorIncome, 2),
                    'current_month_earnings' => round($currentMonthEarnings, 2),
                    'today_earnings' => round($todayEarnings, 2),
                ],
                'recent_activity' => $recentActivity,
            ]
        ]);
    }

    /**
     * Get detailed dashboard statistics with charts data
     */
    public function statistics(Request $request)
    {
        $user = Auth::user();
        $mentor = $user->mentor;

        if (!$mentor) {
            return response()->json([
                'success' => false,
                'message' => 'Mentor profile not found.'
            ], 404);
        }

        // Get monthly earnings for the last 6 months (for bar chart)
        $monthlyEarnings = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M Y');
            $monthEarnings = PaymentTransaction::whereHas('enrollments.enrollable', function($query) use ($mentor) {
                $query->where('mentor_id', $mentor->id);
            })->where('transaction_status', 'completed')
              ->whereMonth('created_at', $date->month)
              ->whereYear('created_at', $date->year)
              ->sum('mentor_amount');
            
            $monthlyEarnings[] = [
                'month' => $monthName,
                'earnings' => round($monthEarnings, 2)
            ];
        }
        
        // Get earnings data for line chart (last 30 days)
        $dailyEarnings = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayEarnings = PaymentTransaction::whereHas('enrollments.enrollable', function($query) use ($mentor) {
                $query->where('mentor_id', $mentor->id);
            })->where('transaction_status', 'completed')
              ->whereDate('created_at', $date->toDateString())
              ->sum('mentor_amount');
            
            $dailyEarnings[] = [
                'date' => $date->format('M d'),
                'earnings' => round($dayEarnings, 2)
            ];
        }

        // Course statistics
        $courseStats = [
            'total_courses' => Course::where('mentor_id', $mentor->id)->count(),
            'approved_courses' => Course::where('mentor_id', $mentor->id)->where('status', 'approved')->count(),
            'pending_courses' => Course::where('mentor_id', $mentor->id)->where('status', 'pending')->count(),
            'rejected_courses' => Course::where('mentor_id', $mentor->id)->where('status', 'rejected')->count(),
        ];

        // Session statistics
        $sessionStats = [
            'total_sessions' => SessionBooking::where('mentor_id', $mentor->id)->count(),
            'available_sessions' => SessionBooking::where('mentor_id', $mentor->id)->whereNull('user_id')->count(),
            'booked_sessions' => SessionBooking::where('mentor_id', $mentor->id)->whereNotNull('user_id')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'monthly_earnings' => $monthlyEarnings,
                'daily_earnings' => $dailyEarnings,
                'course_statistics' => $courseStats,
                'session_statistics' => $sessionStats,
                'total_monthly_earnings' => array_sum(array_column($monthlyEarnings, 'earnings')),
                'total_daily_earnings' => array_sum(array_column($dailyEarnings, 'earnings')),
            ]
        ]);
    }
}
