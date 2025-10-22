<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid webhook signature'], 400);
        }

        // Handle successful payments
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            // Get order number (we passed it in metadata ideally)
            $orderNumber = $session->metadata->order_number ?? null;

            if ($orderNumber) {
                $order = Order::where('order_number', $orderNumber)->first();
                if ($order) {
                    $order->update(['status' => 'completed']);
                }
            }
        }

        return response()->json(['status' => 'success']);
    }
}
