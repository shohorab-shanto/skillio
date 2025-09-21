<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PaymentTransaction;
use Carbon\Carbon;

class MentorEarningsApiController extends Controller
{
    /**
     * Display the mentor earning history
     */
    public function index(Request $request)
    {
        $mentor = Auth::user()->mentor;
        
        if (!$mentor) {
            return response()->json([
                'success' => false,
                'message' => 'Mentor profile not found.'
            ], 404);
        }
        
        // Get mentor's earning transactions through enrollments
        $query = PaymentTransaction::whereHas('enrollments.enrollable', function($q) use ($mentor) {
                $q->where('mentor_id', $mentor->id);
            })
            ->with(['enrollments.enrollable.mentor.user', 'enrollments.user', 'enrollments.enrollable.category'])
            ->orderBy('created_at', 'desc');
        
        // Search functionality - only by transaction ID and date
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhere('created_at', 'like', "%{$search}%");
            });
        }
        
        // Date range filtering
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        $perPage = $request->get('per_page', 10);
        $payments = $query->paginate($perPage);
        
        // Transform payments data for API response
        $payments->getCollection()->transform(function ($payment) {
            $enrollment = $payment->enrollments->first();
            $enrollable = $enrollment ? $enrollment->enrollable : null;
            $user = $enrollment ? $enrollment->user : null;
            
            // Handle different mentor relationship structures
            $serviceName = 'N/A';
            $serviceType = 'Unknown';
            $userName = 'N/A';
            
            if ($enrollable) {
                if ($enrollment->enrollable_type == 'App\Models\Course') {
                    $serviceName = $enrollable->title ?? 'Course';
                    $serviceType = 'Course';
                } else {
                    $serviceName = 'Session';
                    $serviceType = 'Session';
                }
            }
            
            if ($user) {
                $userName = $user->name;
            }
            
            return [
                'id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                'service_type' => $serviceType,
                'service_name' => $serviceName,
                'user_name' => $userName,
                'gross_amount' => round($payment->gross_amount, 2),
                'platform_fee' => round($payment->platform_fee, 2),
                'admin_amount' => round($payment->admin_amount, 2),
                'mentor_amount' => round($payment->mentor_amount, 2),
                'currency' => $payment->currency,
                'transaction_status' => $payment->transaction_status,
                'description' => $payment->description,
                'enrollment' => $enrollment ? [
                    'id' => $enrollment->id,
                    'enrollment_status' => $enrollment->enrollment_status,
                    'amount' => round($enrollment->amount, 2),
                    'payment_status' => $enrollment->payment_status,
                ] : null,
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => [
                'payments' => $payments->items(),
                'pagination' => [
                    'current_page' => $payments->currentPage(),
                    'last_page' => $payments->lastPage(),
                    'per_page' => $payments->perPage(),
                    'total' => $payments->total(),
                ]
            ]
        ]);
    }

    /**
     * Get earning statistics
     */
    public function statistics(Request $request)
    {
        $mentor = Auth::user()->mentor;
        
        if (!$mentor) {
            return response()->json([
                'success' => false,
                'message' => 'Mentor profile not found.'
            ], 404);
        }
        
        // Calculate earning statistics (respecting date filters)
        $statsQuery = PaymentTransaction::whereHas('enrollments.enrollable', function($q) use ($mentor) {
            $q->where('mentor_id', $mentor->id);
        })->where('transaction_status', 'completed');
        
        // Apply date filters to statistics if provided
        if ($request->filled('start_date')) {
            $statsQuery->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $statsQuery->whereDate('created_at', '<=', $request->end_date);
        }
        
        $totalEarning = $statsQuery->sum('mentor_amount');
        
        // This month earning (always current month, regardless of date filters)
        $thisMonthEarning = PaymentTransaction::whereHas('enrollments.enrollable', function($q) use ($mentor) {
            $q->where('mentor_id', $mentor->id);
        })->where('transaction_status', 'completed')
          ->whereMonth('created_at', Carbon::now()->month)
          ->whereYear('created_at', Carbon::now()->year)
          ->sum('mentor_amount');
        
        // Today's earning (always current day, regardless of date filters)
        $todayEarning = PaymentTransaction::whereHas('enrollments.enrollable', function($q) use ($mentor) {
            $q->where('mentor_id', $mentor->id);
        })->where('transaction_status', 'completed')
          ->whereDate('created_at', Carbon::today())
          ->sum('mentor_amount');

        // Monthly earnings for the last 6 months
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

        // Weekly earnings for the last 4 weeks
        $weeklyEarnings = [];
        for ($i = 3; $i >= 0; $i--) {
            $startOfWeek = Carbon::now()->subWeeks($i)->startOfWeek();
            $endOfWeek = Carbon::now()->subWeeks($i)->endOfWeek();
            $weekLabel = $startOfWeek->format('M d') . ' - ' . $endOfWeek->format('M d');
            
            $weekEarnings = PaymentTransaction::whereHas('enrollments.enrollable', function($query) use ($mentor) {
                $query->where('mentor_id', $mentor->id);
            })->where('transaction_status', 'completed')
              ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
              ->sum('mentor_amount');
            
            $weeklyEarnings[] = [
                'week' => $weekLabel,
                'earnings' => round($weekEarnings, 2)
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'total_earning' => round($totalEarning, 2),
                'this_month_earning' => round($thisMonthEarning, 2),
                'today_earning' => round($todayEarning, 2),
                'monthly_earnings' => $monthlyEarnings,
                'weekly_earnings' => $weeklyEarnings,
                'average_monthly_earning' => count($monthlyEarnings) > 0 ? round(array_sum(array_column($monthlyEarnings, 'earnings')) / count($monthlyEarnings), 2) : 0,
                'total_transactions' => PaymentTransaction::whereHas('enrollments.enrollable', function($q) use ($mentor) {
                    $q->where('mentor_id', $mentor->id);
                })->count(),
            ]
        ]);
    }

    /**
     * Get specific payment transaction details
     */
    public function show(PaymentTransaction $payment)
    {
        $mentor = Auth::user()->mentor;
        
        // Ensure this payment belongs to the current mentor
        $enrollment = $payment->enrollments()->whereHas('enrollable', function($q) use ($mentor) {
            $q->where('mentor_id', $mentor->id);
        })->first();
        
        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found or unauthorized access.'
            ], 404);
        }
        
        $payment->load(['enrollments.enrollable.mentor.user', 'enrollments.user', 'enrollments.enrollable.category']);
        $enrollable = $enrollment->enrollable;
        $user = $enrollment->user;
        
        // Handle different service types
        $serviceName = 'N/A';
        $serviceType = 'Unknown';
        
        if ($enrollable) {
            if ($enrollment->enrollable_type == 'App\Models\Course') {
                $serviceName = $enrollable->title ?? 'Course';
                $serviceType = 'Course';
            } else {
                $serviceName = 'Session';
                $serviceType = 'Session';
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                'service_type' => $serviceType,
                'service_name' => $serviceName,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'photo' => $user->photo ? asset('storage/' . $user->photo) : null,
                ],
                'gross_amount' => round($payment->gross_amount, 2),
                'platform_fee' => round($payment->platform_fee, 2),
                'admin_amount' => round($payment->admin_amount, 2),
                'mentor_amount' => round($payment->mentor_amount, 2),
                'currency' => $payment->currency,
                'transaction_status' => $payment->transaction_status,
                'description' => $payment->description,
                'metadata' => $payment->metadata,
                'enrollment' => [
                    'id' => $enrollment->id,
                    'enrollment_status' => $enrollment->enrollment_status,
                    'amount' => round($enrollment->amount, 2),
                    'payment_status' => $enrollment->payment_status,
                    'enrolled_at' => $enrollment->enrolled_at ? $enrollment->enrolled_at->format('Y-m-d H:i:s') : null,
                ],
            ]
        ]);
    }
}
