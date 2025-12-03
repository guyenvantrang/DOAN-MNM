<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// Import đầy đủ các Controller
use App\Http\Controllers\Api\AuthCustomerController;
use App\Http\Controllers\Api\ProductCustomerController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderCustomerController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (Không cần đăng nhập)
|--------------------------------------------------------------------------
*/

// Auth
Route::post('/register', [AuthCustomerController::class, 'register']);
Route::post('/login', [AuthCustomerController::class, 'login']);

// Sản phẩm & Danh mục
Route::get('/products', [ProductCustomerController::class, 'index']);      // -> Gọi: /api/products
Route::get('/products/{id}', [ProductCustomerController::class, 'show']);  // -> Gọi: /api/products/SP01
Route::get('/products/{id}/related', [ProductCustomerController::class, 'related']);
Route::get('/filters', [ProductCustomerController::class, 'filters']);     // -> Gọi: /api/filters

/*
|--------------------------------------------------------------------------
| PRIVATE ROUTES (Cần Token đăng nhập)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // User
    Route::get('/user', [AuthCustomerController::class, 'profile']);
    Route::post('/logout', [AuthCustomerController::class, 'logout']);

    // Giỏ hàng
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::put('/cart/update', [CartController::class, 'update']);
    Route::delete('/cart/remove', [CartController::class, 'remove']);

    // Đặt hàng
    Route::post('/checkout/preview', [OrderCustomerController::class, 'preview']);
    Route::post('/checkout/place-order', [OrderCustomerController::class, 'placeOrder']);
    Route::get('/orders', [OrderCustomerController::class, 'history']);
    Route::get('/orders/{id}', [OrderCustomerController::class, 'detail']);
    Route::post('/orders/{id}/cancel', [OrderCustomerController::class, 'cancel']);

    // Đánh giá
    Route::post('/products/{id}/review', [ProductCustomerController::class, 'review']);
});


// --- PUBLIC ---
Route::post('/register', [AuthCustomerController::class, 'register']);
Route::post('/login', [AuthCustomerController::class, 'login']);
Route::get('/products', [ProductCustomerController::class, 'index']);
Route::get('/products/{id}', [ProductCustomerController::class, 'show']);
Route::get('/products/{id}/related', [ProductCustomerController::class, 'related']);
Route::get('/filters', [ProductCustomerController::class, 'filters']);

// --- PRIVATE (Cần đăng nhập) ---
Route::middleware('auth:sanctum')->group(function () {
    // Profile
    Route::get('/user', [AuthCustomerController::class, 'profile']);
    Route::put('/user/update', [AuthCustomerController::class, 'updateProfile']); // Mới
    Route::put('/user/change-password', [AuthCustomerController::class, 'changePassword']); // Mới
    Route::post('/logout', [AuthCustomerController::class, 'logout']);

    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::put('/cart/update', [CartController::class, 'update']);
    Route::delete('/cart/remove', [CartController::class, 'remove']);

    // Order
    Route::post('/checkout/preview', [OrderCustomerController::class, 'preview']);
    Route::post('/checkout/place-order', [OrderCustomerController::class, 'placeOrder']);
    Route::get('/orders', [OrderCustomerController::class, 'history']);
    Route::get('/orders/{id}', [OrderCustomerController::class, 'detail']);
    Route::post('/orders/{id}/cancel', [OrderCustomerController::class, 'cancel']);

    // Review
    Route::post('/products/{id}/review', [ProductCustomerController::class, 'review']);

    
});