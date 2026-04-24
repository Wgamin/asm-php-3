# TÀI LIỆU CẤU TRÚC CƠ SỞ DỮ LIỆU (DATABASE SCHEMA)
## DỰ ÁN: NÔNG SẢN VIỆT

Tài liệu này mô tả chi tiết các bảng, trường dữ liệu và quan hệ trong Database MySQL của dự án.

### 1. Quản lý Người dùng (Users Management)
- **`users`**: Tài khoản người dùng & Admin.
    - `id`, `name`, `email`, `phone`, `password`, `avatar`, `role` (enum: admin, customer), `google_id`.
- **`user_addresses`**: Sổ địa chỉ người dùng.
    - `id`, `user_id` (FK), `full_name`, `phone`, `province`, `district`, `ward`, `address_line`, `is_default` (boolean).

### 2. Quản lý Sản phẩm (Product Catalog)
- **`categories`**: Danh mục sản phẩm.
    - `id`, `name`, `slug`, `parent_id` (FK - Self reference).
- **`products`**: Thông tin sản phẩm.
    - `id`, `category_id` (FK), `name`, `slug`, `description`, `price`, `sale_price`, `cost_price`, `stock`, `image`, `product_type` (simple/variable), `weight_grams`.
- **`product_variants`**: Các biến thể sản phẩm (Khối lượng, kích cỡ).
    - `id`, `product_id` (FK), `sku`, `price`, `sale_price`, `cost_price`, `stock`, `image`, `variant_values` (JSON).
- **`product_images`**: Thư viện ảnh bổ sung cho sản phẩm.
    - `id`, `product_id` (FK), `image_path`.

### 3. Quy trình Đơn hàng (Order Workflow)
- **`orders`**: Thông tin đơn hàng tổng quát.
    - `id`, `user_id` (FK), `order_number`, `total_amount`, `payable_amount`, `discount_amount`, `shipping_fee_amount`, `status`, `payment_method`, `note`.
- **`order_items`**: Chi tiết sản phẩm trong đơn.
    - `id`, `order_id` (FK), `product_id` (FK), `variant_id` (nullable), `quantity`, `price`, `cost_price`.
- **`payments`**: Nhật ký giao dịch thanh toán.
    - `id`, `order_id` (FK), `method`, `provider`, `amount`, `status`, `transaction_code` (Mã VNPAY), `metadata` (JSON).
- **`shipments`**: Thông tin vận chuyển.
    - `id`, `order_id` (FK), `carrier` (GHN/GHTK), `fee_amount`, `status`, `tracking_code`, `estimated_delivery_at`.

### 4. Tính năng Mở rộng (Extended Features)
- **`wishlists`**: Sản phẩm yêu thích (Pivot table).
    - `user_id` (FK), `product_id` (FK).
- **`coupons`**: Mã giảm giá.
    - `id`, `code`, `type` (fixed/percent), `value`, `min_order_value`, `usage_limit`, `used_count`, `expires_at`.
- **`product_reviews`**: Đánh giá sản phẩm.
    - `id`, `user_id` (FK), `product_id` (FK), `rating` (1-5), `comment`.
- **`news_articles`**: Bài viết tin tức & Blog.
    - `id`, `title`, `slug`, `content`, `image`, `author_id`.

### 5. Sơ đồ Quan hệ Chính (Entity Relationship Logic)
1. **User (1) → Order (n)**: Một khách hàng có nhiều đơn hàng.
2. **Category (1) → Product (n)**: Một danh mục chứa nhiều sản phẩm.
3. **Product (1) → Variant (n)**: Một sản phẩm có nhiều quy cách đóng gói/biến thể.
4. **Order (1) → OrderItem (n)**: Một đơn hàng chứa nhiều mặt hàng.
5. **Order (1) ↔ Payment (1)**: Một đơn hàng có một bản ghi thanh toán duy nhất.
6. **Order (1) ↔ Shipment (1)**: Một đơn hàng đi kèm một vận đơn duy nhất.

---
*Tài liệu Database Schema được chuẩn hóa để phục vụ việc phân tích dữ liệu và báo cáo đồ án.*
