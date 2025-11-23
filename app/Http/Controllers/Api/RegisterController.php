<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Notifications\Welcome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'type' => 'sometimes|in:user,courier',
            'personal_id' => 'required_if:type,courier|file|mimes:jpeg,png,jpg,gif,svg,webp,pdf|max:2048'
        ]);

        if ($request->hasFile('personal_id')) {
            $personalIdPath = $request->file('personal_id')->store('personal-ids', 'public');
            $validated['personal_id'] = $personalIdPath;
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'type' => $validated['type'] ?? 'user',
            'status' => $validated['type'] == 'courier' ? 'onhold' : 'active',
            'personal_id' => $validated['personal_id'] ?? null,
        ]);

        $user = User::with(['orders'])->find($user->id);

        if ($user->type == 'courier') {
            return response()->json([
                'message' => __('messages.your_account_is_under_verification'),
                'status' => 'onhold'
            ], 403);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        $user->notify(new Welcome);

        Notification::create([
            'user_id' => $user->id,
            'content' => 'welcome_to_karakib',
            'icon' => asset('images/happy-face.svg'),
        ]);

        $admins = User::where('type', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'content' => 'new_user',
                'icon' => asset('images/new-user.svg'),
            ]);
        }

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }
}
