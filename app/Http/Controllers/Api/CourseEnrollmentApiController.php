<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
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

class CourseEnrollmentApiController extends Controller
{
    /**
     * Get course enrollment information and checkout details
     */
    public function getEnrollmentInfo(Request $request, Course $course)
    {
        try {
            $user = Auth::user();
            
            // Check if course is available for enrollment
            if ($course->status != 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Course is not available for enrollment',
                    'error' => 'Course status is not approved'
                ], 400);
            }

            // Check if user is already enrolled
            $existingEnrollment = UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', Course::class)
                ->where('enrollable_id', $course->id)
                ->first();

            if ($existingEnrollment) {
                return response()->json([
                    'success' => true,
                    'message' => 'User is already enrolled in this course',
                    'data' => [
                        'is_enrolled' => true,
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

            // Load course relationships
            $course->load(['mentor.user', 'category', 'subCategories']);

            // Calculate pricing
            $originalPrice = (float) $course->price;
            $discountedPrice = (float) $course->discounted_price;
            $discount = (float) $course->discount;
            $finalPrice = $discountedPrice;

            // Calculate Stripe fees (2.9% + 30¢)
            $stripeFee = $this->calculateStripeFee($finalPrice);
            $netAmount = $finalPrice - $stripeFee;

            return response()->json([
                'success' => true,
                'message' => 'Course enrollment information retrieved successfully',
                'data' => [
                    'is_enrolled' => false,
                    'course' => [
                        'id' => $course->id,
                        'title' => $course->title,
                        'description' => $course->description,
                        'thumbnail' => $course->thumbnail_url,
                        'cover_photo' => $course->cover_photo_url,
                        'mentor' => [
                            'id' => $course->mentor->id,
                            'name' => $course->mentor->user->name,
                            'photo' => $course->mentor->photo_url,
                            'rating' => $course->mentor->average_rating,
                            'total_reviews' => $course->mentor->reviews_count,
                        ],
                        'category' => [
                            'id' => $course->category->id,
                            'name' => $course->category->name,
                        ],
                        'sub_categories' => $course->subCategories->map(function ($subCategory) {
                            return [
                                'id' => $subCategory->id,
                                'name' => $subCategory->name,
                            ];
                        }),
                        'duration_days' => $course->duration_days,
                        'created_at' => $course->created_at->toISOString(),
                        'enrollment_count' => $course->enrolledStudentsCount(),
                    ],
                    'pricing' => [
                        'original_price' => round($originalPrice, 2),
                        'discounted_price' => round($discountedPrice, 2),
                        'discount_percentage' => round($discount, 2),
                        'final_price' => round($finalPrice, 2),
                        'stripe_fee' => round($stripeFee, 2),
                        'net_amount' => round($netAmount, 2),
                        'currency' => 'USD',
                    ],
                    'eligibility' => [
                        'can_enroll' => true,
                        'reason' => null,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve enrollment information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enroll in a course with payment processing
     */
    public function enroll(Request $request, Course $course)
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

            // Check if course is available
            if ($course->status != 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Course is not available for enrollment',
                    'error' => 'Course status is not approved'
                ], 400);
            }

            // Check if user is already enrolled
            $existingEnrollment = UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', Course::class)
                ->where('enrollable_id', $course->id)
                ->first();

            if ($existingEnrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are already enrolled in this course',
                    'error' => 'Duplicate enrollment attempt'
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
                $grossAmount = $course->discounted_price;
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
                    'description' => "Course enrollment: {$course->title}",
                    'metadata' => [
                        'type' => 'course',
                        'course_id' => $course->id,
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
                        'enrollable_type' => Course::class,
                        'enrollable_id' => $course->id,
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
                        'description' => "Course enrollment: {$course->title}",
                    ]);

                    // Update enrollment with transaction ID
                    $enrollment->update([
                        'payment_transaction_id' => $transaction->id,
                    ]);

                    // Create conversation for this enrollment
                    $conversation = $enrollment->createConversation();

                    // Create notification for mentor
                    \App\Services\NotificationService::createCourseEnrollmentNotification($user, $course);

                    // Create transfer to mentor if they have a Stripe Connect account
                    $this->createMentorTransfer($course, $transaction, $mentorAmount);

                    DB::commit();

                    return response()->json([
                        'success' => true,
                        'message' => 'Successfully enrolled in course',
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
                            'course' => [
                                'id' => $course->id,
                                'title' => $course->title,
                                'thumbnail' => $course->thumbnail_url,
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
                'message' => 'Failed to enroll in course',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's course enrollments
     */
    public function getUserEnrollments(Request $request)
    {
        try {
            $user = Auth::user();
            $perPage = $request->get('per_page', 10);
            $status = $request->get('status', 'all');

            $query = UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', Course::class)
                ->with(['enrollable.mentor.user', 'enrollable.category', 'paymentTransaction']);

            if ($status != 'all') {
                $query->where('enrollment_status', $status);
            }

            $enrollments = $query->orderBy('created_at', 'desc')
                ->paginate($perPage);

            $enrollmentsData = $enrollments->map(function ($enrollment) {
                $course = $enrollment->enrollable;
                return [
                    'id' => $enrollment->id,
                    'enrollment_status' => $enrollment->enrollment_status,
                    'payment_status' => $enrollment->payment_status,
                    'enrolled_at' => $enrollment->enrolled_at ? $enrollment->enrolled_at->toISOString() : null,
                    'started_at' => $enrollment->started_at ? $enrollment->started_at->toISOString() : null,
                    'completed_at' => $enrollment->completed_at ? $enrollment->completed_at->toISOString() : null,
                    'progress_percentage' => $enrollment->progress_percentage,
                    'amount' => $enrollment->amount,
                    'currency' => $enrollment->currency,
                    'course' => [
                        'id' => $course->id,
                        'title' => $course->title,
                        'description' => $course->description,
                        'thumbnail' => $course->thumbnail_url,
                        'cover_photo' => $course->cover_photo_url,
                        'duration_days' => $course->duration_days,
                        'created_at' => $course->created_at->toISOString(),
                        'mentor' => [
                            'id' => $course->mentor->id,
                            'name' => $course->mentor->user->name,
                            'photo' => $course->mentor->photo_url,
                        ],
                        'category' => [
                            'id' => $course->category->id,
                            'name' => $course->category->name,
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
                'message' => 'User enrollments retrieved successfully',
                'data' => [
                    'enrollments' => $enrollmentsData,
                    'pagination' => [
                        'current_page' => $enrollments->currentPage(),
                        'last_page' => $enrollments->lastPage(),
                        'per_page' => $enrollments->perPage(),
                        'total' => $enrollments->total(),
                        'has_more_pages' => $enrollments->hasMorePages(),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user enrollments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific enrollment details
     */
    public function getEnrollmentDetails(UserEnrollment $enrollment)
    {
        try {
            $user = Auth::user();
            
            // Check if user owns this enrollment
            if ($enrollment->user_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to enrollment',
                    'error' => 'You can only view your own enrollments'
                ], 403);
            }

            // Load relationships
            $enrollment->load(['enrollable.mentor.user', 'enrollable.category', 'enrollable.subCategories', 'paymentTransaction', 'conversation']);

            $course = $enrollment->enrollable;

            return response()->json([
                'success' => true,
                'message' => 'Enrollment details retrieved successfully',
                'data' => [
                    'enrollment' => [
                        'id' => $enrollment->id,
                        'enrollment_status' => $enrollment->enrollment_status,
                        'payment_status' => $enrollment->payment_status,
                        'enrolled_at' => $enrollment->enrolled_at ? $enrollment->enrolled_at->toISOString() : null,
                        'started_at' => $enrollment->started_at ? $enrollment->started_at->toISOString() : null,
                        'completed_at' => $enrollment->completed_at ? $enrollment->completed_at->toISOString() : null,
                        'cancelled_at' => $enrollment->cancelled_at ? $enrollment->cancelled_at->toISOString() : null,
                        'progress_percentage' => $enrollment->progress_percentage,
                        'last_accessed_at' => $enrollment->last_accessed_at ? $enrollment->last_accessed_at->toISOString() : null,
                        'amount' => $enrollment->amount,
                        'currency' => $enrollment->currency,
                        'notes' => $enrollment->notes,
                    ],
                    'course' => [
                        'id' => $course->id,
                        'title' => $course->title,
                        'description' => $course->description,
                        'thumbnail' => $course->thumbnail_url,
                        'cover_photo' => $course->cover_photo_url,
                        'duration_days' => $course->duration_days,
                        'created_at' => $course->created_at->toISOString(),
                        'mentor' => [
                            'id' => $course->mentor->id,
                            'name' => $course->mentor->user->name,
                            'photo' => $course->mentor->photo_url,
                            'rating' => $course->mentor->average_rating,
                            'total_reviews' => $course->mentor->reviews_count,
                        ],
                        'category' => [
                            'id' => $course->category->id,
                            'name' => $course->category->name,
                        ],
                        'sub_categories' => $course->subCategories->map(function ($subCategory) {
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
                'message' => 'Failed to retrieve enrollment details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel an enrollment - DISABLED
     * Users cannot cancel course enrollments after payment
     */
    public function cancelEnrollment(UserEnrollment $enrollment)
    {
        return response()->json([
            'success' => false,
            'message' => 'Cancellation not allowed',
            'error' => 'Course enrollments cannot be cancelled after payment. No refunds are available.',
            'policy' => 'All course enrollments are final once payment is completed'
        ], 400);
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
    private function createMentorTransfer($course, $transaction, $mentorAmount)
    {
        try {
            // Check if mentor has Stripe Connect account
            $mentor = $course->mentor;
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
                    'course_id' => $course->id,
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
            // Log error but don't fail the enrollment
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
