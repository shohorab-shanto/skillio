<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PaymentTransaction;

class UserPaymentsApiController extends Controller
{
    /**
     * Display the payment history
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get user's payment transactions through enrollments
        $query = PaymentTransaction::whereHas('enrollments', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['enrollments.enrollable.mentor.user', 'enrollments.enrollable.category'])
            ->orderBy('created_at', 'desc');
        
        // Search functionality - only by transaction ID and date
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhere('created_at', 'like', "%{$search}%");
            });
        }
        
        $perPage = $request->get('per_page', 10);
        $payments = $query->paginate($perPage);
        
        // Transform payments data
        $payments->getCollection()->transform(function ($payment) {
            $enrollment = $payment->enrollments->first();
            $enrollable = $enrollment ? $enrollment->enrollable : null;
            
            // Handle different mentor relationship structures
            $mentorName = null;
            if ($enrollable) {
                if ($enrollable->mentor && $enrollable->mentor->user) {
                    $mentorName = $enrollable->mentor->user->name;
                }
            }
            
            return [
                'id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'transaction_type' => $payment->transaction_type,
                'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                'service_type' => $enrollment ? class_basename($enrollment->enrollable_type) : 'Unknown',
                'title' => $enrollable ? $enrollable->title ?? 'N/A' : 'N/A',
                'mentor_name' => $mentorName,
                'gross_amount' => round($payment->gross_amount, 2),
                'stripe_fee' => round($payment->stripe_fee ?? 0, 2),
                'admin_amount' => round($payment->admin_amount ?? 0, 2),
                'mentor_amount' => round($payment->mentor_amount, 2),
                'currency' => $payment->currency,
                'status' => $payment->transaction_status,
                'payment_method' => $payment->payment_method_brand ? ucfirst($payment->payment_method_brand) . ' •••• ' . $payment->payment_method_last4 : 'N/A',
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
     * Get payment statistics
     */
    public function statistics(Request $request)
    {
        $user = Auth::user();
        
        // Get all user's payment transactions
        $payments = PaymentTransaction::whereHas('enrollments', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->get();
        
        $stats = [
            'total_transactions' => $payments->count(),
            'total_spent' => round($payments->sum('gross_amount'), 2),
            'total_platform_fees' => round($payments->sum('platform_fee'), 2),
            'this_month_spent' => round($payments->where('created_at', '>=', now()->startOfMonth())->sum('gross_amount'), 2),
            'last_month_spent' => round($payments->whereBetween('created_at', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth()
            ])->sum('gross_amount'), 2),
            'average_transaction' => $payments->count() > 0 ? round($payments->avg('gross_amount'), 2) : 0,
        ];
        
        // Monthly spending for last 6 months
        $monthlySpending = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyAmount = $payments->whereBetween('created_at', [
                $month->startOfMonth(),
                $month->endOfMonth()
            ])->sum('gross_amount');
            
            $monthlySpending[] = [
                'month' => $month->format('M Y'),
                'amount' => round($monthlyAmount, 2)
            ];
        }
        
        $stats['monthly_spending'] = $monthlySpending;
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get specific payment details
     */
    public function show(PaymentTransaction $payment)
    {
        $user = Auth::user();
        
        // Ensure this payment belongs to the current user
        $enrollment = $payment->enrollments()->where('user_id', $user->id)->first();
        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found or unauthorized access.'
            ], 404);
        }
        
        $payment->load(['enrollments.enrollable.mentor.user', 'enrollments.enrollable.category']);
        $enrollable = $enrollment->enrollable;
        
        // Handle different mentor relationship structures
        $mentorName = null;
        if ($enrollable) {
            if ($enrollable->mentor && $enrollable->mentor->user) {
                $mentorName = $enrollable->mentor->user->name;
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'transaction_type' => $payment->transaction_type,
                'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                'service_type' => class_basename($enrollment->enrollable_type),
                'title' => $enrollable ? $enrollable->title ?? 'N/A' : 'N/A',
                'mentor_name' => $mentorName,
                'gross_amount' => round($payment->gross_amount, 2),
                'stripe_fee' => round($payment->stripe_fee ?? 0, 2),
                'net_amount' => round($payment->net_amount ?? 0, 2),
                'admin_amount' => round($payment->admin_amount ?? 0, 2),
                'mentor_amount' => round($payment->mentor_amount, 2),
                'currency' => $payment->currency,
                'status' => $payment->transaction_status,
                'payment_method' => [
                    'type' => $payment->payment_method_type,
                    'brand' => $payment->payment_method_brand,
                    'last4' => $payment->payment_method_last4,
                    'display' => $payment->payment_method_brand ? ucfirst($payment->payment_method_brand) . ' •••• ' . $payment->payment_method_last4 : 'N/A',
                ],
                'stripe_payment_intent_id' => $payment->stripe_payment_intent_id,
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
