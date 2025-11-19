<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BalanceHistory;
use App\Models\Notification;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{
    public function index()
    {
        $history = BalanceHistory::with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($history);
    }

    public function refundWithdraw(BalanceHistory $withdrawal)
    {
        $withdrawal->update([
            'status' => 'cancelled'
        ]);

        $withdrawal->user->refundBalance($withdrawal->amount, 'Refund amount of ' . $withdrawal->amount);

        Notification::create([
            'user_id' => $withdrawal->user_id,
            'content' => 'withdraw_refunded',
            'icon' => asset('images/cancel.svg'),
        ]);

        return response()->json([
            'message' => __('messages.withdraw_refunded')
        ], 201);
    }

    public function completeWithdraw(BalanceHistory $withdrawal, Request $request)
    {
        $data = $request->validate([
            'proof' => 'required|file|mimes:jpeg,png,jpg,gif,svg,pdf|max:5000'
        ]);

        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('products', 'public');
            $data['proof'] = $proofPath;
        }

        $withdrawal->update([
            'status' => 'completed',
            'proof' => $data['proof']
        ]);

        Notification::create([
            'user_id' => $withdrawal->user_id,
            'content' => 'withdraw_completed',
            'icon' => asset('images/checkmark.svg'),
        ]);

        return response()->json([
            'message' => __('messages.withdraw_completed')
        ], 201);
    }
}
