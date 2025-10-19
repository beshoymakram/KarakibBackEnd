<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->user()->id,
            'phone' => 'required|string|unique:users,phone,' . auth()->user()->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            if (auth()->user()->avatar && Storage::disk('public')->exists(auth()->user()->avatar)) {
                Storage::disk('public')->delete(auth()->user()->avatar);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        auth()->user()->update($data);

        return response()->json([
            'message' => 'Personal info updated successfully',
            'user' => auth()->user(),
        ]);
    }

    public function destroy()
    {
        $user = auth()->user();
        $user->status = 'deleted';
        $user->save();
        $user->delete();

        return response()->json([
            'message' => 'Your account has been deleted successfully'
        ]);
    }
}
