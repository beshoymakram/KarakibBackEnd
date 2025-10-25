<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\WasteItem;
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

        $cartItems = CartItem::with('cartable')
            ->forCart($user?->id, $sessionId)
            ->get();

        $productItems = $cartItems->filter(fn($item) => $item->cartable_type === Product::class);
        $wasteItems = $cartItems->filter(fn($item) => $item->cartable_type === WasteItem::class);

        $totalPoints = $wasteItems->sum(function ($item) {
            return $item->cartable->points_per_unit * $item->quantity;
        });

        return response()->json([
            'items' => $cartItems,
            'products' => [
                'items' => $productItems,
                'total' => $productItems->sum('subtotal'),
                'count' => $productItems->sum('quantity')
            ],
            'waste' => [
                'items' => $wasteItems->values(),
                'points' => $totalPoints,
                'count' => $wasteItems->sum('quantity')
            ],
            'session_id' => $sessionId
        ])->cookie('cart_session', $sessionId, 60 * 24 * 30);
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:product,waste',
            'item_id' => 'required|integer',
            'quantity' => 'required|integer|min:1'
        ]);

        // Determine the model class based on type
        $modelClass = $validated['type'] === 'product' ? Product::class : WasteItem::class;
        $item = $modelClass::findOrFail($validated['item_id']);

        // Check stock for products
        if ($validated['type'] === 'product' && $item->stock < $validated['quantity']) {
            return response()->json(['message' => __('messages.insufficient_stock')], 400);
        }
        $user = auth('sanctum')->user();
        $sessionId = $this->getSessionId($request);

        // Find existing cart item
        $cartItem = CartItem::where('cartable_type', $modelClass)
            ->where('cartable_id', $validated['item_id'])
            ->when($user, function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            }, function ($query) use ($sessionId) {
                return $query->where('session_id', $sessionId)->whereNull('user_id');
            })
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $validated['quantity'];

            // Check stock for products when updating
            if ($validated['type'] === 'product' && $item->stock < $newQuantity) {
                return response()->json(['message' => __('messages.insufficient_stock')], 400);
            }

            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            $cartItem = CartItem::create([
                'user_id' => $user?->id,
                'session_id' => $user ? null : $sessionId,
                'cartable_type' => $modelClass,
                'cartable_id' => $validated['item_id'],
                'quantity' => $validated['quantity']
            ]);
        }

        return response()->json([
            'message' => __('messages.added_to_cart'),
            'item' => $cartItem->fresh()->load('cartable')
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

        // Check stock only for products
        if ($cartItem->cartable_type === Product::class) {
            if ($cartItem->cartable->stock < $validated['quantity']) {
                return response()->json(['message' => __('messages.insufficient_stock')], 400);
            }
        }

        $cartItem->update(['quantity' => $validated['quantity']]);

        return response()->json([
            'message' => __('messages.cart_updated'),
            'item' => $cartItem->fresh()->load('cartable')
        ]);
    }

    public function remove(Request $request, $id)
    {
        $user = auth('sanctum')->user();
        $sessionId = $this->getSessionId($request);

        CartItem::where('id', $id)
            ->forCart($user?->id, $sessionId)
            ->delete();

        return response()->json(['message' => __('messages.item_removed')]);
    }

    public function clear(Request $request)
    {
        $validated = $request->validate([
            'type' => 'nullable|in:product,waste,all'
        ]);

        $user = auth('sanctum')->user();
        $sessionId = $this->getSessionId($request);

        $query = CartItem::forCart($user?->id, $sessionId);

        // Allow clearing specific type or all
        if (isset($validated['type']) && $validated['type'] !== 'all') {
            $modelClass = $validated['type'] === 'product' ? Product::class : WasteItem::class;
            $query->where('cartable_type', $modelClass);
        }

        $query->delete();

        return response()->json(['message' => __('messages.cart_cleared')]);
    }

    // Merge guest cart to user cart on login
    public function merge(Request $request)
    {

        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => __('messages.unauthorized')], 401);
        }

        $sessionId = $this->getSessionId($request);

        // Get guest cart items
        $guestItems = CartItem::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->get();

        DB::transaction(function () use ($guestItems, $user) {
            foreach ($guestItems as $guestItem) {
                // Check if user already has this item in cart
                $userItem = CartItem::where('user_id', $user->id)
                    ->where('cartable_type', $guestItem->cartable_type)
                    ->where('cartable_id', $guestItem->cartable_id)
                    ->first();

                if ($userItem) {
                    // Merge quantities
                    $newQuantity = $userItem->quantity + $guestItem->quantity;

                    // Check stock limit for products
                    if ($guestItem->cartable_type === Product::class) {
                        $newQuantity = min($newQuantity, $guestItem->cartable->stock);
                    }

                    $userItem->update(['quantity' => $newQuantity]);
                    $guestItem->delete();
                } else {
                    // Transfer to user
                    $guestItem->update([
                        'user_id' => $user->id,
                        'session_id' => null
                    ]);
                }
            }
        });

        return response()->json(['message' => __('messages.cart_merged')]);
    }

    public function getByType(Request $request, $type)
    {
        $validated = ['type' => $type];

        if (!in_array($type, ['product', 'waste'])) {
            return response()->json(['message' => 'Invalid type'], 400);
        }

        $user = auth('sanctum')->user();
        $sessionId = $this->getSessionId($request);

        $modelClass = $type === 'product' ? Product::class : WasteItem::class;

        $cartItems = CartItem::with('cartable')
            ->where('cartable_type', $modelClass)
            ->forCart($user?->id, $sessionId)
            ->get();

        if ($type === 'product') {
            $total = $cartItems->sum('subtotal');
            $extra = ['total' => $total];
        } else {
            $points = $cartItems->sum(function ($item) {
                return $item->cartable->points_per_unit * $item->quantity;
            });
            $extra = ['points' => $points];
        }

        return response()->json([
            'items' => $cartItems,
            'count' => $cartItems->sum('quantity'),
            ...$extra
        ]);
    }
}
