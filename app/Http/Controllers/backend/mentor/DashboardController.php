<?php

namespace App\Http\Controllers\backend\mentor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\UserEnrollment;
use App\Models\PaymentTransaction;
use App\Models\SessionBooking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $mentor = $user->mentor;

        if (!$mentor) {
            return redirect()->route('mentor.dashboard')->with('error', 'Mentor profile not found.');
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
        
        // Get monthly earnings for the last 6 months (for bar chart)
        $monthlyEarnings = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M');
            $monthEarnings = PaymentTransaction::whereHas('enrollments.enrollable', function($query) use ($mentor) {
                $query->where('mentor_id', $mentor->id);
            })->where('transaction_status', 'completed')
              ->whereMonth('created_at', $date->month)
              ->whereYear('created_at', $date->year)
              ->sum('mentor_amount');
            
            $monthlyEarnings[] = [
                'month' => $monthName,
                'earnings' => $monthEarnings
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
                'earnings' => $dayEarnings
            ];
        }
        
        // Get student list with search and filter
        $studentQuery = UserEnrollment::whereHas('enrollable', function($query) use ($mentor) {
            $query->where('mentor_id', $mentor->id);
        })->with(['user', 'enrollable', 'enrollable.category', 'enrollable.subCategories']);
        
        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $studentQuery->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                })->orWhereHas('enrollable', function($enrollableQuery) use ($search) {
                    $enrollableQuery->where('title', 'like', "%{$search}%");
                });
            });
        }
        
        // Apply status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $studentQuery->where('enrollment_status', $request->status);
        }
        
        $students = $studentQuery->orderBy('created_at', 'desc')->paginate(10);
        
        // Calculate duration left for each enrollment
        $students->getCollection()->transform(function ($enrollment) {
            if ($enrollment->enrollable_type === Course::class) {
                $course = $enrollment->enrollable;
                if ($course->end_date) {
                    $now = Carbon::now();
                    $endDate = Carbon::parse($course->end_date);
                    
                    if ($endDate->gt($now)) {
                        $diff = $now->diff($endDate);
                        $durationParts = [];
                        
                        if ($diff->m > 0) {
                            $durationParts[] = $diff->m . ' month' . ($diff->m > 1 ? 's' : '');
                        }
                        if ($diff->d > 0) {
                            $durationParts[] = $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
                        }
                        if ($diff->h > 0) {
                            $durationParts[] = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
                        }
                        if ($diff->i > 0) {
                            $durationParts[] = $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
                        }
                        
                        $enrollment->duration_left = implode(' ', $durationParts);
                        $enrollment->is_active = true;
                    } else {
                        $enrollment->duration_left = 'Expired';
                        $enrollment->is_active = false;
                    }
                } else {
                    $enrollment->duration_left = 'No end date';
                    $enrollment->is_active = true;
                }
            } else {
                // Session booking
                $session = $enrollment->enrollable;
                if ($session->start_time && $session->end_time) {
                    $now = Carbon::now();
                    $startTime = Carbon::parse($session->start_time);
                    $endTime = Carbon::parse($session->end_time);
                    
                    if ($now->between($startTime, $endTime)) {
                        $enrollment->duration_left = 'Active now';
                        $enrollment->is_active = true;
                    } elseif ($endTime->gt($now)) {
                        $diff = $now->diff($endTime);
                        $durationParts = [];
                        
                        if ($diff->d > 0) {
                            $durationParts[] = $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
                        }
                        if ($diff->h > 0) {
                            $durationParts[] = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
                        }
                        if ($diff->i > 0) {
                            $durationParts[] = $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
                        }
                        
                        $enrollment->duration_left = implode(' ', $durationParts);
                        $enrollment->is_active = true;
                    } else {
                        $enrollment->duration_left = 'Completed';
                        $enrollment->is_active = false;
                    }
                } else {
                    $enrollment->duration_left = 'No time set';
                    $enrollment->is_active = true;
                }
            }
            return $enrollment;
        });
        
        return view('backend.mentor.dashboard.index', compact(
            'totalCourses',
            'totalUsers', 
            'mentorIncome',
            'activeCourses',
            'currentMonthEarnings',
            'todayEarnings',
            'monthlyEarnings',
            'dailyEarnings',
            'students',
            'request'
        ));
    }
}
