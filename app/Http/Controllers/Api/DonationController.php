<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class DonationController extends Controller
{
    public function index()
    {
        $donations = Donation::all();

        return response()->json($donations);
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:25|max:10000',
            'fund_name' => 'required|in:Education Fund,Reforestation,Community Health',
        ]);

        DB::beginTransaction();

        try {
            $donation = Donation::create([
                'donation_number' => Donation::generateNumber(),
                'amount' => $validated['amount'],
                'fund_name' => $validated['fund_name'],
                'status' => 'pending'
            ]);


            Stripe::setApiKey(env('STRIPE_SECRET'));

            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'egp',
                        'product_data' => [
                            'name' => 'Donation #' . $donation->donation_number,
                            'description' => 'To support the ' . $donation->fund_name,
                        ],
                        'unit_amount' => (int) ($donation->amount * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'metadata' => [
                    'donation_id' => $donation->id,
                    'donation_number' => $donation->donation_number,
                ],
                'success_url' => env('FRONTEND_URL') . "/donate/success?donation_number={$donation->donation_number}&transaction_id={CHECKOUT_SESSION_ID}",
                'cancel_url' => env('FRONTEND_URL') . "/donate/failed?donation_number={$donation->donation_number}&transaction_id={CHECKOUT_SESSION_ID}",
            ]);

            $donation->update([
                'stripe_session_id' => $session->id,
                'stripe_payment_intent_id' => $session->payment_intent,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Stripe checkout session created',
                'url' => $session->url,
                'donation_id' => $donation->id,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Donation failed: ' . $e->getMessage()], 500);
        }
    }

    public function verifyPayment(Request $request)
    {
        $sessionId = $request->query('transaction_id');
        $donationNumber = $request->query('donation_number');

        if (!$sessionId || !$donationNumber) {
            return response()->json(['valid' => false, 'message' => 'Missing parameters'], 400);
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $session = StripeSession::retrieve($sessionId);
            $paymentStatus = $session->payment_status ?? 'unpaid';
            $paid = $paymentStatus === 'paid';

            if (isset($session->metadata->donation_number) && $session->metadata->donation_number !== $donationNumber) {
                return response()->json(['valid' => false, 'message' => 'Order mismatch'], 403);
            }

            if ($paid) {
                $order = Donation::where('donation_number', $donationNumber)->first();
                if ($order && $order->status !== 'completed') {
                    $order->update(['status' => 'completed']);
                }
            }

            return response()->json([
                'valid' => $paid,
                'payment_status' => $paymentStatus,
                'donation_number' => $donationNumber,
                'transaction_id' => $sessionId,
            ]);
        } catch (\Exception $e) {
            return response()->json(['valid' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
