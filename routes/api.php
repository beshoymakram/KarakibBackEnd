<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\WasteItemController;
use App\Http\Controllers\Api\WasteTypeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

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
        return response()->json(['message' => 'migrated successfully']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// Public routes - Waste Types
Route::get('/waste-types', [WasteTypeController::class, 'index']);
Route::get('/waste-types/{id}', [WasteTypeController::class, 'show']);

// Public routes - Waste Items
Route::get('/waste-items', [WasteItemController::class, 'index']);
Route::get('/waste-items/{id}', [WasteItemController::class, 'show']);

// Temporary public routes for CRUD (will protect with admin middleware later)
Route::post('/waste-types', [WasteTypeController::class, 'store']);
Route::put('/waste-types/{id}', [WasteTypeController::class, 'update']);
Route::delete('/waste-types/{id}', [WasteTypeController::class, 'destroy']);

Route::post('/waste-items', [WasteItemController::class, 'store']);
Route::put('/waste-items/{id}', [WasteItemController::class, 'update']);
Route::delete('/waste-items/{id}', [WasteItemController::class, 'destroy']);

Route::get('/users', [UsersController::class, 'index']);
Route::put('/users/{user}', [UsersController::class, 'update']);
Route::delete('/users/{user}', [UsersController::class, 'destroy']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/registerAdmin', [RegisterController::class, 'register'])->middleware('admin');
    Route::post('/logout', [LogoutController::class, 'logout']);
    Route::get('/user', fn(Request $request) => $request->user());

    // Your protected routes here
});
