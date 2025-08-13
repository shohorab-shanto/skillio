<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentTransaction;
use App\Models\UserEnrollment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentResultController extends Controller
{
    /**
     * Display payment success page
     */
    public function success(Request $request)
    {
        $transactionId = $request->query('transaction_id');
        $enrollmentId = $request->query('enrollment_id');
        
        // Get payment details
        $transaction = null;
        $enrollment = null;
        
        if ($transactionId) {
            $transaction = PaymentTransaction::where('transaction_id', $transactionId)->first();
        }
        
        if ($enrollmentId) {
            $enrollment = UserEnrollment::with(['enrollable', 'paymentTransaction'])
                ->where('id', $enrollmentId)
                ->where('user_id', Auth::id())
                ->first();
        }
        
        // Log success page view
        Log::info('Payment success page viewed', [
            'user_id' => Auth::id(),
            'transaction_id' => $transactionId,
            'enrollment_id' => $enrollmentId
        ]);
        
        return view('frontend.payment.success', compact('transaction', 'enrollment'));
    }
    
    /**
     * Display payment failure page
     */
    public function failure(Request $request)
    {
        $error = $request->query('error', 'Payment processing failed');
        $transactionId = $request->query('transaction_id');
        $type = $request->query('type', 'session');
        $itemId = $request->query('item_id');
        
        // Get failed transaction details if available
        $transaction = null;
        if ($transactionId) {
            $transaction = PaymentTransaction::where('transaction_id', $transactionId)->first();
        }
        
        // Log failure page view
        Log::info('Payment failure page viewed', [
            'user_id' => Auth::id(),
            'error' => $error,
            'transaction_id' => $transactionId,
            'type' => $type,
            'item_id' => $itemId
        ]);
        
        return view('frontend.payment.failure', compact('error', 'transaction', 'type', 'itemId'));
    }
}