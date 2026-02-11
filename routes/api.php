<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\ProgressController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\MealPlanController;
use App\Http\Controllers\Api\PaymentController;

// Authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protect routes with Sanctum for 'app_users'
Route::middleware(['auth:sanctum'])->group(function () {
    // Auth & Profile
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // Products
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);

    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);

    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{itemId}', [CartController::class, 'update']);
    Route::delete('/cart/{itemId}', [CartController::class, 'destroy']);
    Route::delete('/cart', [CartController::class, 'clear']);

    // Progress
    Route::get('/progress', [ProgressController::class, 'index']);
    Route::post('/progress', [ProgressController::class, 'store']);

    // Meal Logs
    Route::get('/meal-logs', [\App\Http\Controllers\Api\MealLogController::class, 'index']);
    Route::get('/meal-logs/history', [\App\Http\Controllers\Api\MealLogController::class, 'history']);
    Route::post('/meal-logs', [\App\Http\Controllers\Api\MealLogController::class, 'store']);
    Route::delete('/meal-logs/{id}', [\App\Http\Controllers\Api\MealLogController::class, 'destroy']);

    // Meal Plan
    Route::get('/meal-plan/suggest', [MealPlanController::class, 'suggest']);

    // Payment
    Route::post('/payment/initiate', [PaymentController::class, 'initiate']);
    Route::post('/payment/verify', [PaymentController::class, 'verify']);

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist', [WishlistController::class, 'store']);
    Route::get('/wishlist/check/{productId}', [WishlistController::class, 'check']);
    Route::delete('/wishlist/{productId}', [WishlistController::class, 'destroy']);
    Route::delete('/wishlist', [WishlistController::class, 'clear']);
    // Health Profile
    Route::get('/health-profile', [\App\Http\Controllers\Api\HealthProfileController::class, 'show']);
    Route::post('/health-profile', [\App\Http\Controllers\Api\HealthProfileController::class, 'update']);

    // Address
    Route::get('/addresses', [\App\Http\Controllers\Api\AddressController::class, 'index']);
    Route::post('/addresses', [\App\Http\Controllers\Api\AddressController::class, 'store']);
    Route::put('/addresses/{address}', [\App\Http\Controllers\Api\AddressController::class, 'update']);
    Route::delete('/addresses/{address}', [\App\Http\Controllers\Api\AddressController::class, 'destroy']);
});
