<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Public;
use App\Http\Controllers\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [Public\HomeController::class, 'index'])->name('home');
Route::get('/about', [Public\AboutController::class, 'index'])->name('about');
Route::get('/contact', [Public\ContactController::class, 'index'])->name('contact');
Route::post('/contact', [Public\ContactController::class, 'submit'])->name('contact.submit');

// Products
Route::get('/products', [Public\ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [Public\ProductController::class, 'show'])->name('products.show');
Route::get('/api/products/search', [Public\ProductController::class, 'search'])->name('products.search');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [User\DashboardController::class, 'index'])->name('dashboard');
    Route::put('/dashboard/profile', [User\DashboardController::class, 'updateProfile'])->name('user.profile.update');
    Route::get('/dashboard/orders', [User\DashboardController::class, 'orders'])->name('user.orders.index');
    Route::get('/dashboard/orders/{order}', [User\DashboardController::class, 'showOrder'])->name('user.orders.show');
    Route::get('/dashboard/favorites', [User\DashboardController::class, 'favorites'])->name('user.favorites.index');
    Route::get('/dashboard/addresses', [User\DashboardController::class, 'addresses'])->name('user.addresses.index');
    Route::get('/dashboard/reviews', [User\DashboardController::class, 'reviews'])->name('user.reviews.index');

    // Addresses
    Route::post('/addresses', [User\AddressController::class, 'store'])->name('addresses.store');
    Route::put('/addresses/{address}', [User\AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [User\AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::post('/addresses/{address}/default', [User\AddressController::class, 'setDefault'])->name('addresses.default');

    // Cart (JSON API)
    Route::get('/cart', [User\CartController::class, 'index'])->name('cart.index');
    Route::get('/api/cart', [User\CartController::class, 'getCart'])->name('cart.get');
    Route::post('/api/cart/add', [User\CartController::class, 'add'])->name('cart.add');
    Route::put('/api/cart/{productId}', [User\CartController::class, 'update'])->name('cart.update');
    Route::delete('/api/cart/{productId}', [User\CartController::class, 'remove'])->name('cart.remove');

    // Checkout
    Route::get('/checkout', [User\CheckoutController::class, 'index'])->name('checkout.index');

    // Payment Routes
    Route::post('/checkout/cod', [User\PaymentController::class, 'placeCodOrder'])->name('payment.cod');
    Route::post('/payment/razorpay/create-order', [User\PaymentController::class, 'createRazorpayOrder'])->name('payment.razorpay.create');
    Route::post('/payment/razorpay/verify', [User\PaymentController::class, 'verifyAndPlaceOrder'])->name('payment.razorpay.verify');

    // Favorites
    Route::post('/favorites/toggle', [User\FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // Reviews
    Route::post('/reviews', [User\ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [User\ReviewController::class, 'destroy'])->name('reviews.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Products
    Route::resource('products', Admin\ProductController::class);
    Route::delete('/products/images/{image}', [Admin\ProductController::class, 'deleteImage'])->name('products.images.destroy');

    // Categories
    Route::resource('categories', Admin\CategoryController::class);

    // Orders
    Route::get('/orders', [Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/status', [Admin\OrderController::class, 'updateStatus'])->name('orders.status');

    // Customers
    Route::get('/customers', [Admin\CustomerController::class, 'index'])->name('customers.index');
    Route::post('/customers/{user}/toggle', [Admin\CustomerController::class, 'toggleStatus'])->name('customers.toggle');

    // Reviews
    Route::get('/reviews', [Admin\ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{review}/approve', [Admin\ReviewController::class, 'approve'])->name('reviews.approve');
    Route::delete('/reviews/{review}', [Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Settings
    Route::get('/settings', [Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [Admin\SettingsController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
