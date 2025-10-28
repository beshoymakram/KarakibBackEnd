<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PointHistory;
use App\Models\Request as ModelsRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AdminStatisticsController extends Controller
{
    public function index()
    {
        $usersCount = User::count();
        $newUsersCount = User::where('created_at', '>=', now()->subDay())->count();

        $ordersCount = Order::count();
        $newOrdersCount = Order::where('created_at', '>=', now()->subDay())->count();

        $requestsCount = ModelsRequest::count();
        $newRequestsCount = ModelsRequest::where('status', 'pending')->count();

        $donatedPoints = PointHistory::where('type', 'donate')->sum('points');


        return response()->json([
            'users' => [
                'total' => $usersCount,
                'new' => $newUsersCount
            ],
            'orders' => [
                'total' => $ordersCount,
                'new' => $newOrdersCount
            ],
            'requests' => [
                'total' => $requestsCount,
                'pending' => $newRequestsCount
            ],
            'donated_points' => $donatedPoints
        ]);
    }
}
