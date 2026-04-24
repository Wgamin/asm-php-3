# BẢN ĐỒ CẤU TRÚC MÃ NGUỒN (SOURCE CODE MAP)
## DỰ ÁN: NÔNG SẢN VIỆT

Tài liệu này hướng dẫn cách điều hướng và tìm kiếm các thành phần quan trọng trong mã nguồn dự án.

### 1. Tầng Xử lý Logic (Backend - `/app`)
- **Controllers (`app/Http/Controllers`)**: 
    - `PaymentController.php`: Logic cốt lõi tích hợp cổng thanh toán VNPAY (SHA512).
    - `ProfileController.php`: Quản lý trung tâm hồ sơ người dùng (Orders, Addresses, Wishlist).
    - `OrderController.php`: Quy trình tạo đơn hàng, trừ kho, và Checkout.
    - `Admin/`: Hệ thống quản trị nội bộ.
- **Models (`app/Models`)**: 
    - `Product.php`: Quản lý thông tin & biến thể sản phẩm.
    - `Order.php`: Quản lý trạng thái và tính toán tổng tiền đơn hàng.
    - `Category.php`: Cấu trúc danh mục đa cấp (Recursive).
- **Services (`app/Services`)**:
    - `CouponService.php`: Xử lý mã giảm giá.
    - `ShippingService.php`: Kết nối API vận chuyển GHN/GHTK.
- **Providers (`app/Providers`)**:
    - `AppServiceProvider.php`: Global sharing dữ liệu Mega Menu (`$categories`).

### 2. Tầng Giao diện (Frontend - `/resources`)
- **Layouts (`resources/views/layouts`)**:
    - `client.blade.php`: Cấu trúc Navigation 3 tầng hiện đại, tích hợp Alpine.js cho Mega Menu.
- **Modules (`resources/views`)**:
    - `profile/index.blade.php`: View đa năng xử lý tất cả các tab cá nhân.
    - `products/index.blade.php`: Giao diện danh sách sản phẩm với bộ lọc.
    - `admin/`: Hệ thống Blade dành cho Quản trị viên.
- **Assets (`resources/css`, `resources/js`)**:
    - Chứa cấu hình TailwindCSS và khởi tạo Alpine.js cho các tương tác Client-side.

### 3. Tầng Dữ liệu & Cấu hình (`/database`, `/config`, `/.env`)
- **Migrations (`database/migrations`)**: Định nghĩa schema của toàn bộ 20+ bảng trong hệ thống.
- **Routes (`routes/web.php`)**: Bản đồ URL của ứng dụng, phân quyền bằng Middleware (`auth`, `admin`).
- **Configuration (`.env`)**: 
    - Kết nối Database.
    - Cấu hình VNPAY (TMN_CODE, HASH_SECRET).
    - Cấu hình Mail & App Key.

### 4. Luồng xử lý Quan trọng (Key Workflows)
1. **Đặt hàng:** `OrderController@store` → `PaymentController@createPayment` → `VNPay Gateway`.
2. **Xác nhận thanh toán:** `VNPay Gateway` → `PaymentController@vnpayReturn` → Cập nhật `Order` & `Payment`.
3. **Mega Menu:** `AppServiceProvider` → `Layout Client` (Hiển thị danh mục từ DB).
4. **Hồ sơ:** `ProfileController@index` (Lấy dữ liệu tập trung) → `profile.index` (Phân tab bằng Alpine.js).

---
*Bản đồ mã nguồn được thiết lập để phục vụ việc bảo trì và mở rộng dự án.*
