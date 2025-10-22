<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\UserAddress;
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

    public function getAddresses(Request $request)
    {
        $addresses = UserAddress::where('user_id', $request->user()?->id)->get();

        return response()->json($addresses);
    }

    public function createAddress(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'street_address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
        ]);

        $data['user_id'] = $request->user()?->id;

        UserAddress::create($data);
        return response()->json([
            'message' => 'Address created successfully'
        ], 201);
    }

    public function updateAddress(UserAddress $address, Request $request)
    {
        if ($address->user_id !== $request->user()?->id) {
            return response()->json(['message' => 'Address not found for this user'], 404);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'street_address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
        ]);

        $address->update($data);
        return response()->json([
            'message' => 'Address updated successfully'
        ], 201);
    }

    public function deleteAddress(UserAddress $address, Request $request)
    {
        if ($address->user_id !== $request->user()?->id) {
            return response()->json(['message' => 'Address not found for this user'], 404);
        }
        $address->delete();
        return response()->json([
            'message' => 'Address deleted successfully'
        ], 201);
    }

    public function getOrders(Request $request)
    {
        $orders = Order::with(['items.product', 'user', 'address'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    public function cancelOrder(Order $order, Request $request)
    {
        if ($order->user_id !== $request->user()?->id) {
            return response()->json(['message' => 'Order not found for this user'], 404);
        }

        $order->update([
            'status' => 'cancelled'
        ]);

        return response()->json([
            'message' => 'Order cancelled successfully'
        ], 201);
    }
}
