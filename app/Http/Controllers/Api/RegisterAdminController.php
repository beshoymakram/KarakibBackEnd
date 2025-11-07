<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterAdminController extends Controller
{
    public function registerAdmin(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'type' => 'admin',
        ]);

        $admins = User::where('type', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'content' => 'new_admin_added',
                'icon' => asset('images/new-user.svg'),
            ]);
        }

        return response()->json([
            'message' => __('messages.admin_added_successfully'),
            'user' => $user
        ], 201);
    }
}
