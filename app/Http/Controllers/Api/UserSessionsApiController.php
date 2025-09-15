<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserEnrollment;
use App\Models\SessionBooking;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class UserSessionsApiController extends Controller
{
    /**
     * Display user's booked sessions
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Build query for user's session enrollments
        $query = UserEnrollment::with([
            'enrollable.mentor.user',
            'enrollable.subCategories',
            'enrollable.category',
            'paymentTransaction'
        ])
        ->where('user_id', $user->id)
        ->where('enrollable_type', SessionBooking::class)
        ->whereIn('enrollment_status', ['active', 'completed']);

        // Apply filters - use join approach to avoid polymorphic issues
        if ($request->filled('date_from')) {
            $query->whereExists(function($q) use ($request) {
                $q->select(\DB::raw(1))
                  ->from('session_bookings')
                  ->whereColumn('session_bookings.id', 'user_enrollments.enrollable_id')
                  ->where('session_bookings.date', '>=', $request->date_from);
            });
        }

        if ($request->filled('date_to')) {
            $query->whereExists(function($q) use ($request) {
                $q->select(\DB::raw(1))
                  ->from('session_bookings')
                  ->whereColumn('session_bookings.id', 'user_enrollments.enrollable_id')
                  ->where('session_bookings.date', '<=', $request->date_to);
            });
        }

        if ($request->filled('status')) {
            if ($request->status == 'upcoming') {
                $query->where('enrollment_status', 'active')
                      ->whereExists(function($q) {
                          $q->select(\DB::raw(1))
                            ->from('session_bookings')
                            ->whereColumn('session_bookings.id', 'user_enrollments.enrollable_id')
                            ->where('session_bookings.date', '>', now());
                      });
            } elseif ($request->status == 'completed') {
                $query->where('enrollment_status', 'completed');
            } elseif ($request->status == 'past') {
                $query->whereExists(function($q) {
                    $q->select(\DB::raw(1))
                      ->from('session_bookings')
                      ->whereColumn('session_bookings.id', 'user_enrollments.enrollable_id')
                      ->where('session_bookings.date', '<', now());
                });
            } else {
                $query->where('enrollment_status', $request->status);
            }
        }

        if ($request->filled('mentor')) {
            $query->whereHas('enrollable.mentor.user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->mentor . '%');
            });
        }

        $perPage = $request->get('per_page', 9);
        $enrollments = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        // Add conversation data to each enrollment
        $enrollments->getCollection()->transform(function ($enrollment) use ($user) {
            $conversation = Conversation::where('mentor_id', $enrollment->enrollable->mentor->id)
                                      ->where('user_id', $user->id)
                                      ->first();
            
            $session = $enrollment->enrollable;
            $mentor = $session->mentor->user;
            
            return [
                'id' => $enrollment->id,
                'session' => [
                    'id' => $session->id,
                    'date' => $session->date ? $session->date->format('Y-m-d') : null,
                    'start_time' => $session->start_time,
                    'end_time' => $session->end_time,
                    'formatted_time_slot' => $session->formatted_time_slot,
                    'fee' => round($session->fee, 2),
                    'type' => $session->type,
                    'status' => $session->status,
                    'category' => [
                        'id' => $session->category->id,
                        'name' => $session->category->name,
                    ],
                    'sub_categories' => $session->subCategories->map(function($subCategory) {
                        return [
                            'id' => $subCategory->id,
                            'name' => $subCategory->name,
                        ];
                    }),
                ],
                'mentor' => [
                    'id' => $mentor->id,
                    'name' => $mentor->name,
                    'photo' => $mentor->photo ? asset('storage/' . $mentor->photo) : null,
                ],
                'enrollment' => [
                    'status' => $enrollment->enrollment_status,
                    'enrolled_at' => $enrollment->enrolled_at ? $enrollment->enrolled_at->format('Y-m-d H:i:s') : null,
                    'amount' => round($enrollment->amount, 2),
                    'currency' => $enrollment->currency,
                    'payment_status' => $enrollment->payment_status,
                    'switched_at' => $enrollment->switched_at ? $enrollment->switched_at->format('Y-m-d H:i:s') : null,
                ],
                'conversation' => $conversation ? [
                    'id' => $conversation->id,
                    'unique_code' => $conversation->unique_code,
                ] : null,
            ];
        });
        
        // Get statistics
        $stats = [
            'total_sessions' => UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', SessionBooking::class)
                ->whereIn('enrollment_status', ['active', 'completed'])
                ->count(),
            'upcoming_sessions' => UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', SessionBooking::class)
                ->where('enrollment_status', 'active')
                ->whereExists(function($query) {
                    $query->select(DB::raw(1))
                          ->from('session_bookings')
                          ->whereColumn('session_bookings.id', 'user_enrollments.enrollable_id')
                          ->where('session_bookings.date', '>', now());
                })
                ->count(),
            'completed_sessions' => UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', SessionBooking::class)
                ->where('enrollment_status', 'completed')
                ->count(),
            'total_spent' => UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', SessionBooking::class)
                ->whereIn('enrollment_status', ['active', 'completed'])
                ->sum('amount')
        ];
        
        return response()->json([
            'success' => true,
            'data' => [
                'enrollments' => $enrollments->items(),
                'pagination' => [
                    'current_page' => $enrollments->currentPage(),
                    'last_page' => $enrollments->lastPage(),
                    'per_page' => $enrollments->perPage(),
                    'total' => $enrollments->total(),
                ],
                'statistics' => $stats,
            ]
        ]);
    }
    
    /**
     * Show specific session details
     */
    public function show(UserEnrollment $enrollment)
    {
        $user = Auth::user();
        
        // Ensure this enrollment belongs to the current user and is a session
        if ($enrollment->user_id != $user->id || $enrollment->enrollable_type != SessionBooking::class) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to session.'
            ], 403);
        }
        
        $enrollment->load([
            'enrollable.mentor.user',
            'enrollable.subCategories',
            'enrollable.category',
            'paymentTransaction'
        ]);
        
        // Get conversation between user and mentor
        $conversation = Conversation::where('mentor_id', $enrollment->enrollable->mentor->id)
                                  ->where('user_id', $user->id)
                                  ->first();
        
        $session = $enrollment->enrollable;
        $mentor = $session->mentor->user;
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $enrollment->id,
                'session' => [
                    'id' => $session->id,
                    'date' => $session->date ? $session->date->format('Y-m-d') : null,
                    'start_time' => $session->start_time,
                    'end_time' => $session->end_time,
                    'formatted_time_slot' => $session->formatted_time_slot,
                    'fee' => round($session->fee, 2),
                    'type' => $session->type,
                    'status' => $session->status,
                    'category' => [
                        'id' => $session->category->id,
                        'name' => $session->category->name,
                    ],
                    'sub_categories' => $session->subCategories->map(function($subCategory) {
                        return [
                            'id' => $subCategory->id,
                            'name' => $subCategory->name,
                        ];
                    }),
                ],
                'mentor' => [
                    'id' => $mentor->id,
                    'name' => $mentor->name,
                    'photo' => $mentor->photo ? asset('storage/' . $mentor->photo) : null,
                ],
                'enrollment' => [
                    'status' => $enrollment->enrollment_status,
                    'enrolled_at' => $enrollment->enrolled_at ? $enrollment->enrolled_at->format('Y-m-d H:i:s') : null,
                    'amount' => round($enrollment->amount, 2),
                    'currency' => $enrollment->currency,
                    'payment_status' => $enrollment->payment_status,
                    'switched_at' => $enrollment->switched_at ? $enrollment->switched_at->format('Y-m-d H:i:s') : null,
                ],
                'conversation' => $conversation ? [
                    'id' => $conversation->id,
                    'unique_code' => $conversation->unique_code,
                ] : null,
            ]
        ]);
    }

    /**
     * Get available session slots for switching
     */
    public function getAvailableSlots(UserEnrollment $enrollment)
    {
        $user = Auth::user();
        
        // Ensure this enrollment belongs to the current user and is a session
        if ($enrollment->user_id != $user->id || $enrollment->enrollable_type != SessionBooking::class) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to session.'
            ], 403);
        }

        $enrollment->load(['enrollable.mentor']);
        $currentSession = $enrollment->enrollable;
        
        // Check if current session hasn't started yet
        if (!$currentSession->has_not_started) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot switch session that has already started'
            ], 400);
        }

        // Get available sessions from the same mentor with same price
        $availableSessions = SessionBooking::where('mentor_id', $currentSession->mentor_id)
            ->where('fee', $currentSession->fee)
            ->where('status', 'active')
            ->whereNull('user_id')
            ->where(function($query) {
                $query->where('date', '>', now()->toDateString())
                      ->orWhere(function($q) {
                          $q->where('date', now()->toDateString())
                            ->where('start_time', '>', now()->format('H:i:s'));
                      });
            })
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->map(function($session) {
                return [
                    'id' => $session->id,
                    'date' => $session->date->format('M d, Y'),
                    'time_slot' => $session->formatted_time_slot,
                    'fee' => round($session->fee, 2)
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'current_session' => [
                    'id' => $currentSession->id,
                    'date' => $currentSession->date->format('M d, Y'),
                    'time_slot' => $currentSession->formatted_time_slot,
                ],
                'available_sessions' => $availableSessions
            ]
        ]);
    }

    /**
     * Switch session time
     */
    public function switchSession(Request $request, UserEnrollment $enrollment)
    {
        $user = Auth::user();
        
        // Ensure this enrollment belongs to the current user and is a session
        if ($enrollment->user_id != $user->id || $enrollment->enrollable_type != SessionBooking::class) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to session.'
            ], 403);
        }

        $enrollment->load(['enrollable.mentor']);
        $currentSession = $enrollment->enrollable;
        $newSessionId = $request->input('new_session_id');
        
        // Validate request
        $request->validate([
            'new_session_id' => 'required|exists:session_bookings,id'
        ]);

        // Check if current session hasn't started yet
        if (!$currentSession->has_not_started) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot switch session that has already started'
            ], 400);
        }

        // Get the new session
        $newSession = SessionBooking::where('id', $newSessionId)
            ->where('mentor_id', $currentSession->mentor_id)
            ->where('fee', $currentSession->fee)
            ->where('status', 'active')
            ->whereNull('user_id')
            ->first();

        if (!$newSession) {
            return response()->json([
                'success' => false,
                'message' => 'Selected session is not available'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Make current session available again
            $currentSession->update([
                'user_id' => null,
                'status' => 'active'
            ]);

            // Book the new session
            $newSession->update([
                'user_id' => $user->id,
                'status' => 'booked'
            ]);

            // Update the enrollment to point to the new session
            $enrollment->update([
                'enrollable_id' => $newSession->id,
                'switched_at' => now()
            ]);

            // Update payment transaction if exists
            if ($enrollment->paymentTransaction) {
                $enrollment->paymentTransaction->update([
                    'description' => "Session: {$newSession->mentor->user->name} - {$newSession->date->format('M d, Y')} {$newSession->formatted_time_slot}",
                    'metadata' => array_merge($enrollment->paymentTransaction->metadata ?? [], [
                        'session_id' => $newSession->id,
                        'session_date' => $newSession->date->format('Y-m-d'),
                        'session_time' => $newSession->formatted_time_slot,
                        'switched_from_session_id' => $currentSession->id,
                        'switched_at' => now()->toISOString()
                    ])
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Session time switched successfully',
                'data' => [
                    'new_session' => [
                        'id' => $newSession->id,
                        'date' => $newSession->date->format('M d, Y'),
                        'time_slot' => $newSession->formatted_time_slot
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to switch session. Please try again.'
            ], 500);
        }
    }

    /**
     * Get session statistics
     */
    public function statistics(Request $request)
    {
        $user = Auth::user();
        
        $stats = [
            'total_sessions' => UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', SessionBooking::class)
                ->whereIn('enrollment_status', ['active', 'completed'])
                ->count(),
            'upcoming_sessions' => UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', SessionBooking::class)
                ->where('enrollment_status', 'active')
                ->whereExists(function($query) {
                    $query->select(DB::raw(1))
                          ->from('session_bookings')
                          ->whereColumn('session_bookings.id', 'user_enrollments.enrollable_id')
                          ->where('session_bookings.date', '>', now());
                })
                ->count(),
            'completed_sessions' => UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', SessionBooking::class)
                ->where('enrollment_status', 'completed')
                ->count(),
            'total_spent' => UserEnrollment::where('user_id', $user->id)
                ->where('enrollable_type', SessionBooking::class)
                ->whereIn('enrollment_status', ['active', 'completed'])
                ->sum('amount')
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
