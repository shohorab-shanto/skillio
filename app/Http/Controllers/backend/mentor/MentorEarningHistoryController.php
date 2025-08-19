<?php

namespace App\Http\Controllers\backend\mentor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PaymentTransaction;
use Carbon\Carbon;

class MentorEarningHistoryController extends Controller
{
    /**
     * Display the mentor earning history page.
     */
    public function index(Request $request)
    {
        $mentor = Auth::user()->mentor;
        
        if (!$mentor) {
            abort(403, 'Mentor profile not found.');
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
        
        $payments = $query->paginate(10);
        
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
        
        return view('backend.mentor.earning-history.index', compact('payments', 'totalEarning', 'thisMonthEarning', 'todayEarning'));
    }
}
