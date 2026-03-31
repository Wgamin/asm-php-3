<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AuthController; 
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController as PublicProductController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\WishlistController;

use App\Http\Controllers\CompareController;
/*
|--------------------------------------------------------------------------
| 1. DÀNH CHO TẤT CẢ MỌI NGƯỜI (PUBLIC & GUEST)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/san-pham', [PublicProductController::class, 'index'])->name('products.index');
Route::get('/san-pham/{id}', [PublicProductController::class, 'show'])->name('product.detail');
Route::get('/lien-he', function() {
    return view('contact');
})->name('contact');
Route::get('/order-success', [OrderController::class, 'success'])->name('order.success');

// --- CẬP NHẬT: Nhóm Route cho Giỏ hàng (KHÔNG DÙNG AJAX) ---
Route::prefix('cart')->as('cart.')->group(function () {
    Route::post('/add/{id}', [CartController::class, 'add'])->name('add');
    
    // Thay đổi PATCH/DELETE thành GET để dùng thẻ <a> truyền thống
    Route::get('/update/{id}/{quantity}', [CartController::class, 'updateQuantity'])->name('update_quantity'); 
    Route::get('/remove/{id}', [CartController::class, 'removeItem'])->name('remove_item');
    
    Route::get('/clear', [CartController::class, 'clear'])->name('clear');
});

// Nhóm dành riêng cho khách CHƯA đăng nhập (Guest)
Route::middleware(['guest'])->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| 2. DÀNH CHO NGƯỜI DÙNG ĐÃ ĐĂNG NHẬP (USER & ADMIN)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Chuyển checkout vào chung nhóm auth
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [OrderController::class, 'store'])->name('order.store');
    Route::get('/order-success', [OrderController::class, 'success'])->name('order.success');

    // --- MỚI: Route cho Wishlist ---
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle/{id}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
});

/*
|--------------------------------------------------------------------------
| 3. DÀNH RIÊNG CHO QUẢN TRỊ VIÊN (ADMIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->as('admin.')->group(function () {
    Route::controller(AdminOrderController::class)->group(function () {
        Route::get('/orders', 'index')->name('orders.index');
        Route::get('/orders/{id}', 'show')->name('orders.show');
        Route::post('/orders/{id}/status', 'updateStatus')->name('orders.updateStatus');
        Route::delete('/orders/{id}', 'destroy')->name('orders.destroy');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('users', UserController::class);
    Route::get('/roles', [UserController::class, 'index'])->name('roles.index'); 
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
});

// --- AUTH CHO ADMIN ---
Route::prefix('admin')->as('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showAdminLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'adminLogin']);
        Route::get('/register', [AuthController::class, 'showAdminRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'adminRegister']);
    });
    // Không cần định nghĩa logout ở đây nếu đã có ở trên, trừ khi logic khác biệt hoàn toàn
});
//so sánh sản phẩm 
Route::get('/so-sanh', [CompareController::class, 'index'])->name('compare.index');
Route::post('/so-sanh/{product}', [CompareController::class, 'add'])->name('compare.add');
Route::delete('/so-sanh/{product}', [CompareController::class, 'remove'])->name('compare.remove');
Route::delete('/so-sanh', [CompareController::class, 'clear'])->name('compare.clear');




