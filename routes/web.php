<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController; // Import AdminController
use App\Http\Controllers\Shop\ShopController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\UserController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PoliciesController;
use App\Http\Controllers\Admin\AbandonedCartController;
use App\Http\Controllers\Admin\AbandonedCartSettingsController;

Route::get('/', [ShopController::class, 'index'])->name('home');

// Public shop routes
Route::get('/shop', [ShopController::class, 'shop'])->name('shop.index');
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');

// FAQ page
Route::get('/faq', [FaqController::class, 'index'])->name('faq');

// Contact page
Route::get('/contact-us', [ContactController::class, 'index'])->name('contact');
Route::post('/contact-us', [ContactController::class, 'send'])->name('contact.send');
Route::get('/contact-us/thank-you', [ContactController::class, 'thankYou'])->name('contact.thank-you');

// Policies pages
Route::get('/policies/{policy}', [PoliciesController::class, 'show'])->name('policies.show');

// Cart routes (requires auth and verified email)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
    Route::post('/cart/remove-coupon', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');
});

// Wishlist routes (requires auth and verified email)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/wishlist/{product}', [ProductController::class, 'toggleWishlist'])->name('wishlist.toggle');
    Route::delete('/wishlist/{product}', [UserController::class, 'removeFromWishlist'])->name('wishlist.remove');
});

// Checkout routes (requires auth and verified email)
Route::middleware(['auth', 'verified'])->prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::get('/shipping', [CheckoutController::class, 'shipping'])->name('shipping');
    Route::post('/shipping', [CheckoutController::class, 'storeShipping'])->name('store.shipping');
    Route::get('/payment', [CheckoutController::class, 'payment'])->name('payment');
    Route::post('/payment', [CheckoutController::class, 'processPayment'])->name('process.payment');
    Route::post('/create-payment-intent', [CheckoutController::class, 'createPaymentIntent'])->name('create-payment-intent');
    Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');
});

// User account routes (requires auth and verified email)
Route::middleware(['auth', 'verified'])->prefix('account')->name('account.')->group(function () {
    Route::get('/', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [UserController::class, 'orderHistory'])->name('orders');
    Route::get('/orders/{order}', [UserController::class, 'orderDetails'])->name('order.details');
    Route::get('/wishlist', [UserController::class, 'wishlist'])->name('wishlist');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth', 'permission:manage.admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // Product management routes
    Route::middleware('permission:products.view')->group(function () {
        Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
    });

    Route::middleware('permission:categories.view')->group(function () {
        Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
    });

    Route::middleware('permission:users.view')->group(function () {
        Route::resource('users', App\Http\Controllers\Admin\UserController::class);
        Route::put('/users/{user}/toggle-status', [App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggleStatus');
    });

    Route::middleware('permission:orders.view')->group(function () {
        Route::resource('orders', App\Http\Controllers\Admin\OrderController::class);
        Route::put('/orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
    });

    // Marketing & Analytics routes
    Route::prefix('marketing')->name('admin.marketing.')->group(function () {
        // Coupon management
        Route::middleware('permission:coupons.view')->prefix('coupons')->name('coupons.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\MarketingController::class, 'couponsIndex'])->name('index');
            Route::middleware('permission:coupons.create')->group(function () {
                Route::get('/create', [App\Http\Controllers\Admin\MarketingController::class, 'couponsCreate'])->name('create');
                Route::post('/', [App\Http\Controllers\Admin\MarketingController::class, 'couponsStore'])->name('store');
            });
            Route::middleware('permission:coupons.update')->group(function () {
                Route::get('/{coupon}/edit', [App\Http\Controllers\Admin\MarketingController::class, 'couponsEdit'])->name('edit');
                Route::put('/{coupon}', [App\Http\Controllers\Admin\MarketingController::class, 'couponsUpdate'])->name('update');
            });
            Route::middleware('permission:coupons.delete')->group(function () {
                Route::delete('/{coupon}', [App\Http\Controllers\Admin\MarketingController::class, 'couponsDestroy'])->name('destroy');
            });
        });

        // Reports
        Route::middleware('permission:reports.view')->prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\MarketingController::class, 'reportsIndex'])->name('index');
            Route::get('/stock', [App\Http\Controllers\Admin\MarketingController::class, 'stockReport'])->name('stock');
            Route::get('/sales-data', [App\Http\Controllers\Admin\MarketingController::class, 'salesReportData'])->name('salesData');
            Route::get('/conversion-rate', [App\Http\Controllers\Admin\MarketingController::class, 'conversionRate'])->name('conversionRate');
            Route::get('/stock-data', [App\Http\Controllers\Admin\MarketingController::class, 'stockReportData'])->name('stockData');
        });

        // Abandoned carts
        Route::middleware('permission:abandoned-carts.view')->prefix('abandoned-carts')->name('abandoned-carts.')->group(function () {
            Route::get('/', [AbandonedCartController::class, 'index'])->name('index');
            Route::prefix('settings')->name('settings.')->group(function () {
                Route::get('/', [AbandonedCartSettingsController::class, 'index'])->name('index');
                Route::put('/', [AbandonedCartSettingsController::class, 'update'])->name('update');
            });
        });

        // Inventory management
        Route::middleware('permission:inventory.view')->prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\MarketingController::class, 'inventoryIndex'])->name('index');
            Route::post('/bulk-update-stock', [App\Http\Controllers\Admin\MarketingController::class, 'bulkUpdateStock'])->name('bulkUpdateStock');
            Route::post('/bulk-update-status', [App\Http\Controllers\Admin\MarketingController::class, 'bulkUpdateStatus'])->name('bulkUpdateStatus');
            Route::get('/export', [App\Http\Controllers\Admin\MarketingController::class, 'exportProducts'])->name('export');
            Route::post('/import', [App\Http\Controllers\Admin\MarketingController::class, 'importProducts'])->name('import');
        });
    });

    // Notifications
    Route::middleware('permission:notifications.view')->prefix('notifications')->name('admin.notifications.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('index');
        Route::get('/unread', [App\Http\Controllers\Admin\NotificationController::class, 'unread'])->name('unread');
        Route::put('/{id}/read', [App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('markAsRead');
        Route::put('/mark-all-read', [App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('markAllAsRead');
        Route::delete('/{id}', [App\Http\Controllers\Admin\NotificationController::class, 'delete'])->name('delete');
    });

    // Reviews
    Route::middleware('permission:reviews.view')->prefix('reviews')->name('admin.reviews.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('index');
        Route::get('/pending', [App\Http\Controllers\Admin\ReviewController::class, 'pending'])->name('pending');
        Route::get('/approved', [App\Http\Controllers\Admin\ReviewController::class, 'approved'])->name('approved');
        Route::put('/{id}/approve', [App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('approve');
        Route::put('/{id}/toggle-approval', [App\Http\Controllers\Admin\ReviewController::class, 'toggleApproval'])->name('toggleApproval');
        Route::delete('/{id}', [App\Http\Controllers\Admin\ReviewController::class, 'reject'])->name('reject');
    });
});

require __DIR__ . '/auth.php';