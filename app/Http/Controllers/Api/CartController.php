<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartController extends Controller
{
    private function getSessionId(Request $request)
    {
        $sessionId = $request->header('X-Cart-Session') ?? $request->cookie('cart_session');

        if (!$sessionId) {
            $sessionId = Str::uuid()->toString();
        }

        return $sessionId;
    }

    public function index(Request $request)
    {
        $user = auth('sanctum')->user();
        $sessionId = $this->getSessionId($request);

        $cartItems = CartItem::with('product')
            ->forCart($user?->id, $sessionId)
            ->get();

        $total = $cartItems->sum('subtotal');
        $count = $cartItems->sum('quantity');


        // $total = $cartItems->sum(function ($item) {
        //     return $item->product->price * $item->quantity;
        // });

        return response()->json([
            'items' => $cartItems,
            'total' => $total,
            'count' => $count,
            'session_id' => $sessionId
        ])->cookie('cart_session', $sessionId, 60 * 24 * 30);
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if ($product->stock < $validated['quantity']) {
            return response()->json(['message' => 'Insufficient stock'], 400);
        }

        $user = auth('sanctum')->user();
        $sessionId = $this->getSessionId($request);

        $cartItem = CartItem::where('product_id', $validated['product_id'])
            ->when($user, function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            }, function ($query) use ($sessionId) {
                return $query->where('session_id', $sessionId)->whereNull('user_id');
            })
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $validated['quantity'];

            if ($product->stock < $newQuantity) {
                return response()->json(['message' => 'Insufficient stock'], 400);
            }

            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            if ($product->stock < $validated['quantity']) {
                return response()->json(['message' => 'Insufficient stock'], 400);
            }

            $cartItem = CartItem::create([
                'user_id' => $user?->id,
                'session_id' => $user ? null : $sessionId,
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity']
            ]);
        }

        return response()->json([
            'message' => 'Item added to cart',
            'item' => $cartItem->fresh()->load('product')
        ])->cookie('cart_session', $sessionId, 60 * 24 * 30);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $user = auth('sanctum')->user();
        $sessionId = $this->getSessionId($request);

        $cartItem = CartItem::where('id', $id)
            ->forCart($user?->id, $sessionId)
            ->firstOrFail();

        if ($cartItem->product->stock < $validated['quantity']) {
            return response()->json(['message' => 'Insufficient stock'], 400);
        }

        $cartItem->update(['quantity' => $validated['quantity']]);

        return response()->json([
            'message' => 'Cart updated',
            'item' => $cartItem->fresh()->load('product')
        ]);
    }

    public function remove(Request $request, $id)
    {

        $user = auth('sanctum')->user();
        $sessionId = $this->getSessionId($request);

        CartItem::where('id', $id)
            ->forCart($user?->id, $sessionId)
            ->delete();
        return response()->json(['message' => 'Item removed from cart']);
    }

    public function clear(Request $request)
    {
        $user = auth('sanctum')->user();
        $sessionId = $this->getSessionId($request);

        CartItem::forCart($user?->id, $sessionId)->delete();
        return response()->json(['message' => 'Cart cleared']);
    }

    // Merge guest cart to user cart on login
    public function merge(Request $request)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $sessionId = $this->getSessionId($request);

        // Get guest cart items
        $guestItems = CartItem::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->get();

        foreach ($guestItems as $guestItem) {
            // Check if user already has this product in cart
            $userItem = CartItem::where('user_id', $user->id)
                ->where('product_id', $guestItem->product_id)
                ->first();

            if ($userItem) {
                // Merge quantities
                $userItem->increment('quantity', $guestItem->quantity);
                $guestItem->delete();
            } else {
                // Transfer to user
                $guestItem->update([
                    'user_id' => $user->id,
                    'session_id' => null
                ]);
            }
        }

        return response()->json(['message' => 'Cart merged successfully']);
    }
}
