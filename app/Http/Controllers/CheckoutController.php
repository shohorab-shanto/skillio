<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SessionBooking;
use App\Models\Course;
use App\Models\UserEnrollment;
use App\Models\PaymentTransaction;
use App\Models\CustomerAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Customer;
use Stripe\Transfer;

class CheckoutController extends Controller
{
    /**
     * Display the checkout page for a session booking.
     */
    public function sessionCheckout(SessionBooking $sessionBooking)
    {
        // Check if the session is available for booking
        if ($sessionBooking->status != 'active') {
            abort(404, 'Session is not available for booking.');
        }

        // Check if user already has an enrollment for this session
        $existingEnrollment = UserEnrollment::where('user_id', Auth::id())
            ->where('enrollable_type', SessionBooking::class)
            ->where('enrollable_id', $sessionBooking->id)
            ->first();

        if ($existingEnrollment) {
            return redirect()->route('dashboard')->with('error', 'You have already enrolled in this session.');
        }

        // Get session details with relationships
        $sessionBooking->load(['mentor.user', 'category', 'subCategories']);

        return view('frontend.checkout.index', [
            'type' => 'session',
            'item' => $sessionBooking,
            'amount' => $sessionBooking->fee,
            'title' => 'Session with ' . $sessionBooking->mentor->user->name
        ]);
    }

    /**
     * Display the checkout page for a course.
     */
    public function courseCheckout(Course $course)
    {
        // Check if the course is available
        if ($course->status != 'approved') {
            abort(404, 'Course is not available for enrollment.');
        }

        // Check if user already has an enrollment for this course
        $existingEnrollment = UserEnrollment::where('user_id', Auth::id())
            ->where('enrollable_type', Course::class)
            ->where('enrollable_id', $course->id)
            ->first();

        if ($existingEnrollment) {
            return redirect()->route('dashboard')->with('error', 'You have already enrolled in this course.');
        }

        // Get course details with relationships
        $course->load(['mentor.user', 'category', 'subCategories']);

        return view('frontend.checkout.index', [
            'type' => 'course',
            'item' => $course,
            'amount' => $course->price,
            'title' => $course->title
        ]);
    }

    /**
     * Process the payment for a session booking.
     */
    public function processSessionPayment(Request $request, SessionBooking $sessionBooking)
    {
        return $this->processPayment($request, $sessionBooking, 'session');
    }

    /**
     * Process the payment for a course.
     */
    public function processCoursePayment(Request $request, Course $course)
    {
        return $this->processPayment($request, $course, 'course');
    }

    /**
     * Generic payment processing method.
     */
    private function processPayment(Request $request, $item, string $type)
    {
        // Validate the request
        $request->validate([
            'payment_method_id' => 'required|string',
        ]);

        // Check if item is still available
        if ($type == 'session' && $item->status != 'active') {
            return back()->withErrors(['error' => 'Session is no longer available for booking.']);
        }
        if ($type == 'course' && $item->status != 'approved') {
            return back()->withErrors(['error' => 'Course is no longer available for enrollment.']);
        }

        try {
            DB::beginTransaction();

            // Set Stripe API key
            $stripeKey = config('services.stripe.secret_key');
            Stripe::setApiKey($stripeKey);

            // Calculate amounts for revenue sharing
            $grossAmount = $type == 'session' ? $item->fee : $item->price;
            $stripeFee = $this->calculateStripeFee($grossAmount);
            $netAmount = $grossAmount - $stripeFee;
            $mentorAmount = $netAmount * 0.80; // 80% for mentor
            $adminAmount = $netAmount * 0.20; // 20% for admin

            // Get or create Stripe customer
            $stripeCustomerId = $this->getOrCreateStripeCustomer();

            // Create Stripe Payment Intent
            $paymentIntent = PaymentIntent::create([
                'amount' => (int)($grossAmount * 100), // Convert to cents
                'currency' => 'usd',
                'customer' => $stripeCustomerId,
                'payment_method' => $request->payment_method_id,
                'confirm' => true,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never'
                ],
                'description' => $type == 'session' 
                    ? "Session booking with {$item->mentor->user->name}"
                    : "Course enrollment: {$item->title}",
                'metadata' => [
                    'type' => $type,
                    'item_id' => $item->id,
                    'user_id' => Auth::id(),
                ],
            ]);

            if ($paymentIntent->status == 'requires_action') {
                // Handle 3D Secure authentication
                return response()->json([
                    'requires_action' => true,
                    'payment_intent_client_secret' => $paymentIntent->client_secret,
                ]);
            }

            if ($paymentIntent->status == 'succeeded') {
                // Create user enrollment (active for localhost testing)
                $enrollment = UserEnrollment::create([
                    'user_id' => Auth::id(),
                    'enrollment_status' => 'active',
                    'enrollable_type' => get_class($item),
                    'enrollable_id' => $item->id,
                    'amount' => $grossAmount,
                    'currency' => 'USD',
                    'payment_status' => 'paid',
                    'payment_method' => 'stripe',
                    'enrolled_at' => now(),
                ]);

                // Create notification for mentor
                if ($type == 'session') {
                    \App\Services\NotificationService::createSessionBookingNotification(Auth::user(), $item);
                } else {
                    \App\Services\NotificationService::createCourseEnrollmentNotification(Auth::user(), $item);
                }

                // Create payment transaction (completed for localhost testing)
                $transaction = PaymentTransaction::create([
                    'transaction_id' => 'TXN_' . uniqid(),
                    'transaction_type' => 'payment',
                    'transaction_status' => 'completed',
                    'gross_amount' => $grossAmount,
                    'stripe_fee' => $stripeFee,
                    'net_amount' => $netAmount,
                    'mentor_amount' => $mentorAmount,
                    'admin_amount' => $adminAmount,
                    'currency' => 'USD',
                    'stripe_payment_intent_id' => $paymentIntent->id,
                    'stripe_customer_id' => $stripeCustomerId,
                    'stripe_charge_id' => $paymentIntent->latest_charge,
                    'payment_method_type' => 'card',
                    'description' => $type == 'session' 
                        ? "Session booking with {$item->mentor->user->name}"
                        : "Course enrollment: {$item->title}",
                ]);

                // Update enrollment with transaction ID
                $enrollment->update([
                    'payment_transaction_id' => $transaction->id,
                ]);

                // Create conversation for this enrollment
                $conversation = $enrollment->createConversation();

                // Update item status for localhost testing
                if ($type == 'session') {
                    $item->update([
                        'status' => 'booked',
                        'user_id' => Auth::id(),
                        'booked_at' => now(),
                    ]);
                }

                // Create transfer to mentor if they have a Stripe Connect account
                $this->createMentorTransfer($item, $transaction, $mentorAmount);

                DB::commit();

                // Redirect to payment success page
                return redirect()->route('payment.success', [
                    'transaction_id' => $transaction->transaction_id,
                    'enrollment_id' => $enrollment->id
                ]);
            } else {
                throw new \Exception('Payment failed: ' . $paymentIntent->status);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Redirect to payment failure page
            return redirect()->route('payment.failure', [
                'error' => $e->getMessage(),
                'type' => $type,
                'item_id' => $item->id
            ]);
        }
    }

    /**
     * Calculate Stripe processing fee (2.9% + 30 cents).
     */
    private function calculateStripeFee($amount)
    {
        return ($amount * 0.029) + 0.30;
    }

    /**
     * Get or create Stripe customer ID for the authenticated user.
     */
    private function getOrCreateStripeCustomer()
    {
        try {
            $user = Auth::user();
            
            // First, check if user already has a customer account in our database
            $customerAccount = CustomerAccount::where('user_id', $user->id)->first();
            
            if ($customerAccount && $customerAccount->stripe_customer_id) {
                // Verify the customer still exists in Stripe
                try {
                    $stripeCustomer = Customer::retrieve($customerAccount->stripe_customer_id);
                    
                    if ($stripeCustomer && !$stripeCustomer->deleted) {
                        return $customerAccount->stripe_customer_id;
                    }
                } catch (\Exception $e) {
                    // Existing Stripe customer not found, will create new one
                }
            }
            
            // Create new Stripe customer with only required fields
            $customerData = [
                'email' => $user->email,
                'name' => $user->name,
                'metadata' => [
                    'user_id' => $user->id,
                    'platform' => 'skillio',
                    'created_at' => now()->toISOString()
                ]
            ];
            
            // Only add phone if it exists (phone is a simple string, safe for Stripe)
            if (!empty($user->phone)) {
                $customerData['phone'] = $user->phone;
            }
            
            // Skip address for now - Stripe requires complex address object
            // We can add proper address handling later if needed
            
            $stripeCustomer = Customer::create($customerData);
            
            // Store customer account in our database
            $accountData = [
                'stripe_customer_id' => $stripeCustomer->id,
                'status' => 'active',
                'metadata' => [
                    'platform' => 'skillio',
                    'created_via' => 'checkout'
                ]
            ];
            
            CustomerAccount::updateOrCreate(
                ['user_id' => $user->id],
                $accountData
            );
            
            return $stripeCustomer->id;
            
        } catch (\Exception $e) {
            throw new \Exception('Unable to create Stripe customer: ' . $e->getMessage());
        }
    }

    /**
     * Create a transfer to the mentor's Stripe Connect account.
     */
    private function createMentorTransfer($item, PaymentTransaction $transaction, $mentorAmount)
    {
        try {
            // Get the mentor from the item (session or course)
            $mentor = $item->mentor;
            
            // Check if mentor has an active Stripe Connect account
            if (!$mentor->canReceiveTransfers()) {
                // No transfer needed - mentor doesn't have active Stripe Connect account
                return;
            }

            // Create transfer to mentor's Stripe Connect account
            $transfer = Transfer::create([
                'amount' => (int)($mentorAmount * 100), // Convert to cents
                'currency' => 'usd',
                'destination' => $mentor->stripe_connect_account_id,
                'description' => "Payment for " . ($item instanceof \App\Models\SessionBooking 
                    ? "session booking on " . $item->date->format('M d, Y') 
                    : "course: " . $item->title),
                'metadata' => [
                    'transaction_id' => $transaction->transaction_id,
                    'mentor_id' => $mentor->id,
                    'mentor_user_id' => $mentor->user_id,
                    'item_type' => $item instanceof \App\Models\SessionBooking ? 'session' : 'course',
                    'item_id' => $item->id,
                ],
            ]);

            // Update transaction with transfer details
            $transaction->update([
                'stripe_transfer_id' => $transfer->id,
                'transfer_status' => 'pending',
                'transfer_amount' => $mentorAmount,
                'transfer_destination_account' => $mentor->stripe_connect_account_id,
                'transfer_created_at' => now(),
            ]);

        } catch (\Exception $e) {
            // Transfer failed - log the error but don't fail the entire payment
            // The payment was successful, we just couldn't transfer to mentor
            // This can be retried later or handled manually
            $transaction->update([
                'transfer_status' => 'failed',
                'transfer_failure_reason' => 'Transfer creation failed: ' . $e->getMessage(),
            ]);
        }
    }
}
