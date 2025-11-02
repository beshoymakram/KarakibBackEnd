<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Request as ModelsRequest;
use App\Models\RequestItem;
use App\Models\User;
use App\Models\WasteItem;
use App\Notifications\RequestConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    public function index()
    {
        $requests = ModelsRequest::with(['items.item', 'user', 'address', 'courier'])
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($requests);
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'user_address_id' => 'required|exists:user_addresses,id',
            'payout_method' => 'required|in:earn,donate',
        ]);

        $user = $request->user();
        $sessionId = $request->header('X-Cart-Session') ?? $request->cookie('cart_session');

        // Get only product cart items (not waste items)
        $cartItems = CartItem::with('cartable')
            ->where('cartable_type', WasteItem::class)
            ->when($user, function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            }, function ($query) use ($sessionId) {
                return $query->where('session_id', $sessionId)->whereNull('user_id');
            })
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => __('messages.cart_empty')], 400);
        }

        // Calculate total using the subtotal attribute
        $total = $cartItems->sum('points');

        DB::beginTransaction();

        try {
            $order = ModelsRequest::create([
                'request_number' => ModelsRequest::generateNumber(),
                'user_id' => $user->id,
                'total' => $total,
                'payout_method' => $validated['payout_method'],
                'user_address_id' => $validated['user_address_id'],
                'status' => 'pending'
            ]);

            foreach ($cartItems as $cartItem) {
                RequestItem::create([
                    'request_id' => $order->id,
                    'waste_item_id' => $cartItem->cartable_id,
                    'quantity' => $cartItem->quantity,
                    'subtotal' => $cartItem->cartable->points_per_unit * $cartItem->quantity,
                ]);
            }

            CartItem::where('cartable_type', WasteItem::class)
                ->when($user, function ($query) use ($user) {
                    return $query->where('user_id', $user->id);
                }, function ($query) use ($sessionId) {
                    return $query->where('session_id', $sessionId)->whereNull('user_id');
                })
                ->delete();

            DB::commit();
            $request->user()->notify(new RequestConfirmation($order));

            return response()->json([
                'message' => __('messages.request_placed_successfully'),
                'request' => $order->load('items.item'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Request failed: ' . $e->getMessage()], 500);
        }
    }


    public function cancelRequest(ModelsRequest $request)
    {
        $request->update([
            'status' => 'cancelled'
        ]);

        return response()->json([
            'message' => __('messages.request_cancelled_successfully')
        ], 201);
    }

    public function completeRequest(ModelsRequest $request)
    {
        $request->update([
            'status' => 'completed'
        ]);

        $request->user->addPoints($request->total, 'Points earned from request #' . $request->request_number);
        if ($request->payout_method === 'donate') {

            $request->user->donatePoints($request->total, 'Points donated from request #' . $request->request_number);
        }

        return response()->json([
            'message' => __('messages.request_completed_successfully')
        ], 201);
    }

    public function assignRequest(ModelsRequest $request, User $courier)
    {
        if ($courier->type !== 'courier') {
            return response()->json(['message' => __('messages.unauthorized')], 401);
        }

        $request->assignCourier($courier->id);

        return response()->json([
            'message' => __('messages.order_assigned_to_courier')
        ], 201);
    }

    public function unassignRequest(ModelsRequest $request)
    {
        $request->unassignCourier();

        return response()->json([
            'message' => __('messages.order_unassigned')
        ], 201);
    }
}
