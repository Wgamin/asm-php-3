<?php

use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\RealtimeController as AdminRealtimeController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AiChatbotController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderWebhookController;
use App\Http\Controllers\ProductController as PublicProductController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Http;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/san-pham', [PublicProductController::class, 'index'])->name('products.index');
Route::get('/san-pham/{id}', [PublicProductController::class, 'show'])->name('product.detail');
Route::prefix('so-sanh')->as('compare.')->group(function () {
    Route::get('/', [CompareController::class, 'index'])->name('index');
    Route::post('/{product}', [CompareController::class, 'add'])->name('add');
    Route::delete('/{product}', [CompareController::class, 'remove'])->name('remove');
    Route::delete('/', [CompareController::class, 'clear'])->name('clear');
});
Route::get('/tin-tuc', [NewsController::class, 'index'])->name('news.index');
Route::get('/tin-tuc/{slug}', [NewsController::class, 'show'])->name('news.show');
Route::get('/lien-he', function () {
    return view('contact');
})->name('contact');
Route::get('/gioi-thieu', function () {
    return view('about');
})->name('about');
Route::get('/order-success', [OrderController::class, 'success'])->name('order.success');
Route::post('/webhooks/orders/{order}', [OrderWebhookController::class, 'update'])->name('webhooks.orders.update');
Route::get('/payment/momo-return', [PaymentController::class, 'momoReturn'])->name('payment.momoReturn');
Route::post('/payment/momo/ipn', [PaymentController::class, 'momoIpn'])->name('payment.momoIpn');
Route::get('/payment/zalopay-return', [PaymentController::class, 'zalopayReturn'])->name('payment.zalopayReturn');
Route::post('/payment/zalopay/callback', [PaymentController::class, 'zalopayCallback'])->name('payment.zalopayCallback');
Route::prefix('ai-chat')->as('ai-chat.')->group(function () {
    Route::get('/messages', [AiChatbotController::class, 'messages'])->name('messages');
    Route::post('/messages', [AiChatbotController::class, 'send'])->name('send');
    Route::delete('/messages', [AiChatbotController::class, 'clear'])->name('clear');
});

Route::prefix('cart')->as('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add/{id}', [CartController::class, 'add'])->name('add');
    Route::get('/update/{id}/{quantity}', [CartController::class, 'updateQuantity'])->name('update_quantity');
    Route::get('/remove/{id}', [CartController::class, 'removeItem'])->name('remove_item');
    Route::get('/clear', [CartController::class, 'clear'])->name('clear');
});

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetOtp'])->name('password.email');
    Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/support/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/support/chat/messages', [ChatController::class, 'messages'])->name('chat.messages');
    Route::post('/support/chat/messages', [ChatController::class, 'send'])->name('chat.send');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/san-pham/{product}/reviews', [ProductReviewController::class, 'store'])->name('products.reviews.store');
    Route::post('/profile/orders/review', [ProductReviewController::class, 'storeFromOrder'])->name('profile.orders.review');
    Route::get('/profile/orders/{order}', [ProfileController::class, 'showOrder'])->name('profile.orders.show');
    Route::patch('/profile/orders/{order}/cancel', [ProfileController::class, 'cancelOrder'])->name('profile.orders.cancel');
    Route::post('/profile/orders/{order}/buy-again', [ProfileController::class, 'buyAgain'])->name('profile.orders.buyAgain');
    Route::post('/profile/addresses', [ProfileController::class, 'storeAddress'])->name('profile.addresses.store');
    Route::put('/profile/addresses/{address}', [ProfileController::class, 'updateAddress'])->name('profile.addresses.update');
    Route::delete('/profile/addresses/{address}', [ProfileController::class, 'destroyAddress'])->name('profile.addresses.destroy');
    Route::patch('/profile/addresses/{address}/default', [ProfileController::class, 'setDefaultAddress'])->name('profile.addresses.default');

    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::get('/checkout/shipping-options', [OrderController::class, 'shippingOptions'])->name('checkout.shipping.options');
    Route::post('/checkout', [OrderController::class, 'store'])->name('order.store');
    Route::post('/checkout/coupon', [CouponController::class, 'apply'])->name('checkout.coupon.apply');
    Route::delete('/checkout/coupon', [CouponController::class, 'remove'])->name('checkout.coupon.remove');
    Route::get('/order-success', [OrderController::class, 'success'])->name('order.success');

    // --- MỚI BỔ SUNG: CHỨC NĂNG THANH TOÁN VNPAY ---
    Route::post('/payment/vnpay', [PaymentController::class, 'createPayment'])->name('payment.vnpay');
    Route::get('/payment/vnpay-return', [PaymentController::class, 'vnpayReturn'])->name('payment.vnpayReturn');
    Route::post('/payment/momo', [PaymentController::class, 'createMomoPayment'])->name('payment.momo');
    Route::post('/payment/zalopay', [PaymentController::class, 'createZalopayPayment'])->name('payment.zalopay');

    // --- Wishlist ---
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle/{id}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    Route::prefix('attributes')->name('admin.attributes.')->group(function () {
        Route::get('/', [AttributeController::class, 'index'])->name('index');
        Route::post('/', [AttributeController::class, 'store'])->name('store');
        Route::put('/{id}', [AttributeController::class, 'update'])->name('update');
        Route::delete('/{id}', [AttributeController::class, 'destroy'])->name('destroy');

        Route::post('/{attributeId}/values', [AttributeController::class, 'storeValue'])->name('storeValue');
        Route::put('/values/{id}', [AttributeController::class, 'updateValue'])->name('updateValue');
        Route::delete('/values/{id}', [AttributeController::class, 'destroyValue'])->name('destroyValue');
    });
});

Route::middleware(['auth', 'admin'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('/realtime/orders', [AdminRealtimeController::class, 'orders'])->name('realtime.orders');
    Route::get('/chat', [AdminChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/conversations', [AdminChatController::class, 'conversations'])->name('chat.conversations');
    Route::get('/chat/{user}/messages', [AdminChatController::class, 'messages'])->name('chat.messages');
    Route::post('/chat/{user}/messages', [AdminChatController::class, 'send'])->name('chat.send');

    Route::controller(AdminOrderController::class)->group(function () {
        Route::get('/orders', 'index')->name('orders.index');
        Route::get('/orders/{id}', 'show')->name('orders.show');
        Route::post('/orders/{id}/status', 'updateStatus')->name('orders.updateStatus');
        Route::delete('/orders/{id}', 'destroy')->name('orders.destroy');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('coupons', AdminCouponController::class)->except(['show']);
    Route::resource('news', AdminNewsController::class)->except(['show']);
    Route::resource('users', UserController::class);
    Route::get('/roles', [UserController::class, 'index'])->name('roles.index');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
});

Route::prefix('admin')->as('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showAdminLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'adminLogin']);
        Route::get('/register', [AuthController::class, 'showAdminRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'adminRegister']);
    });
});
