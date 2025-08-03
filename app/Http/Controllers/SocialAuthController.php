<?php

namespace App\Http\Controllers;

use Socialite;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'password' => bcrypt(Str::random(16)),
                'status' => 'active',
                'role' => 'user', // Default role, can be adjusted based on your logic
                'gdpr_consent' => true, // or handle this via a consent screen
                'email_verified_at' => now(),
            ]
        );

        Auth::login($user);

        return redirect('/dashboard');
    }

    public function redirectToApple()
    {
        return Socialite::driver('apple')->redirect();
    }

    public function handleAppleCallback()
    {
        $appleUser = Socialite::driver('apple')->stateless()->user();

        $user = User::updateOrCreate(
            ['email' => $appleUser->getEmail()],
            [
                'name' => $appleUser->getName() ?? 'Apple User',
                'apple_id' => $appleUser->getId(),
                'password' => bcrypt(Str::random(16)),
                'status' => 'active',
                'role' => 'user', // Default role, can be adjusted based on your logic
                'email_verified_at' => now(),
                'apple_user_id' => $appleUser->getId(),
                'gdpr_consent' => true, // or handle this via a consent screen
            ]
        );

        Auth::login($user);

        return redirect('/dashboard');
    }
}
