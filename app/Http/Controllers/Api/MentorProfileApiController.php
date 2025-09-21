<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MentorProfileApiController extends Controller
{
    /**
     * Display the mentor's profile
     */
    public function show()
    {
        $user = Auth::user();
        $mentor = $user->mentor;
        
        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'role' => $user->role,
                    'status' => $user->status,
                    'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $user->updated_at->format('Y-m-d H:i:s'),
                ],
                'mentor' => $mentor ? [
                    'id' => $mentor->id,
                    'bio' => $mentor->bio,
                    'work_experience' => $mentor->work_experience,
                    'photo' => $mentor->photo ? asset('storage/' . $mentor->photo) : null,
                    'availability' => $mentor->availability,
                    'total_reviews' => $mentor->totalReviews(),
                    'average_rating' => $mentor->averageRating(),
                    'formatted_average_rating' => $mentor->formatted_average_rating,
                    'star_rating' => $mentor->star_rating,
                    'rating_distribution' => $mentor->ratingDistribution(),
                    'five_star_percentage' => $mentor->five_star_percentage,
                    'has_excellent_reviews' => $mentor->hasExcellentReviews(),
                    'created_at' => $mentor->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $mentor->updated_at->format('Y-m-d H:i:s'),
                ] : null,
            ]
        ]);
    }

    /**
     * Update the mentor's profile information
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

        // Reload relationships
        $user->refresh();
        $mentor->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully!',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'updated_at' => $user->updated_at->format('Y-m-d H:i:s'),
                ],
                'mentor' => [
                    'id' => $mentor->id,
                    'bio' => $mentor->bio,
                    'work_experience' => $mentor->work_experience,
                    'photo' => $mentor->photo ? asset('storage/' . $mentor->photo) : null,
                    'availability' => $mentor->availability,
                    'updated_at' => $mentor->updated_at->format('Y-m-d H:i:s'),
                ],
            ]
        ]);
    }

    /**
     * Update the mentor's password
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

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully!',
            'data' => [
                'id' => $user->id,
                'updated_at' => $user->updated_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    /**
     * Update the mentor's online status
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
            'data' => [
                'status' => $request->online ? 'available' : 'unavailable',
                'status_text' => $request->online ? 'Online' : 'Offline',
                'updated_at' => $mentor->updated_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    /**
     * Get mentor statistics for profile
     */
    public function getStatistics()
    {
        $user = Auth::user();
        $mentor = $user->mentor;

        if (!$mentor) {
            return response()->json([
                'success' => false,
                'message' => 'Mentor profile not found.'
            ], 404);
        }

        // Get mentor statistics
        $totalCourses = \App\Models\Course::where('mentor_id', $mentor->id)->count();
        $activeCourses = \App\Models\Course::where('mentor_id', $mentor->id)->where('status', 'approved')->count();
        $totalSessions = \App\Models\SessionBooking::where('mentor_id', $mentor->id)->count();
        $bookedSessions = \App\Models\SessionBooking::where('mentor_id', $mentor->id)->whereNotNull('user_id')->count();
        
        $totalStudents = \App\Models\UserEnrollment::whereHas('enrollable', function($query) use ($mentor) {
            $query->where('mentor_id', $mentor->id);
        })->distinct('user_id')->count();

        $totalEarnings = \App\Models\PaymentTransaction::whereHas('enrollments.enrollable', function($query) use ($mentor) {
            $query->where('mentor_id', $mentor->id);
        })->where('transaction_status', 'completed')->sum('mentor_amount');

        return response()->json([
            'success' => true,
            'data' => [
                'courses' => [
                    'total' => $totalCourses,
                    'active' => $activeCourses,
                ],
                'sessions' => [
                    'total' => $totalSessions,
                    'booked' => $bookedSessions,
                    'available' => $totalSessions - $bookedSessions,
                ],
                'students' => [
                    'total' => $totalStudents,
                ],
                'earnings' => [
                    'total' => round($totalEarnings, 2),
                ],
                'reviews' => [
                    'total' => $mentor->totalReviews(),
                    'average_rating' => $mentor->averageRating(),
                    'formatted_average_rating' => $mentor->formatted_average_rating,
                    'star_rating' => $mentor->star_rating,
                    'rating_distribution' => $mentor->ratingDistribution(),
                    'five_star_percentage' => $mentor->five_star_percentage,
                    'has_excellent_reviews' => $mentor->hasExcellentReviews(),
                ],
            ]
        ]);
    }
}
