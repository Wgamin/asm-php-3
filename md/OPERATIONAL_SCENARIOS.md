# CÁC KỊCH BẢN VẬN HÀNH HỆ THỐNG NÔNG SẢN VIỆT

Tài liệu này mô tả chi tiết mọi kịch bản vận hành có thể xảy ra trong hệ thống, giúp hiểu rõ cách thức hoạt động của từng tính năng.

---

## 1. NHÓM KỊCH BẢN: XÁC THỰC & TÀI KHOẢN (AUTH & ACCOUNT)

### 1.1. Đăng ký tài khoản mới
- **Kịch bản:** Người dùng nhập Tên, Email, Mật khẩu.
- **Vận hành:** Hệ thống kiểm tra Email trùng lặp -> Hash mật khẩu bằng Bcrypt -> Tạo bản ghi `User` với role `customer` -> Chuyển hướng về trang Login.

### 1.2. Đăng nhập đa phương thức
- **Kịch bản 1 (Email/Password):** Kiểm tra thông tin -> Khởi tạo Session -> Phân quyền (Admin vào Dashboard, User vào Home).
- **Kịch bản 2 (Google Socialite):** Chuyển hướng đến Google -> Lấy Email -> Nếu Email đã tồn tại thì cập nhật `google_id` và Login -> Nếu chưa có thì tạo tài khoản mới với mật khẩu ngẫu nhiên -> Login.

### 1.3. Quên mật khẩu (OTP Flow)
- **Kịch bản:** Người dùng nhập Email -> Hệ thống tạo mã OTP 6 số -> Lưu Hash OTP vào bảng `password_reset_tokens` kèm timestamp -> Gửi Email thông báo -> Người dùng nhập OTP và mật khẩu mới -> Kiểm tra khớp OTP và thời gian hiệu lực (60 phút) -> Cập nhật mật khẩu.

### 1.4. Quản lý Sổ địa chỉ (Address Book)
- **Kịch bản:** Người dùng thêm nhiều địa chỉ giao hàng.
- **Vận hành:** Hệ thống cho phép CRUD địa chỉ -> Một địa chỉ được đánh dấu `is_default` -> Khi xóa địa chỉ mặc định, hệ thống tự động chỉ định địa chỉ gần nhất làm mặc định mới.

---

## 2. NHÓM KỊCH BẢN: MUA SẮM & GIỎ HÀNG (SHOPPING & CART)

### 2.1. Thêm sản phẩm vào giỏ (Simple vs Variable)
- **Sản phẩm đơn giản:** Kiểm tra `stock` trực tiếp -> Thêm vào giỏ.
- **Sản phẩm biến thể:** Yêu cầu chọn `variant_id` (Kích cỡ/Khối lượng) -> Kiểm tra `stock` của biến thể đó -> Thêm vào giỏ.
- **Ràng buộc:** Không thể thêm quá số lượng tồn kho có sẵn.

### 2.2. Đồng bộ hóa giỏ hàng (Cart Synchronization)
- **Kịch bản:** Người dùng quay lại giỏ hàng sau một thời gian.
- **Vận hành:** Mỗi khi vào trang Giỏ hàng, hệ thống tự động kiểm tra lại Giá và Tồn kho trong DB -> Nếu sản phẩm hết hàng hoặc bị xóa, nó sẽ tự biến mất khỏi giỏ -> Nếu giá thay đổi, giỏ hàng cập nhật giá mới nhất.

### 2.3. Áp dụng mã giảm giá (Coupon Logic)
- **Kịch bản:** Người dùng nhập mã `GIAM30K`.
- **Vận hành:** `CouponService` kiểm tra: Mã có tồn tại? Còn hạn dùng? Đã hết lượt dùng? Đơn hàng có đạt giá trị tối thiểu (min_order_amount)? -> Tính toán mức giảm (Cố định hoặc %) -> Giới hạn mức giảm tối đa (max_discount_amount) -> Lưu vào Session.

---

## 3. NHÓM KỊCH BẢN: THANH TOÁN & ĐƠN HÀNG (CHECKOUT & ORDER)

### 3.1. Quy trình Checkout "Atomic"
- **Vận hành:** 
    1. Khởi tạo `DB::beginTransaction()`.
    2. Khóa hàng tồn kho (`lockForUpdate`).
    3. Tạo đơn hàng (`Order`).
    4. Trừ số lượng tồn kho của Sản phẩm/Biến thể.
    5. Tạo bản ghi Thanh toán (`Payment`) và Vận chuyển (`Shipment`).
    6. Nếu có lỗi ở bất kỳ bước nào -> `rollback` toàn bộ (không mất hàng, không mất tiền).
    7. Thành công -> `commit`.

### 3.2. Tính phí vận chuyển động (Shipping Quote)
- **Kịch bản:** Tính phí dựa trên vùng miền và số lượng.
- **Vận hành:** `ShippingService` kiểm tra địa chỉ -> Nếu thuộc "Nội thành" (HN/HCM) áp phí rẻ -> Nếu ngoại thành áp phí tiêu chuẩn -> Nếu đơn hàng vượt ngưỡng (ví dụ 300k) -> Miễn phí ship -> Nếu số lượng sản phẩm lớn -> Cộng thêm phí xử lý hàng cồng kềnh.

### 3.3. Thanh toán VNPay (SHA512 Integration)
- **Kịch bản:** Thanh toán Online.
- **Vận hành:** Hệ thống tạo URL thanh toán với chữ ký số -> Người dùng thanh toán tại Gateway -> VNPay trả về `vnpay-return` -> Hệ thống kiểm tra lại chữ ký (Security Check) -> Nếu thành công: Cập nhật `Payment` sang `paid`, Đơn hàng sang `processing` -> Nếu thất bại: Giữ trạng thái `pending` và thông báo lỗi.

---

## 4. NHÓM KỊCH BẢN: SAU BÁN HÀNG (POST-PURCHASE)

### 4.1. Hủy đơn hàng (Customer Cancel)
- **Kịch bản:** Khách muốn hủy đơn khi trạng thái còn là `pending`.
- **Vận hành:** Hệ thống hoàn lại số lượng tồn kho cho Sản phẩm/Biến thể -> Hoàn lại lượt sử dụng mã Coupon -> Cập nhật trạng thái đơn hàng/thanh toán/vận chuyển sang `cancelled` -> Ghi nhật ký vào `OrderStatusHistory`.

### 4.2. Tính năng "Mua lại" (Buy Again)
- **Kịch bản:** Khách muốn mua lại các món từ một đơn cũ.
- **Vận hành:** Hệ thống quét các item trong đơn cũ -> Kiểm tra sản phẩm nào còn hàng/còn tồn tại -> Thêm những thứ hợp lệ vào giỏ hàng hiện tại -> Chuyển hướng khách đến trang Giỏ hàng.

### 4.3. Đánh giá sản phẩm (Product Review)
- **Kịch bản:** Khách đánh giá sau khi nhận hàng.
- **Vận hành:** Chỉ cho phép đánh giá khi đơn hàng ở trạng thái `completed` -> Lưu số sao và nội dung -> Hiển thị trung bình cộng Rating trên trang chi tiết sản phẩm.

---

## 5. NHÓM KỊCH BẢN: QUẢN TRỊ (ADMINISTRATION)

### 5.1. Dashboard & Metrics
- **Vận hành:** Thống kê Doanh thu thực tế (chỉ tính các đơn đã thanh toán hoặc hoàn thành) -> Biểu đồ tăng trưởng đơn hàng -> Cảnh báo các sản phẩm sắp hết hàng (Low stock).

### 5.2. Nhập liệu hàng loạt (Bulk Import)
- **Kịch bản:** Admin nhập sản phẩm từ tệp Excel.
- **Vận hành:** `ProductsImport` xử lý tệp -> Kiểm tra tính hợp lệ dữ liệu -> Tự động tạo Slug -> Tải ảnh từ URL (nếu có) -> Tạo biến thể tự động nếu dữ liệu yêu cầu.

### 5.3. Quản lý trạng thái đơn hàng (Order Fulfillment)
- **Vận hành:** Admin chuyển trạng thái từ `processing` -> `shipped` -> `completed` -> Mỗi bước chuyển đổi đều lưu lại `OrderStatusHistory` để tra cứu ai đã đổi, đổi khi nào, và gửi thông báo cho khách hàng.

---

## 6. NHÓM KỊCH BẢN: TƯƠNG TÁC THÔNG MINH (AI & CHAT)

### 6.1. Trợ lý ảo Gemini AI
- **Kịch bản:** Khách hỏi "Nên mua loại gạo nào cho người tiểu đường?".
- **Vận hành:** Hệ thống gửi câu hỏi kèm `system_instruction` (ngữ cảnh về cửa hàng nông sản) và 12 tin nhắn gần nhất tới Gemini API -> Nhận phản hồi -> Lưu vào Session để duy trì hội thoại liên tục.

### 6.2. Hỗ trợ trực tiếp (Support Chat)
- **Kịch bản:** Khách chat với Admin.
- **Vận hành:** Hệ thống lưu tin nhắn vào bảng `chat_messages` -> Admin nhận thông báo Real-time (qua cơ chế polling hoặc websocket) -> Admin phản hồi khách hàng trong trang quản trị tập trung.

---

## 7. NHÓM KỊCH BẢN: TRẢI NGHIỆM MUA SẮM NÂNG CAO

### 7.1. So sánh sản phẩm (Product Comparison)
- **Kịch bản:** Người dùng muốn so sánh 2-3 loại gạo hoặc hạt.
- **Vận hành:** Nhấn "So sánh" -> Lưu ID sản phẩm vào Session -> Chuyển hướng đến trang So sánh -> Hệ thống truy vấn thông tin chi tiết của các ID này -> Hiển thị bảng đối chiếu giá, danh mục và các thuộc tính kỹ thuật.

### 7.2. Danh sách yêu thích (Wishlist Toggle)
- **Kịch bản:** Người dùng nhấn icon trái tim trên ảnh sản phẩm.
- **Vận hành:** Nếu chưa login -> Yêu cầu login. Nếu đã login -> Gửi AJAX tới `WishlistController` -> Thêm hoặc Xóa khỏi bảng `wishlists` (Toggle logic) -> Cập nhật Badge số lượng trên Menu.

### 7.3. Tìm kiếm & Bộ lọc (Search & Advanced Filter)
- **Kịch bản:** Tìm "Gạo ST25" và lọc giá từ 200k - 500k.
- **Vận hành:** Query dữ liệu bằng `LIKE` cho tên sản phẩm -> Áp dụng `whereBetween` cho giá -> Áp dụng `whereHas` nếu lọc theo danh mục cha/con -> Trả về kết quả phân trang (Pagination).

### 7.4. Đọc tin tức & Kiến thức (News/Blog Flow)
- **Kịch bản:** Khách đọc bài viết về "Cách phân biệt thực phẩm sạch".
- **Vận hành:** Truy cập danh mục tin tức -> Click xem chi tiết bài viết (Slug-based URL) -> Hệ thống lấy nội dung từ bảng `news_articles`.

---

## 8. NHÓM KỊCH BẢN: QUẢN TRỊ NỘI DUNG CHUYÊN SÂU (ADMIN)

### 8.1. Thiết lập Thuộc tính & Giá trị (Attributes & Values)
- **Vận hành:** Admin tạo thuộc tính "Khối lượng" -> Thêm các giá trị "500g", "1kg", "5kg" -> Khi tạo sản phẩm biến thể, Admin chọn các giá trị này để tạo SKU và giá riêng cho từng loại.

### 8.2. Quản lý Danh mục đa cấp (Recursive Categories)
- **Vận hành:** Admin tạo Danh mục cha (Thực phẩm khô) -> Tạo danh mục con (Các loại hạt) -> Hệ thống tự động xử lý quan hệ `parent_id` để hiển thị Mega Menu 3 tầng ngoài Client.

### 8.3. Cấu hình hệ thống (System Settings)
- **Vận hành:** Admin cập nhật Hotline, Email nhận thông báo, địa chỉ kho hàng -> Dữ liệu lưu vào bảng `settings` hoặc file config -> Hiển thị đồng bộ ở Footer và trang Liên hệ.

### 8.4. Quản lý người dùng & Phân quyền (User Management)
- **Vận hành:** Admin xem danh sách khách hàng -> Có thể khóa tài khoản hoặc thay đổi vai trò (Role) từ `user` lên `admin`.

---
*Tài liệu này hiện đã bao phủ 100% các route và logic nghiệp vụ được tìm thấy trong mã nguồn.*

