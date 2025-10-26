<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['items.product', 'user', 'address'])
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($orders);
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'user_address_id' => 'required|exists:user_addresses,id',
            'payment_method' => 'required|in:cash,card',
        ]);

        $user = $request->user();
        $sessionId = $request->header('X-Cart-Session') ?? $request->cookie('cart_session');

        // Get only product cart items (not waste items)
        $cartItems = CartItem::with('cartable')
            ->where('cartable_type', Product::class)
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
        $total = $cartItems->sum('subtotal');

        DB::beginTransaction();

        try {
            $order = Order::create([
                'order_number' => Order::generateNumber(),
                'user_id' => $user->id,
                'total' => $total,
                'payment_method' => $validated['payment_method'],
                'user_address_id' => $validated['user_address_id'],
                'status' => 'pending'
            ]);

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->cartable_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->cartable->price
                ]);

                // Update stock
                $cartItem->cartable->decrement('stock', $cartItem->quantity);
            }

            // Clear only product items from cart
            CartItem::where('cartable_type', Product::class)
                ->when($user, function ($query) use ($user) {
                    return $query->where('user_id', $user->id);
                }, function ($query) use ($sessionId) {
                    return $query->where('session_id', $sessionId)->whereNull('user_id');
                })
                ->delete();

            if ($validated['payment_method'] === 'cash') {
                DB::commit();

                return response()->json([
                    'message' => __('messages.order_placed_successfully'),
                    'order' => $order->load('items.product'),
                ], 201);
            }

            Stripe::setApiKey(env('STRIPE_SECRET'));

            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'egp',
                        'product_data' => [
                            'name' => 'Order #' . $order->order_number,
                        ],
                        'unit_amount' => (int) ($total * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ],
                'success_url' => env('FRONTEND_URL') . "/checkout/success?order_number={$order->order_number}&transaction_id={CHECKOUT_SESSION_ID}",
                'cancel_url' => env('FRONTEND_URL') . "/checkout/failed?order_number={$order->order_number}&transaction_id={CHECKOUT_SESSION_ID}",
            ]);

            $order->update([
                'stripe_session_id' => $session->id,
                'stripe_payment_intent_id' => $session->payment_intent,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Stripe checkout session created',
                'url' => $session->url,
                'order_id' => $order->id,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Order failed: ' . $e->getMessage()], 500);
        }
    }

    public function verifyPayment(Request $request)
    {
        $sessionId = $request->query('transaction_id');
        $orderNumber = $request->query('order_number');

        if (!$sessionId || !$orderNumber) {
            return response()->json(['valid' => false, 'message' => __('messages.missing_parameters')], 400);
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $session = StripeSession::retrieve($sessionId);
            $paymentStatus = $session->payment_status ?? 'unpaid';
            $paid = $paymentStatus === 'paid';

            if (isset($session->metadata->order_number) && $session->metadata->order_number !== $orderNumber) {
                return response()->json(['valid' => false, 'message' => __('messages.order_mismatch')], 403);
            }

            if ($paid) {
                $order = Order::where('order_number', $orderNumber)->first();
                if ($order && $order->status !== 'completed') {
                    $order->update(['status' => 'completed']);
                }
            }

            return response()->json([
                'valid' => $paid,
                'payment_status' => $paymentStatus,
                'order_number' => $orderNumber,
                'transaction_id' => $sessionId,
            ]);
        } catch (\Exception $e) {
            return response()->json(['valid' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function cancelOrder(Order $order, Request $request)
    {
        $order->update([
            'status' => 'cancelled'
        ]);

        return response()->json([
            'message' => __('messages.order_cancelled_successfully')
        ], 201);
    }

    public function completeOrder(Order $order, Request $request)
    {
        $order->update([
            'status' => 'completed'
        ]);

        return response()->json([
            'message' => __('messages.order_completed_successfully')
        ], 201);
    }
}
