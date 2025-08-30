<?php

namespace App\Http\Controllers\backend\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserEnrollment;
use App\Models\SessionBooking;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class UserSessionsController extends Controller
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

        $enrollments = $query->orderBy('created_at', 'desc')->paginate(9);
        
        // Add conversation data to each enrollment
        $enrollments->getCollection()->transform(function ($enrollment) {
            $conversation = Conversation::where('mentor_id', $enrollment->enrollable->mentor->id)
                                      ->where('user_id', Auth::id())
                                      ->first();
            $enrollment->conversation = $conversation;
            return $enrollment;
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
        
        return view('backend.user.sessions.index', compact('enrollments', 'stats', 'request'));
    }
    
    /**
     * Show specific session details
     */
    public function show($enrollmentId)
    {
        $user = Auth::user();
        
        $enrollment = UserEnrollment::with([
            'enrollable.mentor.user',
            'enrollable.subCategories',
            'paymentTransaction'
        ])
        ->where('id', $enrollmentId)
        ->where('user_id', $user->id)
        ->where('enrollable_type', SessionBooking::class)
        ->firstOrFail();
        
        // Get conversation between user and mentor
        $conversation = Conversation::where('mentor_id', $enrollment->enrollable->mentor->id)
                                  ->where('user_id', $user->id)
                                  ->first();
        
        return view('backend.user.sessions.show', compact('enrollment', 'conversation'));
    }

    /**
     * Get available session slots for switching
     */
    public function getAvailableSlots($enrollmentId)
    {
        $user = Auth::user();
        
        $enrollment = UserEnrollment::with(['enrollable.mentor'])
            ->where('id', $enrollmentId)
            ->where('user_id', $user->id)
            ->where('enrollable_type', SessionBooking::class)
            ->where('enrollment_status', 'active')
            ->firstOrFail();

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
                    'fee' => $session->fee
                ];
            });

        return response()->json([
            'success' => true,
            'sessions' => $availableSessions
        ]);
    }

    /**
     * Switch session time
     */
    public function switchSession(Request $request, $enrollmentId)
    {
        $user = Auth::user();
        
        $enrollment = UserEnrollment::with(['enrollable.mentor'])
            ->where('id', $enrollmentId)
            ->where('user_id', $user->id)
            ->where('enrollable_type', SessionBooking::class)
            ->where('enrollment_status', 'active')
            ->firstOrFail();

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
                'enrollable_id' => $newSession->id
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
                'new_session' => [
                    'id' => $newSession->id,
                    'date' => $newSession->date->format('M d, Y'),
                    'time_slot' => $newSession->formatted_time_slot
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
}