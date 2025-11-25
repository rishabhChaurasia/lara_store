<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController; // Import AdminController
use App\Http\Controllers\Shop\ShopController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\UserController;
use App\Http\Controllers\Shop\CheckoutController;

Route::get('/', [ShopController::class, 'index'])->name('home');

// Public shop routes
Route::get('/shop', [ShopController::class, 'shop'])->name('shop.index');
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');

// Cart routes (requires auth)
Route::middleware('auth')->group(function () {
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
    Route::post('/cart/remove-coupon', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');
});

// Wishlist routes (requires auth)
Route::middleware('auth')->group(function () {
    Route::post('/wishlist/{product}', [ProductController::class, 'toggleWishlist'])->name('wishlist.toggle');
    Route::delete('/wishlist/{product}', [UserController::class, 'removeFromWishlist'])->name('wishlist.remove');
});

// Checkout routes
Route::middleware('auth')->prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::get('/shipping', [CheckoutController::class, 'shipping'])->name('shipping');
    Route::post('/shipping', [CheckoutController::class, 'storeShipping'])->name('store.shipping');
    Route::get('/payment', [CheckoutController::class, 'payment'])->name('payment');
    Route::post('/payment', [CheckoutController::class, 'processPayment'])->name('process.payment');
    Route::post('/create-payment-intent', [CheckoutController::class, 'createPaymentIntent'])->name('create-payment-intent');
    Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');
});

// User account routes (requires auth)
Route::middleware('auth')->prefix('account')->name('account.')->group(function () {
    Route::get('/', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [UserController::class, 'orderHistory'])->name('orders');
    Route::get('/orders/{order}', [UserController::class, 'orderDetails'])->name('order.details');
    Route::get('/wishlist', [UserController::class, 'wishlist'])->name('wishlist');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::put('/users/{user}/toggle-status', [App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggleStatus');
    Route::resource('orders', App\Http\Controllers\Admin\OrderController::class);
    Route::put('/orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');

    // Marketing & Analytics routes
    Route::prefix('marketing')->name('admin.marketing.')->group(function () {
        Route::prefix('coupons')->name('coupons.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\MarketingController::class, 'couponsIndex'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\MarketingController::class, 'couponsCreate'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\MarketingController::class, 'couponsStore'])->name('store');
            Route::get('/{coupon}/edit', [App\Http\Controllers\Admin\MarketingController::class, 'couponsEdit'])->name('edit');
            Route::put('/{coupon}', [App\Http\Controllers\Admin\MarketingController::class, 'couponsUpdate'])->name('update');
            Route::delete('/{coupon}', [App\Http\Controllers\Admin\MarketingController::class, 'couponsDestroy'])->name('destroy');
        });

        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\MarketingController::class, 'reportsIndex'])->name('index');
            Route::get('/stock', [App\Http\Controllers\Admin\MarketingController::class, 'stockReport'])->name('stock');
            Route::get('/sales-data', [App\Http\Controllers\Admin\MarketingController::class, 'salesReportData'])->name('salesData');
            Route::get('/conversion-rate', [App\Http\Controllers\Admin\MarketingController::class, 'conversionRate'])->name('conversionRate');
            Route::get('/stock-data', [App\Http\Controllers\Admin\MarketingController::class, 'stockReportData'])->name('stockData');
        });

        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\MarketingController::class, 'inventoryIndex'])->name('index');
            Route::post('/bulk-update-stock', [App\Http\Controllers\Admin\MarketingController::class, 'bulkUpdateStock'])->name('bulkUpdateStock');
            Route::post('/bulk-update-status', [App\Http\Controllers\Admin\MarketingController::class, 'bulkUpdateStatus'])->name('bulkUpdateStatus');
            Route::get('/export', [App\Http\Controllers\Admin\MarketingController::class, 'exportProducts'])->name('export');
            Route::post('/import', [App\Http\Controllers\Admin\MarketingController::class, 'importProducts'])->name('import');
        });
    });

    Route::prefix('notifications')->name('admin.notifications.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('index');
        Route::get('/unread', [App\Http\Controllers\Admin\NotificationController::class, 'unread'])->name('unread');
        Route::put('/{id}/read', [App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('markAsRead');
        Route::put('/mark-all-read', [App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('markAllAsRead');
        Route::delete('/{id}', [App\Http\Controllers\Admin\NotificationController::class, 'delete'])->name('delete');
    });

    Route::prefix('reviews')->name('admin.reviews.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('index');
        Route::get('/pending', [App\Http\Controllers\Admin\ReviewController::class, 'pending'])->name('pending');
        Route::get('/approved', [App\Http\Controllers\Admin\ReviewController::class, 'approved'])->name('approved');
        Route::put('/{id}/approve', [App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('approve');
        Route::put('/{id}/toggle-approval', [App\Http\Controllers\Admin\ReviewController::class, 'toggleApproval'])->name('toggleApproval');
        Route::delete('/{id}', [App\Http\Controllers\Admin\ReviewController::class, 'reject'])->name('reject');
    });
});

require __DIR__ . '/auth.php';