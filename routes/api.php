<?php

use App\Http\Controllers\Api\AdminStatisticsController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\GoogleAuthController;
use App\Http\Controllers\Api\InquiryController;
use App\Http\Controllers\Api\InquiryControllerController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\WasteItemController;
use App\Http\Controllers\Api\WasteTypeController;
use App\Http\Controllers\Api\ProductsCategoryController;
use App\Http\Controllers\Api\RegisterAdminController;
use App\Http\Controllers\Api\RegisterCourierController;
use App\Http\Controllers\Api\RequestController;
use App\Http\Controllers\Api\StripeWebhookController;
use App\Http\Controllers\Api\UserPasswordResetController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ReviewController;
use App\Models\CourierRequest;
use App\Models\ProductsCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::options('{any}', function () {
    return response()->noContent();
})->where('any', '.*');

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);



Route::get('/storage-link', function () {
    if (request()->input('secret') !== 'beshoy') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    try {
        Artisan::call('storage:link');
        return response()->json(['message' => 'Storage link created successfully']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

Route::get('/migrate', function () {
    if (request()->input('secret') !== 'beshoy') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    try {
        Artisan::call('migrate');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        Artisan::call('event:clear');
        Artisan::call('optimize:clear');
        Artisan::call('queue:flush');

        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');

        Artisan::call('optimize');
        return response()->json(['message' => 'migrated successfully']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

Route::get('/admin-statistics', [WasteTypeController::class, 'index']);


Route::get('/waste-types', [WasteTypeController::class, 'index']);
Route::get('/waste-types/{id}', [WasteTypeController::class, 'show']);

Route::get('/waste-items', [WasteItemController::class, 'index']);
Route::get('/waste-items/{id}', [WasteItemController::class, 'show']);

// Public product routes
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

Route::get('/products-categories', [ProductsCategoryController::class, 'index']);
Route::get('/products-categories/{id}', [ProductsCategoryController::class, 'show']);

Route::post('/payment/process', [PaymentController::class, 'processPayment']);
Route::get('/payment/status/{id}', [PaymentController::class, 'getTransactionStatus']);

// Cart
Route::get('/cart', [CartController::class, 'index']);
Route::post('/cart', [CartController::class, 'add']);
Route::put('/cart/{id}', [CartController::class, 'update']);
Route::delete('/cart/{id}', [CartController::class, 'remove']);
Route::delete('/cart', [CartController::class, 'clear']);

Route::post('/donate', [DonationController::class, 'checkout']);
Route::post('/inquiries', [InquiryController::class, 'store']);

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);
Route::get('/verify-payment', [OrderController::class, 'verifyPayment']);
Route::get('/verify-donation', [DonationController::class, 'verifyDonation']);

Route::post('/forgot-password', [UserPasswordResetController::class, 'send']);
Route::post('/forgot-password/resend', [UserPasswordResetController::class, 'resend']);
Route::post('/verify-code', [UserPasswordResetController::class, 'store']);
Route::post('/reset-password', [UserPasswordResetController::class, 'update']);
Route::get('/products/{productId}/reviews', [ReviewController::class, 'index']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LogoutController::class, 'logout']);
    Route::get('/user', fn(Request $request) => $request->user()->load('orders', 'requests', 'notifications'));

    Route::put('/profile', [ProfileController::class, 'update']);
    Route::delete('/profile/destroy', [ProfileController::class, 'destroy']);
    Route::get('/profile/addresses', [ProfileController::class, 'getAddresses']);
    Route::post('/profile/addresses', [ProfileController::class, 'createAddress']);
    Route::put('/profile/addresses/{address}', [ProfileController::class, 'updateAddress']);
    Route::delete('/profile/addresses/{address}', [ProfileController::class, 'deleteAddress']);
    Route::get('/profile/orders', [ProfileController::class, 'getOrders']);
    Route::put('/profile/orders/{order}/cancel', [ProfileController::class, 'cancelOrder']);
    Route::get('/profile/points', [ProfileController::class, 'pointsHistory']);
    Route::post('/profile/points/convert', [ProfileController::class, 'convertPoints']);
    Route::post('/profile/points/donate', [ProfileController::class, 'donatePoints']);
    Route::get('/profile/balance', [ProfileController::class, 'balanceHistory']);
    Route::post('/profile/balance/withdraw', [ProfileController::class, 'withdrawBalance']);
    Route::get('/profile/requests', [ProfileController::class, 'getRequests']);
    Route::put('/profile/requests/{request}/cancel', [ProfileController::class, 'cancelRequest']);
    Route::get('/profile/notifications', [NotificationController::class, 'index']);
    Route::post('/profile/notifications/read', [NotificationController::class, 'markAllAsRead']);
    Route::post('/profile/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);

    // Merge guest cart on login (protected)
    Route::post('/cart/merge', [CartController::class, 'merge']);

    // Orders
    Route::post('/checkout', [OrderController::class, 'checkout']);
    Route::post('/collect', [RequestController::class, 'checkout']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);

    // Check if user can review
    Route::get('/products/{productId}/can-review', [ReviewController::class, 'canReview']);

    // Submit/manage reviews
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{reviewId}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{reviewId}', [ReviewController::class, 'destroy']);

    // Your protected routes here
    Route::middleware('admin')->group(function () {
        Route::get('/numbers', [AdminStatisticsController::class, 'index']);

        Route::post('/registerAdmin', [RegisterAdminController::class, 'registerAdmin']);
        Route::post('/registerCourier', [RegisterCourierController::class, 'registerCourier']);

        Route::get('/donations', [DonationController::class, 'index']);

        Route::get('/orders', [OrderController::class, 'index']);
        Route::post('/orders', [OrderController::class, 'store']);
        Route::put('/orders/{category}', [OrderController::class, 'update']);
        Route::delete('/orders/{category}', [OrderController::class, 'destroy']);
        Route::put('/orders/{order}/cancel', [OrderController::class, 'cancelOrder']);
        Route::put('/orders/{order}/complete', [OrderController::class, 'completeOrder']);
        Route::post('/orders/{order}/assign/{courier}', [OrderController::class, 'assignOrder']);
        Route::post('/orders/{order}/unassign', [OrderController::class, 'unassignOrder']);

        Route::get('/requests', [RequestController::class, 'index']);
        Route::post('/requests', [RequestController::class, 'store']);
        Route::put('/requests/{request}', [RequestController::class, 'update']);
        Route::delete('/requests/{request}', [RequestController::class, 'destroy']);
        Route::put('/requests/{request}/cancel', [RequestController::class, 'cancelRequest']);
        Route::put('/requests/{request}/complete', [RequestController::class, 'completeRequest']);
        Route::post('/requests/{request}/assign/{courier}', [RequestController::class, 'assignRequest']);
        Route::post('/requests/{request}/unassign', [RequestController::class, 'unassignRequest']);


        Route::post('/products-categories', [ProductsCategoryController::class, 'store']);
        Route::put('/products-categories/{category}', [ProductsCategoryController::class, 'update']);
        Route::delete('/products-categories/{category}', [ProductsCategoryController::class, 'destroy']);

        Route::get('/inquiries', [InquiryController::class, 'index']);
        Route::put('/inquiries/{inquiry}', [InquiryController::class, 'update']);
        Route::delete('/inquiries/{inquiry}', [InquiryController::class, 'destroy']);

        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);

        Route::post('/waste-types', [WasteTypeController::class, 'store']);
        Route::put('/waste-types/{wasteType}', [WasteTypeController::class, 'update']);
        Route::delete('/waste-types/{wasteType}', [WasteTypeController::class, 'destroy']);

        Route::post('/waste-items', [WasteItemController::class, 'store']);
        Route::put('/waste-items/{id}', [WasteItemController::class, 'update']);
        Route::delete('/waste-items/{id}', [WasteItemController::class, 'destroy']);

        Route::get('/users', [UsersController::class, 'index']);
        Route::get('/users/couriers', [UsersController::class, 'getCouriers']);
        Route::put('/users/{user}', [UsersController::class, 'update']);

        Route::delete('/users/{user}', [UsersController::class, 'destroy']);
    });
    Route::middleware('courier')->group(function () {
        Route::get('/courier/numbers', [CourierController::class, 'index']);
        Route::get('/assigned-requests', [CourierController::class, 'getAssignedRequests']);
        Route::get('/assigned-orders', [CourierController::class, 'getAssignedOrders']);

        Route::post('/orders/scan-qr', [OrderController::class, 'scanQrCode']);
        Route::post('/orders/verify-qr', [OrderController::class, 'getOrderByQr']);

        Route::post('/requests/scan-qr', [RequestController::class, 'scanQrCode']);
        Route::post('/requests/verify-qr', [RequestController::class, 'getOrderByQr']);
    });
});
