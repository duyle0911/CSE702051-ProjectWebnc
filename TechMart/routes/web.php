<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\StripeCheckoutController;
use App\Http\Controllers\VNPayController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/


Route::get('/vnpay-return', [VNPayController::class, 'returnPayment'])->name('vnpay.return');


// Stripe
Route::get('/checkout', [StripeCheckoutController::class, 'checkout'])->name('checkout');
Route::get('/checkout/success', [StripeCheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel', [StripeCheckoutController::class, 'cancel'])->name('checkout.cancel');
//Route::get('/stripe', [StripeController::class, 'checkout'])->name('stripe.checkout');
//Route::get('/stripe/success', [StripeController::class, 'success'])->name('stripe.success');
//Route::get('/stripe/cancel', [StripeController::class, 'cancel'])->name('stripe.cancel');
//Route::post('/stripe/session', [StripeController::class, 'session'])->name('stripe.session');
//Momo
Route::get('/checkout/momo/return', [CheckoutController::class, 'momoReturn'])->name('checkout.momo.return');
// web.php
Route::get('/checkout/momo/{order}', [CheckoutController::class, 'redirectToMomo'])->name('checkout.momo');
//VNpay
Route::get('/checkout/vnpay/{orderId}', [CheckoutController::class, 'vnpay'])->name('checkout.vnpay');
Route::get('/checkout/success/{orderId}', [CheckoutController::class, 'success'])->name('checkout.success');
//Route::get('/vnpay-pay', [VNPayController::class, 'createPayment'])->name('vnpay.pay');
//Route::get('/vnpay-return', [VNPayController::class, 'returnPayment'])->name('vnpay.return');
// callback sau thanh toán
//Route::get('/payment/momo/callback', [PaymentController::class, 'handleMomoReturn'])->name('payment.momo.callback');
//Route::post('/payment/momo/notify', [PaymentController::class, 'handleMomoNotify'])->name('payment.momo.notify');
// Home route
Route::get('/', [HomeController::class, 'index'])->name('home');

// Search route
Route::get('/search', [HomeController::class, 'search'])->name('search');

// Product routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/categories/{category}', [ProductController::class, 'category'])->name('products.category');

// Cart routes (require authentication)
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store'); // Route này sẽ hoạt động
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');

    Route::patch('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');

    // Thêm route để lấy số lượng cart
    Route::get('/cart/count', [CartController::class, 'getCount'])->name('cart.count');

    // Order routes - Thêm route này
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    // Checkout routes
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Sửa lỗi route success - thay {order} thành {orderId}
    Route::get('/checkout/success/{orderId}', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
    Route::get('/checkout/bank-transfer/{orderId}', [CheckoutController::class, 'bankTransfer'])->name('checkout.bank-transfer');

    // Thêm routes cho các phương thức thanh toán khác
    Route::get('/checkout/momo/{orderId}', [CheckoutController::class, 'momo'])->name('checkout.momo');
    Route::get('/checkout/vnpay/{orderId}', [CheckoutController::class, 'vnpay'])->name('checkout.vnpay');
});

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Products
    Route::resource('products', AdminProductController::class);

    // Categories
    Route::resource('categories', CategoryController::class);

    // Orders
    Route::resource('orders', AdminOrderController::class)->only(['index', 'show', 'destroy']);
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');

    // Users
    Route::resource('users', UserController::class);

    // Revenue Statistics
    Route::get('/revenue', [\App\Http\Controllers\Admin\RevenueController::class, 'index'])->name('revenue.index');
    Route::get('/revenue/export', [\App\Http\Controllers\Admin\RevenueController::class, 'export'])->name('revenue.export');
});

require __DIR__ . '/auth.php';
