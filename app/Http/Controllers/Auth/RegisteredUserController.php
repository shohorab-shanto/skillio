<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // dd($request->all()); // Debugging line to check the request data
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:user,mentor'],
            'gdpr_consent' => ['required', 'accepted'],
        ]);

        


        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'gdpr_consent' => $request->has('gdpr_consent'),
        ];

        if ($request->role == 'user') {
            $userData['status'] = 'active';
        } elseif ($request->role == 'mentor') {
            $userData['status'] = 'active';
        }

        $user = User::create($userData);

        event(new Registered($user));

        Auth::login($user);

        // Redirect based on user role
        if ($user->role == 'admin') {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        } elseif ($user->role == 'mentor') {
            // Create a mentor profile associated with the newly registered user
            \App\Models\Mentor::create([
                'user_id' => $user->id,
            ]);
            return redirect()->intended(route('mentor.dashboard', absolute: false));
        }else{
            return redirect()->intended(route('user.dashboard', absolute: false));
        }

        
    }
}
