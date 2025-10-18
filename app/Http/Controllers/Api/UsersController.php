<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index()
    {
        if (auth()->user()) {
            $users = User::where('id', '!=', auth()->user()->id)->get();
        } else {
            $users = User::all();
        }

        return response()->json($users);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}
