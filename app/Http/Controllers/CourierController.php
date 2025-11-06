<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Request as ModelsRequest;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    public function index(Request $request)
    {
        $ordersCount = Order::where('courier_id', $request->user()->id)->count();
        $newOrdersCount = Order::where('courier_id', $request->user()->id)->where('created_at', '>=', now()->subDay())->count();

        $requestsCount = ModelsRequest::where('courier_id', $request->user()->id)->count();
        $newRequestsCount = ModelsRequest::where('courier_id', $request->user()->id)->where('status', 'assigned')->count();

        return response()->json([
            'orders' => [
                'total' => $ordersCount,
                'new' => $newOrdersCount
            ],
            'requests' => [
                'total' => $requestsCount,
                'pending' => $newRequestsCount
            ],
        ]);
    }
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
