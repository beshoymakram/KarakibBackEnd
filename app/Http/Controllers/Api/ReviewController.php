<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Check if user can review a product
     */
    public function canReview(Request $request, $productId)
    {
        $user = $request->user();

        // Find completed orders containing this product
        $eligibleOrder = Order::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereHas('items', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->first();

        if (!$eligibleOrder) {
            return response()->json([
                'can_review' => false,
                'message' => __('messages.must_purchase_to_review')
            ]);
        }

        // Check if already reviewed
        $existingReview = Review::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->where('order_id', $eligibleOrder->id)
            ->first();

        return response()->json([
            'can_review' => !$existingReview,
            'has_reviewed' => (bool) $existingReview,
            'existing_review' => $existingReview,
            'order_id' => $eligibleOrder->id
        ]);
    }

    /**
     * Submit a review
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $user = $request->user();

        // Verify order belongs to user and is completed
        $order = Order::where('id', $validated['order_id'])
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->first();

        if (!$order) {
            return response()->json([
                'message' => __('messages.invalid_order')
            ], 403);
        }

        // Verify product is in the order
        $orderItem = $order->items()
            ->where('product_id', $validated['product_id'])
            ->first();

        if (!$orderItem) {
            return response()->json([
                'message' => __('messages.product_not_in_order')
            ], 403);
        }

        // Check if already reviewed
        $existingReview = Review::where('user_id', $user->id)
            ->where('product_id', $validated['product_id'])
            ->where('order_id', $validated['order_id'])
            ->first();

        if ($existingReview) {
            return response()->json([
                'message' => __('messages.already_reviewed')
            ], 400);
        }

        // Create review
        $review = Review::create([
            'user_id' => $user->id,
            'product_id' => $validated['product_id'],
            'order_id' => $validated['order_id'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment']
        ]);

        return response()->json([
            'message' => __('messages.review_submitted'),
            'review' => $review->load('user')
        ], 201);
    }

    /**
     * Get reviews for a product
     */
    public function index($productId)
    {
        $reviews = Review::where('product_id', $productId)
            ->with('user')
            ->latest()
            ->paginate(10);

        return response()->json($reviews);
    }

    /**
     * Update user's review
     */
    public function update(Request $request, $reviewId)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $user = $request->user();
        $review = Review::where('id', $reviewId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $review->update($validated);

        return response()->json([
            'message' => __('messages.review_updated'),
            'review' => $review->load('user')
        ]);
    }

    /**
     * Delete user's review
     */
    public function destroy(Request $request, $reviewId)
    {
        $user = $request->user();
        $review = Review::where('id', $reviewId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $review->delete();

        return response()->json([
            'message' => __('messages.review_deleted')
        ]);
    }
}
