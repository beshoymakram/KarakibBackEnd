<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class PaymentController extends Controller
{
    public function processPayment(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'card_number' => 'required|string|size:16',
            'cvv' => 'required|string|size:3',
            'expiry_month' => 'required|integer|between:1,12',
            'expiry_year' => 'required|integer|min:2024',
        ]);

        // Simulate processing delay
        sleep(1);

        // Mock payment logic
        $success = $this->mockPaymentProcessor($validated['card_number']);

        if ($success) {
            // Create transaction record
            $transaction = [
                'transaction_id' => 'TXN_' . strtoupper(uniqid()),
                'amount' => $validated['amount'],
                'status' => 'success',
                'timestamp' => now(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Payment successful',
                'transaction' => $transaction
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment declined'
        ], 422);
    }

    private function mockPaymentProcessor($cardNumber)
    {
        // Test cards
        $testCards = [
            '4242424242424242' => true,  // Success
            '4000000000000002' => false, // Decline
            '4000000000009995' => false, // Insufficient funds
        ];

        return $testCards[$cardNumber] ?? (substr($cardNumber, -1) % 2 === 0);
    }

    public function getTransactionStatus($transactionId)
    {
        return response()->json([
            'transaction_id' => $transactionId,
            'status' => 'completed',
            'amount' => 100.00,
            'timestamp' => now()
        ]);
    }

    public function createStripeSession(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Validate / calculate server-side instead of trusting client
        $amount = (int) $request->input('amount'); // amount in cents
        $currency = $request->input('currency', 'usd');

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => $request->input('description', 'Order'),
                    ],
                    'unit_amount' => $amount,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'metadata' => [
                'order_id' => $request->input('order_id', 'none'),
            ],
            'success_url' => env('APP_URL') . '/payment-success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => env('APP_URL') . '/payment-cancel',
        ]);

        return response()->json(['url' => $session->url]);
    }
}
