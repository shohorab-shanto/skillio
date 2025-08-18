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
            if ($request->status === 'upcoming') {
                $query->where('enrollment_status', 'active')
                      ->whereExists(function($q) {
                          $q->select(\DB::raw(1))
                            ->from('session_bookings')
                            ->whereColumn('session_bookings.id', 'user_enrollments.enrollable_id')
                            ->where('session_bookings.date', '>', now());
                      });
            } elseif ($request->status === 'completed') {
                $query->where('enrollment_status', 'completed');
            } elseif ($request->status === 'past') {
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
}