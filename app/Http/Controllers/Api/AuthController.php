<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required', 
                'confirmed', 
                'min:8',
                'regex:/^(?=.*[A-Z])(?=.*\d).+$/'
            ],
            'role' => ['required', 'in:user'],
            'gdpr_consent' => ['required', 'accepted'],
        ], [
            'password.min' => 'Password must be at least 8 characters long.',
            'password.regex' => 'Password must contain at least 1 uppercase letter and 1 number.',
            'gdpr_consent.accepted' => 'You must accept the terms and conditions.',
            'role.in' => 'Mentor registration is not allowed through this endpoint. Please contact support.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'user', // Only users can register through API
                'gdpr_consent' => $request->has('gdpr_consent'),
                'status' => 'active',
            ];

            $user = User::create($userData);

            event(new Registered($user));

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'status' => $user->status,
                        'email_verified_at' => $user->email_verified_at,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'errors' => ['server' => 'An error occurred during registration'],
            ], 500);
        }
    }

    /**
     * Login user
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required'],
            'remember' => ['boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
                'errors' => ['email' => 'The provided credentials are incorrect'],
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'status' => $user->status,
                    'email_verified_at' => $user->email_verified_at,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
        ]);
    }

    /**
     * Get current user
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'User data retrieved successfully',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'status' => $user->status,
                    'email_verified_at' => $user->email_verified_at,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ],
            ],
        ]);
    }

    /**
     * Refresh token
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed successfully',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * Google OAuth - Authenticate user with Google ID token from mobile app
     */
    public function googleAuth(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_token' => ['required', 'string'],
            'name' => ['nullable', 'string'],
            'email' => ['nullable', 'email'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Verify the ID token with Google
            $googleUser = $this->verifyGoogleToken($request->id_token);
            
            if (!$googleUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Google token',
                    'errors' => ['id_token' => 'The provided Google token is invalid or expired'],
                ], 401);
            }

            // Extract user information from Google response
            $googleId = $googleUser['sub'] ?? $googleUser['id'] ?? null;
            $email = $googleUser['email'] ?? $request->email;
            $name = $googleUser['name'] ?? $request->name;
            $emailVerified = $googleUser['email_verified'] ?? false;

            if (!$googleId || !$email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to retrieve user information from Google',
                    'errors' => ['google' => 'Missing required user information'],
                ], 400);
            }

            // Check if user already exists
            $existingUser = User::where('email', $email)->first();
            
            // Create or update user
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name ?? 'Google User',
                    'google_id' => $googleId,
                    'password' => $existingUser && $existingUser->password ? $existingUser->password : Hash::make(Str::random(32)),
                    'status' => 'active',
                    'role' => $existingUser && $existingUser->role ? $existingUser->role : 'user',
                    'gdpr_consent' => true,
                    'email_verified_at' => $emailVerified ? now() : null,
                ]
            );

            // Generate Sanctum token
            $token = $user->createToken('google_auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Google authentication successful',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'status' => $user->status,
                        'email_verified_at' => $user->email_verified_at,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Google authentication failed',
                'errors' => ['server' => 'An error occurred during Google authentication: ' . $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Apple OAuth (placeholder - implement based on your OAuth setup)
     */
    public function appleAuth(Request $request): JsonResponse
    {
        // This would integrate with your existing Apple OAuth implementation
        return response()->json([
            'success' => false,
            'message' => 'Apple OAuth not implemented yet',
            'errors' => ['oauth' => 'Apple OAuth integration pending'],
        ], 501);
    }

    /**
     * Forgot password
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $status = Password::sendResetLink($request->only('email'));

        if ($status == Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset link sent to your email',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unable to send reset link',
            'errors' => ['email' => __($status)],
        ], 400);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => ['required'],
            'email' => ['required', 'email'],
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

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Password reset failed',
            'errors' => ['email' => __($status)],
        ], 400);
    }

    /**
     * Verify Google ID token with Google's servers
     * 
     * @param string $idToken
     * @return array|null
     */
    private function verifyGoogleToken(string $idToken): ?array
    {
        try {
            // Google's token verification endpoint
            $url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($idToken);
            
            // Make request to Google
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode != 200 || !$response) {
                return null;
            }
            
            $data = json_decode($response, true);
            
            // Verify the token is for our app (if GOOGLE_CLIENT_ID is set)
            $googleClientId = config('services.google.client_id');
            if ($googleClientId && isset($data['aud']) && $data['aud'] != $googleClientId) {
                return null;
            }
            
            return $data;
            
        } catch (\Exception $e) {
            return null;
        }
    }
}
