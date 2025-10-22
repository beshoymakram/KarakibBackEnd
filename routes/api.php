<?php

use App\Http\Controllers\Api\CartController;
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
use App\Http\Controllers\Api\StripeWebhookController;
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

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);
Route::get('/verify-payment', [OrderController::class, 'verifyPayment']);



Route::middleware('auth:sanctum')->group(function () {
    Route::post('/registerAdmin', [RegisterController::class, 'register'])->middleware('admin');
    Route::post('/logout', [LogoutController::class, 'logout']);
    Route::get('/user', fn(Request $request) => $request->user());

    Route::put('/profile', [ProfileController::class, 'update']);
    Route::delete('/profile/destroy', [ProfileController::class, 'destroy']);
    Route::get('/profile/addresses', [ProfileController::class, 'getAddresses']);
    Route::post('/profile/addresses', [ProfileController::class, 'createAddress']);
    Route::put('/profile/addresses/{address}', [ProfileController::class, 'updateAddress']);
    Route::delete('/profile/addresses/{address}', [ProfileController::class, 'deleteAddress']);

    // Merge guest cart on login (protected)
    Route::post('/cart/merge', [CartController::class, 'merge']);

    // Orders
    Route::post('/checkout', [OrderController::class, 'checkout']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);

    // Your protected routes here
    Route::middleware('admin')->group(function () {
        Route::post('/products-categories', [ProductsCategoryController::class, 'store']);
        Route::put('/products-categories/{category}', [ProductsCategoryController::class, 'update']);
        Route::delete('/products-categories/{category}', [ProductsCategoryController::class, 'destroy']);

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
        Route::put('/users/{user}', [UsersController::class, 'update']);

        Route::delete('/users/{user}', [UsersController::class, 'destroy']);
    });
});
