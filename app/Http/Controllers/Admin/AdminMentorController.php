<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Mentor;
use Illuminate\Http\Request;

class AdminMentorController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'mentor')
            ->with('mentor');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Verification filter
        if ($request->filled('verification_status')) {
            if ($request->verification_status === 'verified') {
                $query->whereHas('mentor', function($q) {
                    $q->where('verified', true);
                });
            } elseif ($request->verification_status === 'unverified') {
                $query->whereHas('mentor', function($q) {
                    $q->where('verified', false);
                });
            }
        }

        // Availability filter
        if ($request->filled('availability')) {
            $query->whereHas('mentor', function($q) use ($request) {
                $q->where('availability', $request->availability);
            });
        }

        // Date filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $mentors = $query->latest()->paginate(15);

        return view('admin.mentors.index', compact('mentors'));
    }

    public function show(User $user)
    {
        if ($user->role !== 'mentor') {
            return redirect()->back()->with('error', 'Invalid user type');
        }

        $mentor = $user->mentor;
        if (!$mentor) {
            return redirect()->back()->with('error', 'Mentor profile not found');
        }

        return view('admin.mentors.show', compact('user', 'mentor'));
    }

    public function courses(User $user)
    {
        if ($user->role !== 'mentor') {
            return redirect()->back()->with('error', 'Invalid user type');
        }

        $courses = $user->courses()
            ->with(['category', 'subCategories'])
            ->latest()
            ->paginate(15);

        return view('admin.mentors.courses', compact('user', 'courses'));
    }

    public function sessions(User $user)
    {
        if ($user->role !== 'mentor') {
            return redirect()->back()->with('error', 'Invalid user type');
        }

        $sessions = $user->sessionBookings()
            ->with(['category', 'subCategories'])
            ->latest()
            ->paginate(15);

        return view('admin.mentors.sessions', compact('user', 'sessions'));
    }

    public function toggleVerification(Request $request, User $user)
    {
        if ($user->role !== 'mentor') {
            return response()->json(['success' => false, 'message' => 'Invalid user type']);
        }

        $mentor = $user->mentor;
        if (!$mentor) {
            return response()->json(['success' => false, 'message' => 'Mentor profile not found']);
        }

        $mentor->update(['verified' => !$mentor->verified]);
        
        $status = $mentor->verified ? 'verified' : 'unverified';
        
        return response()->json([
            'success' => true, 
            'message' => "Mentor {$status} successfully",
            'verified' => $mentor->verified
        ]);
    }

    public function updateAvailability(Request $request, User $user)
    {
        if ($user->role !== 'mentor') {
            return response()->json(['success' => false, 'message' => 'Invalid user type']);
        }

        $mentor = $user->mentor;
        if (!$mentor) {
            return response()->json(['success' => false, 'message' => 'Mentor profile not found']);
        }

        $request->validate([
            'availability' => 'required|in:available,unavailable,busy'
        ]);

        $mentor->update(['availability' => $request->availability]);
        
        return response()->json([
            'success' => true, 
            'message' => "Mentor availability updated to {$request->availability}",
            'availability' => $request->availability
        ]);
    }
}
