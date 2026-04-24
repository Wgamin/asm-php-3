# TÀI LIỆU CHI TIẾT DỰ ÁN: NÔNG SẢN VIỆT (E-COMMERCE)

## 1. Giới thiệu tổng quan
- **Tên dự án:** Nông Sản Việt.
- **Mục tiêu:** Nền tảng thương mại điện tử chuyên cung cấp nông sản sạch, kết hợp tin tức thị trường và tư vấn bằng AI.
- **Đối tượng:** Người tiêu dùng lẻ cần thực phẩm nguồn gốc rõ ràng.

## 2. Công nghệ sử dụng (Tech Stack)
- **Backend:** Laravel 12.x (PHP 8.2+).
- **Frontend:** Blade Template, TailwindCSS (v3), Alpine.js (cho các xử lý tương tác nhanh), jQuery (cho AJAX).
- **Database:** MySQL.
- **Công cụ:** Vite (Asset Bundling), Composer, NPM.
- **Tích hợp:** 
    - **VNPAY API v2.1.0:** Cổng thanh toán trực tuyến.
    - **Google Socialite:** Đăng nhập qua Google.
    - **AI Chatbot:** (Gemini API) để tư vấn sản phẩm.
    - **GHN/GHTK:** Logic tính phí vận chuyển thông qua `ShippingService`.

## 3. Kiến trúc hệ thống & Logic cốt lõi

### A. Quy trình Thanh toán (Checkout & Payment)
- **Cơ chế:** Khi khách hàng đặt hàng, hệ thống tạo `Order` với trạng thái `pending`.
- **VNPay Logic:** 
    - Sử dụng chuẩn hash **SHA512** với mã hóa **RFC 3986** (không dùng `urldecode` trước khi hash).
    - Logic băm chuỗi (`SecureHash`) được xử lý thủ công qua vòng lặp để đảm bảo độ chính xác tuyệt đối theo tài liệu kỹ thuật VNPAY 2.1.0.
- **Xử lý đơn hàng:** Sau khi VNPay trả về kết quả thành công (`ResponseCode == 00`), hệ thống cập nhật trạng thái đơn hàng sang `processing` và lưu mã giao dịch.

### B. Cấu trúc Navigation & Mega Menu
- **3 Tầng (Tier Navigation):** 
    - Tầng 1: Thông tin liên hệ & Auth (Login/Register).
    - Tầng 2: Logo, Search Bar, Cart/Wishlist badge.
    - Tầng 3: Menu điều hướng chính.
- **Mega Menu:** Danh mục sản phẩm được chia sẻ toàn cục thông qua `AppServiceProvider`, hỗ trợ hiển thị danh mục đa cấp (Cha - Con).

### C. Quản lý Hồ sơ & Trải nghiệm người dùng (UX)
- **Profile Center:** Một View duy nhất (`profile.index`) xử lý nhiều Tab thông qua Alpine.js:
    - `info`: Cập nhật thông tin, ảnh đại diện, mật khẩu.
    - `addresses`: Quản lý nhiều địa chỉ giao hàng (CRUD), hỗ trợ đặt địa chỉ mặc định.
    - `orders`: Lịch sử đơn hàng, xem chi tiết, hủy đơn, mua lại đơn cũ.
    - `wishlist`: Danh sách sản phẩm yêu thích (Redirect từ /wishlist sang /profile?tab=wishlist).
    - `compare`: So sánh thông số kỹ thuật giữa các sản phẩm.

## 4. Chi tiết các trang trong hệ thống

### A. Giao diện khách hàng (Client Side)
1. **Trang Chủ:** Banner quảng bá, sản phẩm mới nhất, tin tức tiêu biểu.
2. **Danh Sách Sản Phẩm:** Bộ lọc theo danh mục/giá/thuộc tính, sắp xếp thông minh.
3. **Chi Tiết Sản Phẩm:** Gallery ảnh, chọn biến thể, mô tả kỹ thuật, đánh giá (Review) từ người dùng.
4. **Giỏ Hàng:** Quản lý số lượng sản phẩm, tính tổng tiền hàng.
5. **Thanh Toán (Checkout):** Nhập địa chỉ giao hàng, tính phí ship thời gian thực (GHN/GHTK), áp dụng mã giảm giá, chọn phương thức thanh toán (COD/VNPAY).
6. **Hồ Sơ Cá Nhân:** Quản lý thông tin, danh sách địa chỉ, lịch sử đơn hàng, yêu thích và so sánh sản phẩm.
7. **Tin Tức:** Danh sách bài viết và chi tiết bài viết về nông nghiệp/thực phẩm.
8. **Liên Hệ & Giới Thiệu:** Thông tin doanh nghiệp và form liên lạc.
9. **Chatbot AI:** Giao diện tư vấn sản phẩm thông qua AI Gemini.

### B. Giao diện quản trị (Admin Side)
1. **Dashboard:** Thống kê doanh thu, đơn hàng và khách hàng bằng biểu đồ.
2. **Quản Lý Sản Phẩm:** CRUD sản phẩm, quản lý biến thể, quản lý tồn kho, nhập liệu từ Excel.
3. **Quản Lý Danh Mục:** Cấu trúc danh mục đa tầng.
4. **Quản Lý Đơn Hàng:** Tiếp nhận, phê duyệt và cập nhật trạng thái vận chuyển.
5. **Quản Lý Mã Giảm Giá:** Thiết lập các chương trình khuyến mãi theo mã code.
6. **Quản Lý Khách Hàng:** Quản lý tài khoản người dùng và phân quyền hệ thống.
7. **Hệ Thống Chat:** Admin hỗ trợ trực tiếp người dùng trong thời gian thực.

## 5. Cấu trúc Database quan trọng
- `users`: Thông tin người dùng, vai trò (`admin`, `customer`), `google_id`, `avatar`, `phone`.
- `products`: Sản phẩm chính, hỗ trợ sản phẩm đơn giản hoặc biến thể (`product_type`).
- `product_variants`: Các biến thể sản phẩm (Kích thước, khối lượng, giá riêng, `cost_price`).
- `categories`: Danh mục sản phẩm có quan hệ cha-con (`parent_id`).
- `orders`: Thông tin đơn hàng (Tổng tiền, phí ship, giảm giá từ coupon, `payable_amount`).
- `order_items`: Chi tiết từng sản phẩm trong đơn, lưu giá tại thời điểm mua (`cost_price`, `price`).
- `payments`: Lưu lịch sử giao dịch (Phương thức, trạng thái từ Gateway, `transaction_code`).
- `shipments`: Thông tin vận chuyển, đơn vị vận chuyển (`carrier`), phí ship, trạng thái.
- `wishlists`: Bảng trung gian (Many-to-Many) giữa `User` và `Product`.
- `coupons`: Mã giảm giá (Số lần dùng, mức giảm, đơn tối thiểu).

## 5. Các Service & Middleware đặc thù
- **CouponService:** Xử lý logic áp dụng mã giảm giá, tính toán mức giảm và kiểm tra lỗi.
- **ShippingService:** Tính toán phí vận chuyển thực tế từ các đơn vị vận chuyển (GHN/GHTK).
- **AdminMiddleware:** Bảo vệ các route quản trị, chặn các truy cập không phải Admin.

## 6. Hướng dẫn cài đặt (Installation Guide)
1. Cài đặt môi trường: `composer install` & `npm install`.
2. Cấu hình `.env`:
   - `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
   - `VNPAY_TMN_CODE`, `VNPAY_HASH_SECRET`, `VNPAY_URL`, `VNPAY_RETURN_URL`.
3. Khởi tạo Database: `php artisan migrate --seed`.
4. Liên kết Storage: `php artisan storage:link`.
5. Chạy dự án: `php artisan serve` & `npm run dev`.

---
*Tài liệu được khởi tạo bởi Gemini CLI cho mục đích làm báo cáo đồ án.*
