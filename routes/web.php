<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\AccountController;
use App\Http\Controllers\Shop\HomeController;

Route::redirect('/', '/shop');

// SEO
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index']);
Route::get('/robots.txt', [\App\Http\Controllers\SitemapController::class, 'robots']);

// ============ CUSTOMER SHOP ============
Route::prefix('shop')->name('shop.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/products', [ProductController::class, 'index'])->name('products');
    Route::get('/products/{slug}', [ProductController::class, 'show'])->name('product.show');
    Route::post('/products/{product}/review', [ProductController::class, 'storeReview'])->name('product.review');

    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/buy-now/{product}', [CartController::class, 'buyNow'])->name('cart.buy-now');
    Route::patch('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

    Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon');
    Route::delete('/cart/coupon', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/order-success/{orderNumber}', [CheckoutController::class, 'success'])->name('order.success');

    Route::middleware('auth')->group(function () {
        Route::get('/my-account', [AccountController::class, 'index'])->name('my-account');
        Route::put('/my-account/profile', [AccountController::class, 'updateProfile'])->name('profile.update');
        Route::put('/my-account/password', [AccountController::class, 'updatePassword'])->name('password.update');

        Route::post('/my-account/address', [AccountController::class, 'storeAddress'])->name('address.store');
        Route::put('/my-account/address/{address}', [AccountController::class, 'updateAddress'])->name('address.update');
        Route::delete('/my-account/address/{address}', [AccountController::class, 'deleteAddress'])->name('address.delete');

        Route::get('/my-account/order/{orderNumber}', [AccountController::class, 'orderDetail'])->name('order.detail');
        Route::patch('/my-account/order/{order}/cancel', [AccountController::class, 'cancelOrder'])->name('order.cancel');
        Route::get('/my-account/order/{orderNumber}/invoice', [AccountController::class, 'invoice'])->name('order.invoice');
    });

    Route::get('/contact', [\App\Http\Controllers\Shop\ContactController::class, 'index'])->name('contact');
    Route::post('/contact', [\App\Http\Controllers\Shop\ContactController::class, 'store'])->name('contact.store');

    Route::get('/page/{slug}', [\App\Http\Controllers\Shop\PageController::class, 'show'])->name('page');

    Route::post('/newsletter/subscribe', [\App\Http\Controllers\Shop\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
    Route::get('/newsletter/unsubscribe/{token}', [\App\Http\Controllers\Shop\NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
});

// ============ ADMIN AUTH (public) ============
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});

// ============ ADMIN PROTECTED ============
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Categories & Brands
    Route::resource('categories', CategoryController::class);
    Route::resource('brands', BrandController::class);

    // Products
    Route::post('products/generate-variants', [AdminProductController::class, 'generateVariants'])->name('products.generate-variants');
    Route::delete('products/image/{image}', [AdminProductController::class, 'deleteImage'])->name('products.image.delete');
    Route::patch('products/image/{image}/primary', [AdminProductController::class, 'setPrimary'])->name('products.image.primary');
    Route::resource('products', AdminProductController::class);

    // Inventory
    Route::get('inventory', [\App\Http\Controllers\Admin\InventoryController::class, 'index'])->name('inventory.index');
    Route::get('inventory/history', [\App\Http\Controllers\Admin\InventoryController::class, 'history'])->name('inventory.history');
    Route::get('inventory/{product}/adjust', [\App\Http\Controllers\Admin\InventoryController::class, 'adjustForm'])->name('inventory.adjust-form');
    Route::post('inventory/{product}/adjust', [\App\Http\Controllers\Admin\InventoryController::class, 'adjust'])->name('inventory.adjust');

    // Orders
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    Route::patch('orders/{order}/payment', [OrderController::class, 'updatePayment'])->name('orders.payment');
    Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('orders/{order}/packing-slip', [OrderController::class, 'packingSlip'])->name('orders.packing-slip');

    // Coupons
    Route::resource('coupons', \App\Http\Controllers\Admin\CouponController::class);

    // Settings
    Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');

    // Banners
    Route::resource('banners', \App\Http\Controllers\Admin\BannerController::class);
    Route::patch('banners/{banner}/toggle', [\App\Http\Controllers\Admin\BannerController::class, 'toggle'])->name('banners.toggle');

    // Reviews
    Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::patch('reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
    Route::patch('reviews/{review}/reject', [ReviewController::class, 'reject'])->name('reviews.reject');
    Route::post('reviews/{review}/reply', [ReviewController::class, 'reply'])->name('reviews.reply');
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Customers
    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::patch('customers/{customer}/block', [CustomerController::class, 'toggleBlock'])->name('customers.block');

    // Newsletter
    Route::get('newsletter', [\App\Http\Controllers\Admin\NewsletterController::class, 'index'])->name('newsletter.index');
    Route::get('newsletter/export', [\App\Http\Controllers\Admin\NewsletterController::class, 'export'])->name('newsletter.export');
    Route::delete('newsletter/{subscriber}', [\App\Http\Controllers\Admin\NewsletterController::class, 'destroy'])->name('newsletter.destroy');

    // Contact Messages
    Route::get('contact-messages', [\App\Http\Controllers\Admin\ContactMessageController::class, 'index'])->name('contact-messages.index');
    Route::get('contact-messages/{contactMessage}', [\App\Http\Controllers\Admin\ContactMessageController::class, 'show'])->name('contact-messages.show');
    Route::delete('contact-messages/{contactMessage}', [\App\Http\Controllers\Admin\ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');

    // CMS Pages
    Route::resource('pages', \App\Http\Controllers\Admin\PageController::class);
    Route::patch('pages/{page}/toggle', [\App\Http\Controllers\Admin\PageController::class, 'toggle'])->name('pages.toggle');

    // Payment Methods
    Route::resource('payment-methods', \App\Http\Controllers\Admin\PaymentMethodController::class);
    Route::patch('payment-methods/{paymentMethod}/toggle', [\App\Http\Controllers\Admin\PaymentMethodController::class, 'toggle'])->name('payment-methods.toggle');
    Route::delete('payment-method-settings/{setting}', [\App\Http\Controllers\Admin\PaymentMethodController::class, 'deleteSetting'])->name('payment-methods.setting.delete');

    // Attributes
    Route::resource('attributes', \App\Http\Controllers\Admin\AttributeController::class);
    Route::patch('attributes/{attribute}/toggle', [\App\Http\Controllers\Admin\AttributeController::class, 'toggle'])->name('attributes.toggle');
    Route::delete('attribute-values/{value}', [\App\Http\Controllers\Admin\AttributeController::class, 'deleteValue'])->name('attributes.value.delete');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('sales', [\App\Http\Controllers\Admin\ReportController::class, 'sales'])->name('sales');
        Route::get('sales/csv', [\App\Http\Controllers\Admin\ReportController::class, 'salesCsv'])->name('sales.csv');
        Route::get('products', [\App\Http\Controllers\Admin\ReportController::class, 'products'])->name('products');
        Route::get('customers', [\App\Http\Controllers\Admin\ReportController::class, 'customers'])->name('customers');
        Route::get('payments', [\App\Http\Controllers\Admin\ReportController::class, 'payments'])->name('payments');
    });
});

require __DIR__.'/auth.php';