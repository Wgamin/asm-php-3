<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController as PublicProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Admin\AttributeController; 

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
Route::get('/gioi-thieu', function() {
    return view('about');
})->name('about');
Route::get('/order-success', [OrderController::class, 'success'])->name('order.success');

// --- CẬP NHẬT: Nhóm Route cho Giỏ hàng (KHÔNG DÙNG AJAX) ---
Route::prefix('cart')->as('cart.')->group(function () {
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

    // --- MỚI BỔ SUNG: ĐĂNG NHẬP BẰNG GOOGLE ---
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');



    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [OrderController::class, 'store'])->name('order.store');
    Route::post('/checkout/coupon', [CouponController::class, 'apply'])->name('checkout.coupon.apply');
    Route::delete('/checkout/coupon', [CouponController::class, 'remove'])->name('checkout.coupon.remove');
    Route::get('/order-success', [OrderController::class, 'success'])->name('order.success');

    // --- Wishlist ---
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle/{id}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // --- QUẢN LÝ THUỘC TÍNH (BỔ SUNG ĐẦY ĐỦ) ---
    // Sử dụng prefix 'attributes' để tránh xung đột URL
    Route::prefix('attributes')->name('admin.attributes.')->group(function () {
        // Thuộc tính chính (Màu sắc, Size...)
        Route::get('/', [AttributeController::class, 'index'])->name('index');         // Danh sách
        Route::post('/', [AttributeController::class, 'store'])->name('store');        // Lưu tên mới
        Route::put('/{id}', [AttributeController::class, 'update'])->name('update');   // Cập nhật tên
        Route::delete('/{id}', [AttributeController::class, 'destroy'])->name('destroy'); // Xóa cả bộ

        // Giá trị thuộc tính con (Đỏ, Xanh, L, XL...)
        Route::post('/{attributeId}/values', [AttributeController::class, 'storeValue'])->name('storeValue');
        Route::put('/values/{id}', [AttributeController::class, 'updateValue'])->name('updateValue');
        Route::delete('/values/{id}', [AttributeController::class, 'destroyValue'])->name('destroyValue');
    });
});

Route::middleware(['auth', 'admin'])->prefix('admin')->as('admin.')->group(function () {
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
    Route::resource('news', \App\Http\Controllers\Admin\NewsController::class)->except(['show']);
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

