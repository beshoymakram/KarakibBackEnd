<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Request as ModelsRequest;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    public function getAssignedRequests(Request $request)
    {
        if ($request->user()->type !== 'courier') {
            return response()->json(['message' => __('messages.unauthorized')], 401);
        }

        $requests = ModelsRequest::with(['items.item', 'user', 'address'])
            ->where('courier_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($requests);
    }

    public function getAssignedOrders(Request $request)
    {
        if ($request->user()->type !== 'courier') {
            return response()->json(['message' => __('messages.unauthorized')], 401);
        }

        $orders = Order::with(['items.product', 'user', 'address'])
            ->where('courier_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }
}
