# KHÁM PHÁ CHI TIẾT HỆ THỐNG (FINAL SYSTEM DISCOVERY)
## DỰ ÁN: NÔNG SẢN VIỆT

Tài liệu này bổ sung các khía cạnh kỹ thuật chuyên sâu và các cơ chế ngầm của hệ thống mà các tài liệu trước chưa đề cập hết.

---

### 1. Cơ chế Webhook & Bảo mật Giao dịch
Hệ thống sử dụng một Endpoint Webhook tập trung (`/webhooks/orders/{order}`) để đồng bộ hóa trạng thái từ các đối tác bên ngoài (Thanh toán, Vận chuyển).

- **Xác thực Header:** Sử dụng `X-Webhook-Secret` kết hợp với `hash_equals` để ngăn chặn tấn công giả mạo (Replay Attack).
- **Đồng bộ hóa 3 lớp:** Một lần gọi Webhook có thể cập nhật đồng thời trạng thái Đơn hàng (`Order`), trạng thái Thanh toán (`Payment`) và trạng thái Vận chuyển (`Shipment`).
- **Idempotency:** Hệ thống ghi lại toàn bộ `metadata` của Request vào lịch sử để phục vụ việc tra soát (Audit Trail) khi có tranh chấp giao dịch.

### 2. Độ tin cậy & Kiểm thử (Quality Assurance)
Hệ thống được bảo vệ bởi 19 kịch bản kiểm thử tự động (Feature Tests), đảm bảo các logic cốt lõi không bị phá vỡ khi nâng cấp:
- **`StockCheckoutTest`**: Đảm bảo không bao giờ xảy ra tình trạng bán quá số lượng tồn kho (Over-selling).
- **`ShippingCarrierQuoteTest`**: Kiểm tra tính chính xác của thuật toán báo giá vận chuyển.
- **`CouponCheckoutTest`**: Test các trường hợp mã giảm giá hết hạn, sai đơn tối thiểu hoặc hết lượt dùng.
- **`OrderFulfillmentFlowTest`**: Mô phỏng toàn bộ vòng đời đơn hàng từ lúc đặt đến lúc giao thành công.

### 3. Kiến trúc Middleware & Exception
- **Middleware Alias:** Đăng ký tại `bootstrap/app.php` theo chuẩn Laravel 12 mới nhất.
- **Atomic Transaction:** Tất cả các thao tác thay đổi trạng thái nhạy cảm (như cập nhật Webhook) đều được bọc trong `DB::transaction()` để đảm bảo dữ liệu luôn nhất quán.
- **Health Check:** Hệ thống sử dụng endpoint mặc định `/up` để giám sát tình trạng hoạt động của Server.

### 4. Quản lý Tài sản (Asset Management)
- **Vite Bundler:** Tối ưu hóa việc nén file và hot-reload trong quá trình phát triển.
- **Tailwind JIT:** Chỉ đóng gói những CSS thực sự sử dụng, giúp tốc độ tải trang cực nhanh.
- **Alpine.js:** Xử lý logic phía Client mà không cần các Framework nặng nề như Vue/React, giữ cho ứng dụng nhẹ và mượt mà.

### 5. Cấu hình Môi trường (.env)
Các tham số quan trọng cần lưu ý khi triển khai:
- `VNPAY_HASH_SECRET`: Chìa khóa bảo mật để tạo chữ ký SHA512 cho thanh toán.
- `ORDER_WEBHOOK_SECRET`: Chìa khóa để xác thực các cuộc gọi từ Gateway/Carrier.
- `GEMINI_API_KEY`: Kết nối với bộ não AI để tư vấn khách hàng.

---
*Tài liệu này hoàn tất quá trình khai thác thông tin từ mã nguồn dự án Nông Sản Việt.*
