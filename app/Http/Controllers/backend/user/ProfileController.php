<?php

namespace App\Http\Controllers\backend\user;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show()
    {
        $user = Auth::user();
        return view('backend.user.profile.show', compact('user'));
    }

    /**
     * Update the user's profile information.
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

        return redirect()->route('user.profile.show')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the user's password.
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

        return redirect()->route('user.profile.show')
            ->with('success', 'Password updated successfully!');
    }
}
