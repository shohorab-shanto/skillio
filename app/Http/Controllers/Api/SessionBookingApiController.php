<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SessionBooking;
use App\Models\UserEnrollment;
use App\Models\PaymentTransaction;
use App\Models\CustomerAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Customer;
use Stripe\Transfer;

class SessionBookingApiController extends Controller
{
    /**
     * Get available sessions for a mentor
     */
    public function getAvailableSessions(Request $request)
    {
        try {
            $mentorId = $request->get('mentor_id');
            $categoryId = $request->get('category_id');
            $date = $request->get('date');
            $perPage = $request->get('per_page', 10);

            $query = SessionBooking::with(['mentor.user', 'category', 'subCategories'])
                ->where('status', 'active')
                ->whereNull('user_id')
                ->where('date', '>=', now()->toDateString());

            if ($mentorId) {
                $query->where('mentor_id', $mentorId);
            }

            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }

            if ($date) {
                $query->where('date', $date);
            }

            $sessions = $query->orderBy('date', 'asc')
                ->orderBy('start_time', 'asc')
                ->paginate($perPage);

            $sessionsData = $sessions->map(function ($session) {
                return [
                    'id' => $session->id,
                    'date' => $session->date->format('Y-m-d'),
                    'start_time' => $session->start_time->format('H:i'),
                    'end_time' => $session->end_time->format('H:i'),
                    'duration_minutes' => $session->duration_in_minutes,
                    'formatted_time_slot' => $session->formatted_time_slot,
                    'fee' => $session->fee,
                    'currency' => 'USD',
                    'mentor' => [
                        'id' => $session->mentor->id,
                        'name' => $session->mentor->user->name,
                        'photo' => $session->mentor->photo_url,
                        'rating' => $session->mentor->average_rating,
                        'total_reviews' => $session->mentor->reviews_count,
                    ],
                    'category' => [
                        'id' => $session->category->id,
                        'name' => $session->category->name,
                    ],
                    'sub_categories' => $session->subCategories->map(function ($subCategory) {
                        return [
                            'id' => $subCategory->id,
                            'name' => $subCategory->name,
                        ];
                    }),
                    'is_available' => $session->is_available,
                    'has_not_started' => $session->has_not_started,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Available sessions retrieved successfully',
                'data' => [
                    'sessions' => $sessionsData,
                    'pagination' => [
                        'current_page' => $sessions->currentPage(),
                        'last_page' => $sessions->lastPage(),
                        'per_page' => $sessions->perPage(),
                        'total' => $sessions->total(),
                        'has_more_pages' => $sessions->hasMorePages(),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve available sessions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get session booking information and checkout details
     */
    public function getBookingInfo(Request $request, SessionBooking $session)
    {
        try {
            $user = Auth::user();
            
            // Check if session is available for booking
            if ($session->status != 'active' || $session->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session is not available for booking',
                    'error' => 'Session is already booked or not active'
                ], 400);
            }

            // Check if user already has a booking for this session
            $existingEnrollment = UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', SessionBooking::class)
                ->where('enrollable_id', $session->id)
                ->first();

            if ($existingEnrollment) {
                return response()->json([
                    'success' => true,
                    'message' => 'User already has a booking for this session',
                    'data' => [
                        'is_booked' => true,
                        'enrollment' => [
                            'id' => $existingEnrollment->id,
                            'status' => $existingEnrollment->enrollment_status,
                            'payment_status' => $existingEnrollment->payment_status,
                            'enrolled_at' => $existingEnrollment->enrolled_at ? $existingEnrollment->enrolled_at->toISOString() : null,
                            'amount' => $existingEnrollment->amount,
                            'currency' => $existingEnrollment->currency,
                        ]
                    ]
                ]);
            }

            // Load session relationships
            $session->load(['mentor.user', 'category', 'subCategories']);

            // Calculate pricing
            $fee = (float) $session->fee;
            $stripeFee = $this->calculateStripeFee($fee);
            $netAmount = $fee - $stripeFee;

            return response()->json([
                'success' => true,
                'message' => 'Session booking information retrieved successfully',
                'data' => [
                    'is_booked' => false,
                    'session' => [
                        'id' => $session->id,
                        'date' => $session->date->format('Y-m-d'),
                        'start_time' => $session->start_time->format('H:i'),
                        'end_time' => $session->end_time->format('H:i'),
                        'duration_minutes' => $session->duration_in_minutes,
                        'formatted_time_slot' => $session->formatted_time_slot,
                        'mentor' => [
                            'id' => $session->mentor->id,
                            'name' => $session->mentor->user->name,
                            'photo' => $session->mentor->photo_url,
                            'rating' => $session->mentor->average_rating,
                            'total_reviews' => $session->mentor->reviews_count,
                        ],
                        'category' => [
                            'id' => $session->category->id,
                            'name' => $session->category->name,
                        ],
                        'sub_categories' => $session->subCategories->map(function ($subCategory) {
                            return [
                                'id' => $subCategory->id,
                                'name' => $subCategory->name,
                            ];
                        }),
                        'is_available' => $session->is_available,
                        'has_not_started' => $session->has_not_started,
                    ],
                    'pricing' => [
                        'fee' => round($fee, 2),
                        'stripe_fee' => round($stripeFee, 2),
                        'net_amount' => round($netAmount, 2),
                        'currency' => 'USD',
                    ],
                    'eligibility' => [
                        'can_book' => true,
                        'reason' => null,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve session booking information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Book a session with payment processing
     */
    public function bookSession(Request $request, SessionBooking $session)
    {
        try {
            $user = Auth::user();
            
            // Validate request
            $validator = Validator::make($request->all(), [
                'payment_method_id' => 'required|string',
                'cardholder_name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if session is available
            if ($session->status != 'active' || $session->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session is not available for booking',
                    'error' => 'Session is already booked or not active'
                ], 400);
            }

            // Check if user already has a booking for this session
            $existingEnrollment = UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', SessionBooking::class)
                ->where('enrollable_id', $session->id)
                ->first();

            if ($existingEnrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have a booking for this session',
                    'error' => 'Duplicate booking attempt'
                ], 400);
            }

            // Verify payment method with Stripe
            $paymentMethodVerification = $this->verifyPaymentMethod($request->payment_method_id, $request->cardholder_name);
            if (!$paymentMethodVerification['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $paymentMethodVerification['message'],
                    'error' => $paymentMethodVerification['error']
                ], $paymentMethodVerification['status_code']);
            }

            DB::beginTransaction();

            try {
                // Set Stripe API key
                $stripeKey = config('services.stripe.secret_key');
                Stripe::setApiKey($stripeKey);

                // Calculate amounts
                $grossAmount = $session->fee;
                $stripeFee = $this->calculateStripeFee($grossAmount);
                $netAmount = $grossAmount - $stripeFee;
                $mentorAmount = $netAmount * 0.80; // 80% for mentor
                $adminAmount = $netAmount * 0.20; // 20% for admin

                // Get or create Stripe customer
                $stripeCustomerId = $this->getOrCreateStripeCustomer($user);

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
                    'description' => "Session booking with {$session->mentor->user->name}",
                    'metadata' => [
                        'type' => 'session',
                        'session_id' => $session->id,
                        'user_id' => $user->id,
                    ],
                ]);

                if ($paymentIntent->status == 'requires_action') {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Payment requires additional authentication',
                        'requires_action' => true,
                        'payment_intent_client_secret' => $paymentIntent->client_secret,
                    ], 402);
                }

                if ($paymentIntent->status == 'succeeded') {
                    // Create user enrollment
                    $enrollment = UserEnrollment::create([
                        'user_id' => $user->id,
                        'enrollment_status' => 'active',
                        'enrollable_type' => SessionBooking::class,
                        'enrollable_id' => $session->id,
                        'amount' => $grossAmount,
                        'currency' => 'USD',
                        'payment_status' => 'paid',
                        'payment_method' => 'stripe',
                        'enrolled_at' => now(),
                    ]);

                    // Create payment transaction
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
                        'description' => "Session booking with {$session->mentor->user->name}",
                    ]);

                    // Update enrollment with transaction ID
                    $enrollment->update([
                        'payment_transaction_id' => $transaction->id,
                    ]);

                    // Update session status
                    $session->update([
                        'status' => 'booked',
                        'user_id' => $user->id,
                        'payment_status' => 'paid',
                    ]);

                    // Create conversation for this enrollment
                    $conversation = $enrollment->createConversation();

                    // Create notification for mentor
                    \App\Services\NotificationService::createSessionBookingNotification($user, $session);

                    // Create transfer to mentor if they have a Stripe Connect account
                    $this->createMentorTransfer($session, $transaction, $mentorAmount);

                    DB::commit();

                    return response()->json([
                        'success' => true,
                        'message' => 'Successfully booked session',
                        'data' => [
                            'enrollment' => [
                                'id' => $enrollment->id,
                                'status' => $enrollment->enrollment_status,
                                'payment_status' => $enrollment->payment_status,
                                'enrolled_at' => $enrollment->enrolled_at->toISOString(),
                                'amount' => $enrollment->amount,
                                'currency' => $enrollment->currency,
                            ],
                            'transaction' => [
                                'id' => $transaction->id,
                                'transaction_id' => $transaction->transaction_id,
                                'status' => $transaction->transaction_status,
                                'amount' => $transaction->gross_amount,
                                'currency' => $transaction->currency,
                            ],
                            'session' => [
                                'id' => $session->id,
                                'date' => $session->date->format('Y-m-d'),
                                'start_time' => $session->start_time->format('H:i'),
                                'end_time' => $session->end_time->format('H:i'),
                                'mentor_name' => $session->mentor->user->name,
                            ]
                        ]
                    ], 201);

                } else {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Payment failed',
                        'error' => 'Payment was not successful',
                        'payment_status' => $paymentIntent->status
                    ], 400);
                }

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to book session',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's session bookings
     */
    public function getUserBookings(Request $request)
    {
        try {
            $user = Auth::user();
            $perPage = $request->get('per_page', 10);
            $status = $request->get('status', 'all');

            $query = UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', SessionBooking::class)
                ->with(['enrollable.mentor.user', 'enrollable.category', 'paymentTransaction']);

            if ($status != 'all') {
                $query->where('enrollment_status', $status);
            }

            $bookings = $query->orderBy('created_at', 'desc')
                ->paginate($perPage);

            $bookingsData = $bookings->map(function ($enrollment) {
                $session = $enrollment->enrollable;
                return [
                    'id' => $enrollment->id,
                    'enrollment_status' => $enrollment->enrollment_status,
                    'payment_status' => $enrollment->payment_status,
                    'enrolled_at' => $enrollment->enrolled_at ? $enrollment->enrolled_at->toISOString() : null,
                    'started_at' => $enrollment->started_at ? $enrollment->started_at->toISOString() : null,
                    'completed_at' => $enrollment->completed_at ? $enrollment->completed_at->toISOString() : null,
                    'amount' => $enrollment->amount,
                    'currency' => $enrollment->currency,
                    'session' => [
                        'id' => $session->id,
                        'date' => $session->date->format('Y-m-d'),
                        'start_time' => $session->start_time->format('H:i'),
                        'end_time' => $session->end_time->format('H:i'),
                        'duration_minutes' => $session->duration_in_minutes,
                        'formatted_time_slot' => $session->formatted_time_slot,
                        'fee' => $session->fee,
                        'status' => $session->status,
                        'mentor' => [
                            'id' => $session->mentor->id,
                            'name' => $session->mentor->user->name,
                            'photo' => $session->mentor->photo_url,
                        ],
                        'category' => [
                            'id' => $session->category->id,
                            'name' => $session->category->name,
                        ],
                    ],
                    'transaction' => $enrollment->paymentTransaction ? [
                        'id' => $enrollment->paymentTransaction->id,
                        'transaction_id' => $enrollment->paymentTransaction->transaction_id,
                        'status' => $enrollment->paymentTransaction->transaction_status,
                        'amount' => $enrollment->paymentTransaction->gross_amount,
                        'currency' => $enrollment->paymentTransaction->currency,
                    ] : null,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'User session bookings retrieved successfully',
                'data' => [
                    'bookings' => $bookingsData,
                    'pagination' => [
                        'current_page' => $bookings->currentPage(),
                        'last_page' => $bookings->lastPage(),
                        'per_page' => $bookings->perPage(),
                        'total' => $bookings->total(),
                        'has_more_pages' => $bookings->hasMorePages(),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user session bookings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific booking details
     */
    public function getBookingDetails(UserEnrollment $enrollment)
    {
        try {
            $user = Auth::user();
            
            // Check if user owns this booking
            if ($enrollment->user_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to booking',
                    'error' => 'You can only view your own bookings'
                ], 403);
            }

            // Check if this is a session booking
            if ($enrollment->enrollable_type != SessionBooking::class) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid booking type',
                    'error' => 'This is not a session booking'
                ], 400);
            }

            // Load relationships
            $enrollment->load(['enrollable.mentor.user', 'enrollable.category', 'enrollable.subCategories', 'paymentTransaction', 'conversation']);

            $session = $enrollment->enrollable;

            return response()->json([
                'success' => true,
                'message' => 'Booking details retrieved successfully',
                'data' => [
                    'enrollment' => [
                        'id' => $enrollment->id,
                        'enrollment_status' => $enrollment->enrollment_status,
                        'payment_status' => $enrollment->payment_status,
                        'enrolled_at' => $enrollment->enrolled_at ? $enrollment->enrolled_at->toISOString() : null,
                        'started_at' => $enrollment->started_at ? $enrollment->started_at->toISOString() : null,
                        'completed_at' => $enrollment->completed_at ? $enrollment->completed_at->toISOString() : null,
                        'cancelled_at' => $enrollment->cancelled_at ? $enrollment->cancelled_at->toISOString() : null,
                        'amount' => $enrollment->amount,
                        'currency' => $enrollment->currency,
                        'notes' => $enrollment->notes,
                    ],
                    'session' => [
                        'id' => $session->id,
                        'date' => $session->date->format('Y-m-d'),
                        'start_time' => $session->start_time->format('H:i'),
                        'end_time' => $session->end_time->format('H:i'),
                        'duration_minutes' => $session->duration_in_minutes,
                        'formatted_time_slot' => $session->formatted_time_slot,
                        'fee' => $session->fee,
                        'status' => $session->status,
                        'has_not_started' => $session->has_not_started,
                        'mentor' => [
                            'id' => $session->mentor->id,
                            'name' => $session->mentor->user->name,
                            'photo' => $session->mentor->photo_url,
                            'rating' => $session->mentor->average_rating,
                            'total_reviews' => $session->mentor->reviews_count,
                        ],
                        'category' => [
                            'id' => $session->category->id,
                            'name' => $session->category->name,
                        ],
                        'sub_categories' => $session->subCategories->map(function ($subCategory) {
                            return [
                                'id' => $subCategory->id,
                                'name' => $subCategory->name,
                            ];
                        }),
                    ],
                    'transaction' => $enrollment->paymentTransaction ? [
                        'id' => $enrollment->paymentTransaction->id,
                        'transaction_id' => $enrollment->paymentTransaction->transaction_id,
                        'status' => $enrollment->paymentTransaction->transaction_status,
                        'amount' => $enrollment->paymentTransaction->gross_amount,
                        'currency' => $enrollment->paymentTransaction->currency,
                        'payment_method_type' => $enrollment->paymentTransaction->payment_method_type,
                        'created_at' => $enrollment->paymentTransaction->created_at->toISOString(),
                    ] : null,
                    'conversation' => $enrollment->conversation ? [
                        'id' => $enrollment->conversation->id,
                        'status' => $enrollment->conversation->status,
                    ] : null,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve booking details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a session booking - DISABLED
     * Users cannot cancel bookings after payment
     */
    public function cancelBooking(UserEnrollment $enrollment)
    {
        return response()->json([
            'success' => false,
            'message' => 'Cancellation not allowed',
            'error' => 'Session bookings cannot be cancelled after payment. You can switch to another available session with the same mentor and price.',
            'suggestion' => 'Use the switch session endpoint to change your booking'
        ], 400);
    }

    /**
     * Switch to a different session with same mentor and price
     */
    public function switchSession(Request $request, UserEnrollment $enrollment)
    {
        try {
            $user = Auth::user();

            // Check if user owns this enrollment
            if ($enrollment->user_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                    'error' => 'You can only switch your own bookings'
                ], 403);
            }

            // Check if enrollment is for a session
            if ($enrollment->enrollable_type != SessionBooking::class) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid enrollment type',
                    'error' => 'This enrollment is not for a session'
                ], 400);
            }

            // Check if enrollment can be switched
            if ($enrollment->enrollment_status != 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot switch this booking',
                    'error' => 'Only active bookings can be switched'
                ], 400);
            }

            // Validate request
            $validator = Validator::make($request->all(), [
                'new_session_id' => 'required|integer|exists:session_bookings,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $currentSession = $enrollment->enrollable;
            $newSession = SessionBooking::find($request->new_session_id);

            // Check if new session exists and is available
            if (!$newSession || $newSession->status != 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not available',
                    'error' => 'The selected session is not available for booking'
                ], 400);
            }

            // Check if same mentor
            if ($currentSession->mentor_id != $newSession->mentor_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot switch to different mentor',
                    'error' => 'You can only switch to sessions with the same mentor'
                ], 400);
            }

            // Check if same price
            if ($currentSession->fee != $newSession->fee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot switch to different price',
                    'error' => 'You can only switch to sessions with the same price'
                ], 400);
            }

            // Check if new session is not already booked
            $existingBooking = UserEnrollment::where('enrollable_type', SessionBooking::class)
                ->where('enrollable_id', $newSession->id)
                ->where('enrollment_status', 'active')
                ->first();

            if ($existingBooking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session already booked',
                    'error' => 'The selected session is already booked by another user'
                ], 400);
            }

            DB::beginTransaction();

            try {
                // Free up current session
                $currentSession->update([
                    'status' => 'active',
                    'user_id' => null,
                    'booked_at' => null,
                ]);

                // Book new session
                $newSession->update([
                    'status' => 'booked',
                    'user_id' => $user->id,
                    'booked_at' => now(),
                ]);

                // Update enrollment to point to new session
                $enrollment->update([
                    'enrollable_id' => $newSession->id,
                    'switched_at' => now(),
                ]);

                // Update payment transaction description
                if ($enrollment->paymentTransaction) {
                    $enrollment->paymentTransaction->update([
                        'description' => "Session booking with {$newSession->mentor->user->name} (switched from session #{$currentSession->id})",
                    ]);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Session switched successfully',
                    'data' => [
                        'enrollment_id' => $enrollment->id,
                        'old_session' => [
                            'id' => $currentSession->id,
                            'date' => $currentSession->date,
                            'start_time' => $currentSession->start_time,
                            'end_time' => $currentSession->end_time,
                        ],
                        'new_session' => [
                            'id' => $newSession->id,
                            'date' => $newSession->date,
                            'start_time' => $newSession->start_time,
                            'end_time' => $newSession->end_time,
                        ],
                        'switched_at' => $enrollment->switched_at->toISOString()
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to switch session',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available sessions for switching (same mentor, same price)
     */
    public function getSwitchableSessions(Request $request, UserEnrollment $enrollment)
    {
        try {
            $user = Auth::user();

            // Check if user owns this enrollment
            if ($enrollment->user_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                    'error' => 'You can only view switchable sessions for your own bookings'
                ], 403);
            }

            // Check if enrollment is for a session
            if ($enrollment->enrollable_type != SessionBooking::class) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid enrollment type',
                    'error' => 'This enrollment is not for a session'
                ], 400);
            }

            $currentSession = $enrollment->enrollable;
            $perPage = $request->get('per_page', 10);

            // Get available sessions with same mentor and price
            $availableSessions = SessionBooking::where('mentor_id', $currentSession->mentor_id)
                ->where('fee', $currentSession->fee)
                ->where('status', 'active')
                ->where('id', '!=', $currentSession->id) // Exclude current session
                ->where('date', '>=', now()->toDateString()) // Only future sessions
                ->with(['mentor.user', 'category', 'subCategories'])
                ->orderBy('date')
                ->orderBy('start_time')
                ->paginate($perPage);

            $sessionsData = $availableSessions->map(function ($session) {
                return [
                    'id' => $session->id,
                    'date' => $session->date,
                    'start_time' => $session->start_time,
                    'end_time' => $session->end_time,
                    'duration' => $session->duration_in_minutes,
                    'type' => $session->type,
                    'fee' => $session->fee,
                    'mentor' => [
                        'id' => $session->mentor->id,
                        'name' => $session->mentor->user->name,
                        'photo' => $session->mentor->photo ? asset('storage/' . $session->mentor->photo) : asset('assets/images/user-avatar.png'),
                    ],
                    'category' => [
                        'id' => $session->category->id,
                        'name' => $session->category->name,
                    ],
                    'sub_categories' => $session->subCategories->map(function ($subCategory) {
                        return [
                            'id' => $subCategory->id,
                            'name' => $subCategory->name,
                        ];
                    }),
                    'is_available' => $session->is_available,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Switchable sessions retrieved successfully',
                'data' => [
                    'current_session' => [
                        'id' => $currentSession->id,
                        'date' => $currentSession->date,
                        'start_time' => $currentSession->start_time,
                        'end_time' => $currentSession->end_time,
                    ],
                    'available_sessions' => $sessionsData,
                    'pagination' => [
                        'current_page' => $availableSessions->currentPage(),
                        'last_page' => $availableSessions->lastPage(),
                        'per_page' => $availableSessions->perPage(),
                        'total' => $availableSessions->total(),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve switchable sessions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate Stripe fee (2.9% + 30¢)
     */
    private function calculateStripeFee($amount)
    {
        return round(($amount * 0.029) + 0.30, 2);
    }

    /**
     * Get or create Stripe customer
     */
    private function getOrCreateStripeCustomer($user)
    {
        // Check if user already has a Stripe customer ID
        $customerAccount = CustomerAccount::where('user_id', $user->id)->first();
        
        if ($customerAccount && $customerAccount->stripe_customer_id) {
            return $customerAccount->stripe_customer_id;
        }

        // Create new Stripe customer
        $stripeCustomer = Customer::create([
            'email' => $user->email,
            'name' => $user->name,
            'metadata' => [
                'user_id' => $user->id,
            ],
        ]);

        // Store customer ID in database
        if ($customerAccount) {
            $customerAccount->update(['stripe_customer_id' => $stripeCustomer->id]);
        } else {
            CustomerAccount::create([
                'user_id' => $user->id,
                'stripe_customer_id' => $stripeCustomer->id,
            ]);
        }

        return $stripeCustomer->id;
    }

    /**
     * Create transfer to mentor
     */
    private function createMentorTransfer($session, $transaction, $mentorAmount)
    {
        try {
            // Check if mentor has Stripe Connect account
            $mentor = $session->mentor;
            if (!$mentor->stripe_connect_account_id) {
                return; // Mentor doesn't have Stripe Connect, skip transfer
            }

            // Create transfer
            $transfer = Transfer::create([
                'amount' => (int)($mentorAmount * 100), // Convert to cents
                'currency' => 'usd',
                'destination' => $mentor->stripe_connect_account_id,
                'transfer_group' => $transaction->transaction_id,
                'metadata' => [
                    'transaction_id' => $transaction->id,
                    'session_id' => $session->id,
                    'mentor_id' => $mentor->id,
                ],
            ]);

            // Update transaction with transfer info
            $transaction->update([
                'stripe_transfer_id' => $transfer->id,
                'transfer_status' => 'pending',
                'transfer_amount' => $mentorAmount,
            ]);

        } catch (\Exception $e) {
            // Log error but don't fail the booking
            \Log::error('Failed to create mentor transfer: ' . $e->getMessage());
        }
    }

    /**
     * Verify payment method with Stripe
     */
    private function verifyPaymentMethod($paymentMethodId, $cardholderName)
    {
        try {
            // Set Stripe API key
            $stripeKey = config('services.stripe.secret_key');
            Stripe::setApiKey($stripeKey);

            // Retrieve payment method from Stripe
            $paymentMethod = PaymentMethod::retrieve($paymentMethodId);

            // Check if payment method exists
            if (!$paymentMethod) {
                return [
                    'success' => false,
                    'message' => 'Invalid payment method',
                    'error' => 'Payment method not found',
                    'status_code' => 400
                ];
            }

            // Check if payment method is a card
            if ($paymentMethod->type !== 'card') {
                return [
                    'success' => false,
                    'message' => 'Invalid payment method type',
                    'error' => 'Only card payments are supported',
                    'status_code' => 400
                ];
            }

            // Get card details
            $card = $paymentMethod->card;
            $billingName = $paymentMethod->billing_details->name ?? '';

            // Verify cardholder name matches (case insensitive)
            if (!empty($billingName) && !empty($cardholderName)) {
                if (strtolower(trim($billingName)) !== strtolower(trim($cardholderName))) {
                    return [
                        'success' => false,
                        'message' => 'Cardholder name mismatch',
                        'error' => 'The cardholder name does not match the payment method',
                        'status_code' => 400
                    ];
                }
            }

            // Check if card is expired
            $currentYear = (int)date('Y');
            $currentMonth = (int)date('n');
            
            if ($card->exp_year < $currentYear || 
                ($card->exp_year == $currentYear && $card->exp_month < $currentMonth)) {
                return [
                    'success' => false,
                    'message' => 'Card expired',
                    'error' => 'The payment method has expired',
                    'status_code' => 400
                ];
            }

            // Check if card brand is supported
            $supportedBrands = ['visa', 'mastercard', 'amex', 'discover'];
            if (!in_array(strtolower($card->brand), $supportedBrands)) {
                return [
                    'success' => false,
                    'message' => 'Unsupported card brand',
                    'error' => 'Card brand ' . $card->brand . ' is not supported',
                    'status_code' => 400
                ];
            }

            // All checks passed
            return [
                'success' => true,
                'message' => 'Payment method verified successfully',
                'payment_method' => $paymentMethod,
                'status_code' => 200
            ];

        } catch (\Stripe\Exception\InvalidRequestException $e) {
            return [
                'success' => false,
                'message' => 'Invalid payment method',
                'error' => 'Payment method not found or invalid',
                'status_code' => 400
            ];
        } catch (\Stripe\Exception\AuthenticationException $e) {
            return [
                'success' => false,
                'message' => 'Payment verification failed',
                'error' => 'Stripe authentication error',
                'status_code' => 500
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Payment verification failed',
                'error' => $e->getMessage(),
                'status_code' => 500
            ];
        }
    }
}
