<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\Otp;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserPasswordResetController extends Controller
{
    public function send(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $request->email)->first();

        $user->generateOtp();
        $user->notify(new Otp);

        return response()->json([
            'message' => __('messages.otpSent'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|integer'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user->otp || !$user->otp_expired_at) {
            return response()->json([
                'message' => __('messages.noOtpFound'),
            ], 400);
        }

        if ($user->otp_expired_at->lt(now())) {
            $user->resetOtp();
            return response()->json([
                'message' => __('messages.otpExpired'),
            ], 400);
        }

        if ($request->input('otp') != $user->otp) {
            return response()->json([
                'message' => __('messages.wrongOtp'),
            ], 400);
        }

        $token = bin2hex(random_bytes(32));

        cache()->put("password_reset_verified:{$user->email}", $token, now()->addMinutes(15));

        $user->resetOtp();

        return response()->json([
            'message' => __('messages.enterNewPassword'),
            'reset_token' => $token,
        ], 200);
    }

    public function update(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'reset_token' => 'required|string',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        $cachedToken = cache()->get("password_reset_verified:{$user->email}");

        if (!$cachedToken || $cachedToken !== $request->reset_token) {
            return response()->json([
                'message' => __('messages.invalidOrExpiredToken'),
            ], 400);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        cache()->forget("password_reset_verified:{$user->email}");

        return response()->json([
            'message' => __('messages.passwordResetSuccess'),
        ], 200);
    }
}
