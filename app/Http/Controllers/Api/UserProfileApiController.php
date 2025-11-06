<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserProfileApiController extends Controller
{
    /**
     * Display the user's profile
     */
    public function show()
    {
        $user = Auth::user();
        $user->load('userPreferences');
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'role' => $user->role,
                'status' => $user->status,
                'photo' => $user->photo ? asset('storage/' . $user->photo) : null,
                'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->format('Y-m-d H:i:s') : null,
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $user->updated_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    /**
     * Update the user's profile information
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[\+]?[1-9][\d]{0,15}$/'],
        ], [
            'phone.regex' => 'Please enter a valid phone number format (e.g., +1234567890).',
        ]);

        $user->update($request->only(['address', 'phone']));

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully!',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'updated_at' => $user->updated_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    /**
     * Update the user's password
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
     * Get user preferences
     */
    public function getPreferences()
    {
        $user = Auth::user();
        $preferences = UserPreference::where('user_id', $user->id)->first();
        
        if (!$preferences) {
            return response()->json([
                'success' => true,
                'data' => [
                    'education_type' => null,
                    'category_id' => null,
                    'sub_category_id' => null,
                    'country' => null,
                    'city' => null,
                    'wants_courses' => false,
                    'wants_mentoring' => false,
                ]
            ]);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'education_type' => $preferences->education_type,
                'category_id' => $preferences->category_id,
                'sub_category_id' => $preferences->sub_category_id,
                'country' => $preferences->country,
                'city' => $preferences->city,
                'wants_courses' => $preferences->wants_courses,
                'wants_mentoring' => $preferences->wants_mentoring,
                'created_at' => $preferences->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $preferences->updated_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    /**
     * Update user preferences
     */
    public function updatePreferences(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'education_type' => ['nullable', 'in:in-person,online,both'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'sub_category_id' => ['nullable', 'exists:sub_categories,id'],
            'country' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'wants_courses' => ['nullable', 'boolean'],
            'wants_mentoring' => ['nullable', 'boolean'],
        ]);

        $preferences = UserPreference::firstOrNew(['user_id' => $user->id]);
        $preferences->fill($request->only([
            'education_type',
            'category_id',
            'sub_category_id',
            'country',
            'city',
            'wants_courses',
            'wants_mentoring',
        ]));
        $preferences->save();

        return response()->json([
            'success' => true,
            'message' => 'Preferences updated successfully!',
            'data' => [
                'education_type' => $preferences->education_type,
                'category_id' => $preferences->category_id,
                'sub_category_id' => $preferences->sub_category_id,
                'country' => $preferences->country,
                'city' => $preferences->city,
                'wants_courses' => $preferences->wants_courses,
                'wants_mentoring' => $preferences->wants_mentoring,
                'updated_at' => $preferences->updated_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    /**
     * Check onboarding completion status
     */
    public function getOnboardingStatus()
    {
        $user = Auth::user();
        $preferences = UserPreference::where('user_id', $user->id)->first();
        
        $isComplete = true;
        $missingSteps = [];
        
        if (!$preferences) {
            $isComplete = false;
            $missingSteps[] = 'category_service';
        } else {
            if (!$preferences->category_id) {
                $isComplete = false;
                $missingSteps[] = 'category_service';
            }
            
            if (!$preferences->education_type) {
                $isComplete = false;
                $missingSteps[] = 'in_person_or_online';
            }
            
            if ($preferences->education_type == 'online') {
                if (!$preferences->wants_courses && !$preferences->wants_mentoring) {
                    $isComplete = false;
                    $missingSteps[] = 'online_education';
                }
            } elseif ($preferences->education_type == 'in-person') {
                if (!$preferences->country || !$preferences->city) {
                    $isComplete = false;
                    $missingSteps[] = 'in_person_education_location';
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'is_complete' => $isComplete,
                'missing_steps' => $missingSteps,
                'next_step' => $isComplete ? null : $missingSteps[0] ?? 'category_service',
            ]
        ]);
    }

    /**
     * Delete user account (soft delete with anonymization)
     */
    public function deleteAccount(Request $request)
    {
        $user = Auth::user();
        
        // Optional: Require password confirmation for extra security
        if ($request->has('password')) {
            $request->validate([
                'password' => ['required', 'current_password'],
            ]);
        }

        // Generate anonymized email to maintain uniqueness if needed
        $anonymizedEmail = 'deleted_user_' . $user->id . '_' . time() . '@deleted.local';

        // Anonymize user data
        $user->update([
            'name' => 'Deleted User',
            'email' => $anonymizedEmail,
            'phone' => null,
            'address' => null,
            'google_id' => null,
            'apple_id' => null,
            'firebase_uid' => null,
            'password' => Hash::make(bin2hex(random_bytes(32))), // Random password
            'status' => 'inactive',
            'is_anonymized' => true,
        ]);

        // Revoke all authentication tokens
        $user->tokens()->delete();

        // Soft delete the user
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Your account has been successfully deleted. All personal information has been removed from our system.',
        ], 200);
    }
}
