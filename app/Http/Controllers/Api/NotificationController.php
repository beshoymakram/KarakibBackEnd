<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()) {
            return response()->json($request->user()->notifications);
        } else {
            return response()->json([]);
        }
    }

    public function markAsRead(Notification $notification)
    {
        $notification->update(['read' => true]);

        return response()->json([
            'message' => __('messages.marked_as_read'),
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        $notifications = $request->user()->notifications;

        if ($notifications) {
            foreach ($notifications as $notification) {
                $notification->update(['read' => true]);
            }
        }

        return response()->json([
            'message' => __('messages.marked_all_as_read'),
        ]);
    }
}
