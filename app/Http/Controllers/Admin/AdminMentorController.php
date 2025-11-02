<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
            if ($request->verification_status == 'verified') {
                $query->whereHas('mentor', function($q) {
                    $q->where('verified', true);
                });
            } elseif ($request->verification_status == 'unverified') {
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

    public function create()
    {
        return view('admin.mentors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20|regex:/^[\+]?[1-9][\d]{0,15}$/',
            'bio' => 'nullable|string|max:1000',
            'work_experience' => 'nullable|string|max:255',
            'availability' => 'required|in:available,unavailable',
            'type' => 'required|in:online,in-person',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'verified' => 'boolean'
        ], [
            'phone.regex' => 'Please enter a valid phone number format (e.g., +1234567890).',
        ]);

        // Additional validation: address is required for in-person mentors
        if ($request->type == 'in-person' && empty($request->address)) {
            return back()->withErrors(['address' => 'Location is required for in-person mentors.'])->withInput();
        }

        // Create user account
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'mentor',
            'email_verified_at' => now(),
            'address' => $request->address,
            'phone' => $request->phone,
        ]);

        // Prepare mentor data
        $mentorData = [
            'user_id' => $user->id,
            'bio' => $request->bio,
            'work_experience' => $request->work_experience,
            'availability' => $request->availability,
            'type' => $request->type,
            'verified' => $request->has('verified') ? true : false,
        ];

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('mentors', 'public');
            $mentorData['photo'] = $photoPath;
        }

        // Create mentor profile
        Mentor::create($mentorData);

        return redirect()->route('admin.mentors.index')
            ->with('success', 'Mentor created successfully!');
    }

    public function show(User $user)
    {
        if ($user->role != 'mentor') {
            return redirect()->back()->with('error', 'Invalid user type');
        }

        $mentor = $user->mentor;
        if (!$mentor) {
            return redirect()->back()->with('error', 'Mentor profile not found');
        }

        return view('admin.mentors.show', compact('user', 'mentor'));
    }

    public function edit(User $user)
    {
        if ($user->role != 'mentor') {
            return redirect()->back()->with('error', 'Invalid user type');
        }

        $mentor = $user->mentor;
        if (!$mentor) {
            return redirect()->back()->with('error', 'Mentor profile not found');
        }

        return view('admin.mentors.edit', compact('user', 'mentor'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->role != 'mentor') {
            return redirect()->back()->with('error', 'Invalid user type');
        }

        $mentor = $user->mentor;
        if (!$mentor) {
            return redirect()->back()->with('error', 'Mentor profile not found');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20|regex:/^[\+]?[1-9][\d]{0,15}$/',
            'bio' => 'nullable|string|max:1000',
            'work_experience' => 'nullable|string|max:255',
            'availability' => 'required|in:available,unavailable',
            'type' => 'required|in:online,in-person',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'verified' => 'boolean'
        ], [
            'phone.regex' => 'Please enter a valid phone number format (e.g., +1234567890).',
        ]);

        // Additional validation: address is required for in-person mentors
        if ($request->type == 'in-person' && empty($request->address)) {
            return back()->withErrors(['address' => 'Location is required for in-person mentors.'])->withInput();
        }

        // Update user account
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        // Prepare mentor data
        $mentorData = [
            'bio' => $request->bio,
            'work_experience' => $request->work_experience,
            'availability' => $request->availability,
            'type' => $request->type,
            'verified' => $request->has('verified') ? true : false,
        ];

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($mentor->photo) {
                Storage::disk('public')->delete($mentor->photo);
            }
            $photoPath = $request->file('photo')->store('mentors', 'public');
            $mentorData['photo'] = $photoPath;
        }

        // Update mentor profile
        $mentor->update($mentorData);

        return redirect()->route('admin.mentors.index')
            ->with('success', 'Mentor updated successfully!');
    }

    public function destroy(User $user)
    {
        if ($user->role != 'mentor') {
            return redirect()->back()->with('error', 'Invalid user type');
        }

        $mentor = $user->mentor;
        if (!$mentor) {
            return redirect()->back()->with('error', 'Mentor profile not found');
        }

        // Check if mentor has courses or session bookings
        $coursesCount = $mentor->courses()->count();
        $sessionsCount = $mentor->sessionBookings()->count();

        if ($coursesCount > 0 || $sessionsCount > 0) {
            return redirect()->back()->with('error', "Cannot delete mentor. They have {$coursesCount} courses and {$sessionsCount} session bookings.");
        }

        // Delete mentor photo if exists
        if ($mentor->photo) {
            Storage::disk('public')->delete($mentor->photo);
        }

        // Delete mentor profile and user account
        $mentor->delete();
        $user->delete();

        return redirect()->back()->with('success', 'Mentor deleted successfully');
    }

    public function courses(User $user)
    {
        if ($user->role != 'mentor') {
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
        if ($user->role != 'mentor') {
            return redirect()->back()->with('error', 'Invalid user type');
        }

        $sessions = $user->mentor->sessionBookings()
            ->with(['category', 'subCategories'])
            ->latest()
            ->paginate(15);

        return view('admin.mentors.sessions', compact('user', 'sessions'));
    }

    public function toggleVerification(Request $request, User $user)
    {
        if ($user->role != 'mentor') {
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
        if ($user->role != 'mentor') {
            return response()->json(['success' => false, 'message' => 'Invalid user type']);
        }

        $mentor = $user->mentor;
        if (!$mentor) {
            return response()->json(['success' => false, 'message' => 'Mentor profile not found']);
        }

        $request->validate([
            'availability' => 'required|in:available,unavailable'
        ]);

        $mentor->update(['availability' => $request->availability]);
        
        return response()->json([
            'success' => true, 
            'message' => "Mentor availability updated to {$request->availability}",
            'availability' => $request->availability
        ]);
    }

    public function updateAccountDetails(Request $request, User $user)
    {
        if ($user->role != 'mentor') {
            return response()->json(['success' => false, 'message' => 'Invalid user type']);
        }

        $mentor = $user->mentor;
        if (!$mentor) {
            return response()->json(['success' => false, 'message' => 'Mentor profile not found']);
        }

        $request->validate([
            'stripe_connect_account_id' => 'nullable|string',
            'connect_account_status' => 'required|in:pending,active,rejected,restricted',
        ]);

        // Prepare data for update - only Stripe Connect fields
        $updateData = [
            'stripe_connect_account_id' => $request->stripe_connect_account_id,
            'connect_account_status' => $request->connect_account_status,
        ];

        // Update mentor
        $mentor->update($updateData);
        
        return response()->json([
            'success' => true, 
            'message' => 'Stripe Connect account updated successfully'
        ]);
    }
}
