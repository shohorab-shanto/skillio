<?php

namespace App\Http\Controllers\backend\mentor;

use App\Http\Controllers\Controller;
use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the mentor's profile.
     */
    public function show()
    {
        $user = Auth::user();
        $mentor = $user->mentor ?? new Mentor(['user_id' => $user->id]);
        
        return view('backend.mentor.profile.show', compact('user', 'mentor'));
    }

    /**
     * Update the mentor's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $mentor = $user->mentor ?? new Mentor(['user_id' => $user->id]);

        $request->validate([
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[\+]?[1-9][\d]{0,15}$/'],
            'work_experience' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ], [
            'phone.regex' => 'Please enter a valid phone number format (e.g., +1234567890).',
        ]);

        // Update user data (address and phone)
        $userData = $request->only(['address', 'phone']);
        $user->update($userData);

        // Prepare mentor data
        $mentorData = $request->only([
            'bio', 
            'work_experience'
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($mentor->photo && Storage::disk('public')->exists($mentor->photo)) {
                Storage::disk('public')->delete($mentor->photo);
            }

            // Store new photo
            $photoPath = $request->file('photo')->store('mentors/photos', 'public');
            $mentorData['photo'] = $photoPath;
        }

        // Update or create mentor profile
        if ($mentor->exists) {
            $mentor->update($mentorData);
        } else {
            $mentorData['user_id'] = $user->id;
            $mentor = Mentor::create($mentorData);
        }

        return redirect()->route('mentor.profile.show')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the mentor's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required', 
                'confirmed', 
                'min:8',
                'regex:/^(?=.*[A-Z])(?=.*\d).+$/'
            ],
        ], [
            'password.min' => 'Password must be at least 8 characters long.',
            'password.regex' => 'Password must contain at least 1 uppercase letter and 1 number.',
        ]);

        $user = Auth::user();

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('mentor.profile.show')
            ->with('success', 'Password updated successfully!');
    }

    /**
     * Update the mentor's online status.
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'online' => ['required', 'boolean'],
        ]);

        $user = Auth::user();
        $mentor = $user->mentor ?? Mentor::create(['user_id' => $user->id]);

        $mentor->update([
            'availability' => $request->online ? 'available' : 'unavailable'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully!',
            'status' => $request->online ? 'Online' : 'Offline'
        ]);
    }
}
