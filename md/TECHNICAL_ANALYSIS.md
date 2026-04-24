# BẢN PHÂN TÍCH KỸ THUẬT CHUYÊN SÂU (TECHNICAL SOURCE ANALYSIS)
## DỰ ÁN: NÔNG SẢN VIỆT (LARAVEL 12)

### 1. Kiến trúc Hệ thống (Architecture Overview)
Dự án được xây dựng dựa trên framework **Laravel 12 (PHP 8.2+)**, áp dụng mô hình **MVC** kết hợp với **Service Pattern**.

- **Frontend:** Blade Template + TailwindCSS + Alpine.js + jQuery.
- **Backend:** Laravel Core với các lớp xử lý tách biệt: Controllers, Models, Services, Middlewares, Notifications.
- **Database:** MySQL với hệ thống quan hệ (Relationships) chặt chẽ.

### 2. Phân tích Các Lớp Logic (Logic Layers)

#### A. Lớp Model & Database (Data Layer)
Sử dụng Eloquent ORM để quản lý dữ liệu. Các quan hệ chính:
- `Product` ↔ `ProductVariant`: One-to-Many (Quản lý quy cách đóng gói, giá biến thể).
- `Category` ↔ `Category`: Recursive One-to-Many (Danh mục cha-con).
- `Order` ↔ `OrderItem`: One-to-Many (Chi tiết đơn hàng).
- `User` ↔ `UserAddress`: One-to-Many (Quản lý nhiều địa chỉ giao hàng).
- `User` ↔ `Product` (Wishlist): Many-to-Many (Bảng trung gian `wishlists`).

#### B. Lớp Controller (Presentation/Logic Layer)
- **PaymentController:** Xử lý cổng thanh toán VNPAY. Điểm đặc biệt là thuật toán băm chữ ký (`vnp_SecureHash`) được viết thủ công bằng vòng lặp, sử dụng mã hóa **RFC 3986** và **HMAC-SHA512** để đảm bảo tính chính xác tuyệt đối so với Gateway.
- **ProfileController:** Quản lý đa chức năng (Tab-based). Sử dụng `Eager Loading` (`with()`) để tối ưu hóa số lượng câu lệnh SQL (giải quyết bài toán N+1 queries).
- **OrderController:** Xử lý quy trình Checkout. Toàn bộ logic tạo đơn hàng, trừ kho sản phẩm, áp mã giảm giá và tính phí vận chuyển được bọc trong `DB::beginTransaction()` để đảm bảo tính **Atomic** (Tất cả thành công hoặc tất cả thất bại).

#### C. Lớp Service (Business Logic Layer)
- **CouponService:** Tách biệt logic kiểm tra mã giảm giá (Hạn dùng, giá trị đơn hàng tối thiểu, số lần sử dụng tối đa).
- **ShippingService:** Kết nối logic với các đơn vị vận chuyển (GHN, GHTK) để tính phí ship động.

### 3. Công nghệ Frontend & Tương tác (Frontend Engineering)
- **Mega Menu:** Dữ liệu danh mục được chia sẻ toàn cục qua `AppServiceProvider`, giúp Menu luôn cập nhật dữ liệu mới nhất từ Database mà không cần gọi lại ở mỗi Controller.
- **Alpine.js:** Xử lý các tương tác phía Client (Chuyển tab Profile, đóng mở Menu) giúp giảm tải cho server và tăng tốc độ phản hồi giao diện.
- **Vite:** Công cụ đóng gói (Bundler) hiện đại, giúp tối ưu hóa dung lượng file CSS/JS khi triển khai thực tế.

### 4. An ninh & Bảo mật (Security)
- **Authentication:** Sử dụng hệ thống Auth mặc định của Laravel, kết hợp với `Bcrypt` để băm mật khẩu.
- **Authorization:** Sử dụng `AdminMiddleware` để phân quyền người dùng và Quản trị viên.
- **Data Integrity:** Sử dụng `FormRequest` để validate dữ liệu từ Client, ngăn chặn các cuộc tấn công XSS và SQL Injection.
- **Payment Security:** Tuân thủ nghiêm ngặt quy trình tạo chữ ký số của VNPAY, bảo vệ các tham số nhạy cảm trong file `.env`.

### 5. Cấu trúc Thư mục Quan trọng
- `app/Http/Controllers/`: Chứa logic điều hướng và xử lý yêu cầu.
- `app/Models/`: Định nghĩa cấu trúc dữ liệu và các mối quan hệ.
- `app/Services/`: Chứa logic nghiệp vụ dùng chung.
- `database/migrations/`: Lịch sử thay đổi cấu trúc bảng.
- `resources/views/`: Hệ thống giao diện Blade.
- `public/storage/`: Nơi lưu trữ ảnh sản phẩm và avatar người dùng.

---
*Tài liệu phân tích kỹ thuật được khởi tạo tự động bởi Gemini CLI - 2026.*
