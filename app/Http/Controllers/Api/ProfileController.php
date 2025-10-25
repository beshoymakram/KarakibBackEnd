<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PointHistory;
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
            'email' => 'required|email|unique:users,email,' . $request->user()->id,
            'phone' => 'required|string|unique:users,phone,' . $request->user()->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            if ($request->user()->avatar && Storage::disk('public')->exists($request->user()->avatar)) {
                Storage::disk('public')->delete($request->user()->avatar);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        if ($request->user()->google_id && ($data['email'] !== $request->user()->email)) {
            return response()->json(['message' => _("messages.Users registered with google can't change their email.")], 404);
        }

        $request->user()->update($data);

        return response()->json([
            'message' => __('messages.Personal info updated successfully'),
            'user' => $request->user(),
        ]);
    }

    public function destroy()
    {
        $user = $request->user();
        $user->status = 'deleted';
        $user->save();
        $user->delete();

        return response()->json([
            'message' => __('messages.Your account has been deleted successfully')
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
            'message' => __('messages.Address created successfully')
        ], 201);
    }

    public function updateAddress(UserAddress $address, Request $request)
    {
        if ($address->user_id !== $request->user()?->id) {
            return response()->json(['message' => __('messages.Address not found for this user')], 404);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'street_address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
        ]);

        $address->update($data);
        return response()->json([
            'message' => __('messages.Address updated successfully')
        ], 201);
    }

    public function deleteAddress(UserAddress $address, Request $request)
    {
        if ($address->user_id !== $request->user()?->id) {
            return response()->json(['message' => __('messages.Address not found for this user')], 404);
        }
        $address->delete();
        return response()->json([
            'message' => __('messages.Address deleted successfully')
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
            return response()->json(['message' => __('messages.Order not found for this user')], 404);
        }

        $order->update([
            'status' => 'cancelled'
        ]);

        return response()->json([
            'message' => __('messages.Order cancelled successfully')
        ], 201);
    }

    public function pointsHistory(Request $request)
    {
        $history = PointHistory::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($history);
    }
}
