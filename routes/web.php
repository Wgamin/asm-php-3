<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AuthController; // Controller tự tạo
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController as PublicProductController;

/*
|--------------------------------------------------------------------------
| 1. DÀNH CHO TẤT CẢ MỌI NGƯỜI (PUBLIC & GUEST)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/san-pham', [PublicProductController::class, 'index'])->name('products.index');
Route::get('/san-pham/{id}', [PublicProductController::class, 'show'])->name('product.detail');


// Nhóm dành riêng cho khách CHƯA đăng nhập (Guest)
Route::middleware(['guest'])->group(function () {
    // Đăng ký
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Đăng nhập
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});


// Đăng xuất (Phải đăng nhập mới logout được)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


/*
|--------------------------------------------------------------------------
| 2. DÀNH CHO NGƯỜI DÙNG ĐÃ ĐĂNG NHẬP (USER & ADMIN)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
});


/*
|--------------------------------------------------------------------------
| 3. DÀNH RIÊNG CHO QUẢN TRỊ VIÊN (ADMIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->as('admin.')->group(function () {
    
    // Trang chủ Admin
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Quản lý sản phẩm
    Route::resource('products', ProductController::class);
    // quản lý danh mục (Dùng link này cho menu "Danh mục")
    Route::resource('categories', CategoryController::class);
    // Quản lý người dùng (Dùng link này cho menu "Người dùng")
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    
    // Quản lý vai trò (Nếu bạn muốn tách riêng trang Roles)
    Route::get('/roles', [UserController::class, 'index'])->name('roles.index'); 

});

// --- AUTH CHO ADMIN ---
Route::prefix('admin')->as('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        // Form đăng nhập admin: domain.com/admin/login
        Route::get('/login', [AuthController::class, 'showAdminLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'adminLogin']);

        // Form đăng ký admin (Nếu bạn muốn cho phép đăng ký trực tiếp quyền admin)
        Route::get('/register', [AuthController::class, 'showAdminRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'adminRegister']);
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
});