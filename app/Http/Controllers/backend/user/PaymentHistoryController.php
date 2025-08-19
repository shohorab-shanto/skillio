<?php

namespace App\Http\Controllers\backend\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PaymentTransaction;

class PaymentHistoryController extends Controller
{
    /**
     * Display the payment history page.
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
        
        $payments = $query->paginate(10);
        
        return view('backend.user.payment-history.index', compact('payments'));
    }
}
