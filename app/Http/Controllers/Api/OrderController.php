<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'user_address_id' => 'required|exists:user_addresses,id',
            'payment_method' => 'required|in:cash,card',
        ]);

        $user = $request->user();
        $cartItems = CartItem::with('product')
            ->where('user_id', $user->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $total = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

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
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price
                ]);

                // Update stock
                $cartItem->product->decrement('stock', $cartItem->quantity);
            }

            // Clear cart
            CartItem::where('user_id', $user->id)->delete();
            if ($validated['payment_method'] === 'cash') {
                DB::commit();

                return response()->json([
                    'message' => 'Order placed successfully (Cash on Delivery)',
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

    public function index(Request $request)
    {
        $orders = Order::with('items.product')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($orders);
    }

    public function show(Request $request, $id)
    {
        $order = Order::with('items.product')
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json($order);
    }
}
