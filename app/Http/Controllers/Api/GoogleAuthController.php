<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
        // $url = Socialite::driver('google')
        // ->redirect()
        // ->getTargetUrl();
        // dd($url); // This will show you the exact Google URL
    }

    /**
     * Handle Google OAuth callback
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Find or create user
            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                // Update Google ID if not set
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->id,
                    ]);
                }
            } else {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'phone' => '', // Set default or ask later
                    'type' => 'user',
                    'password' => Hash::make(Str::random(24)), // Random password
                    'email_verified_at' => now(), // Auto-verify Google users
                ]);
            }

            // Create token (if using Sanctum)
            $token = $user->createToken('auth_token')->plainTextToken;

            // Redirect to frontend with token
            return redirect()->to(
                env('FRONTEND_URL') . '/auth/callback?token=' . $token . '&user=' . urlencode(json_encode($user))
            );
        } catch (\Exception $e) {
            return redirect()->to(
                env('FRONTEND_URL') . '/login?error=' . urlencode('Google authentication failed: ' . $e->getMessage())
            );
        }
    }
}
