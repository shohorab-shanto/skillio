<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use App\Models\UserEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentTransaction::with(['enrollments.user', 'enrollments.enrollable']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhere('stripe_payment_intent_id', 'like', "%{$search}%")
                  ->orWhere('stripe_customer_id', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('enrollments.user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Transaction Status filter
        if ($request->filled('transaction_status')) {
            $query->where('transaction_status', $request->transaction_status);
        }

        // Transaction Type filter
        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        // Amount range filter
        if ($request->filled('amount_min')) {
            $query->where('gross_amount', '>=', $request->amount_min);
        }
        if ($request->filled('amount_max')) {
            $query->where('gross_amount', '<=', $request->amount_max);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Currency filter
        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }

        $transactions = $query->latest()->paginate(15);

        // Get summary statistics
        $summary = $this->getTransactionSummary();

        return view('admin.transactions.index', compact('transactions', 'summary'));
    }

    public function show(PaymentTransaction $transaction)
    {
        $transaction->load(['enrollments.user', 'enrollments.enrollable']);
        
        return view('admin.transactions.show', compact('transaction'));
    }

    private function getTransactionSummary()
    {
        $summary = [
            'total_transactions' => PaymentTransaction::count(),
            'total_gross_amount' => PaymentTransaction::sum('gross_amount'),
            'total_admin_amount' => PaymentTransaction::where('transaction_status', 'completed')->sum('admin_amount'),
            'total_mentor_amount' => PaymentTransaction::where('transaction_status', 'completed')->sum('mentor_amount'),
            'total_stripe_fees' => PaymentTransaction::sum('stripe_fee'),
            'completed_transactions' => PaymentTransaction::where('transaction_status', 'completed')->count(),
            'pending_transactions' => PaymentTransaction::where('transaction_status', 'pending')->count(),
            'failed_transactions' => PaymentTransaction::where('transaction_status', 'failed')->count(),
        ];

        return $summary;
    }

    public function getTransactionStats()
    {
        $stats = [
            'daily_revenue' => PaymentTransaction::where('transaction_status', 'completed')
                ->whereDate('created_at', today())
                ->sum('gross_amount'),
            'weekly_revenue' => PaymentTransaction::where('transaction_status', 'completed')
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->sum('gross_amount'),
            'monthly_revenue' => PaymentTransaction::where('transaction_status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->sum('gross_amount'),
            'total_customers' => PaymentTransaction::distinct('stripe_customer_id')->count(),
        ];

        return response()->json($stats);
    }
}
