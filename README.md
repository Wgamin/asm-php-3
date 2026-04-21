# ASM PHP 3

Website bán nông sản xây dựng bằng Laravel 12, có giao diện khách hàng, trang quản trị và các module thương mại điện tử cơ bản như giỏ hàng, đặt hàng, coupon, tin tức chuẩn SEO, wishlist và so sánh sản phẩm.

## Công nghệ sử dụng

- PHP `^8.2`
- Laravel `^12.0`
- MySQL
- Vite
- Tailwind CSS `^4`
- Alpine.js
- Pest
- Laravel Socialite

## Tính năng hiện có

### Phía khách hàng

- Trang chủ hiển thị sản phẩm nổi bật và bài viết mới
- Danh sách sản phẩm và trang chi tiết sản phẩm
- Giỏ hàng lưu bằng session
- Checkout và đặt hàng
- Áp dụng / gỡ coupon tại checkout
- Wishlist
- So sánh sản phẩm
- Đăng ký, đăng nhập, đăng xuất
- Đăng nhập bằng Google
- Hồ sơ người dùng
- Trang tin tức `/tin-tuc`
- Trang chi tiết bài viết theo slug `/tin-tuc/{slug}`
- Trang liên hệ

### Phía quản trị

- Dashboard
- Quản lý sản phẩm
- Quản lý danh mục
- Quản lý người dùng
- Quản lý đơn hàng
- Quản lý coupon
- Quản lý tin tức
- Quản lý thuộc tính / giá trị thuộc tính
- Cài đặt hệ thống

### SEO cho module tin tức

- URL thân thiện bằng `slug`
- `meta title`, `meta description`, `meta keywords`
- Canonical URL
- Open Graph / Twitter Card
- JSON-LD cho `Blog`, `NewsArticle`, `BreadcrumbList`
- Chỉ bài viết thỏa:
  - `is_published = true`
  - `published_at <= now()`
  mới hiển thị ngoài public

## Cấu trúc chức năng chính

### Public

- [routes/web.php](C:\xampp\htdocs\asmphp3\asm-php-3\routes\web.php)
- [app/Http/Controllers/HomeController.php](C:\xampp\htdocs\asmphp3\asm-php-3\app\Http\Controllers\HomeController.php)
- [app/Http/Controllers/ProductController.php](C:\xampp\htdocs\asmphp3\asm-php-3\app\Http\Controllers\ProductController.php)
- [app/Http/Controllers/OrderController.php](C:\xampp\htdocs\asmphp3\asm-php-3\app\Http\Controllers\OrderController.php)
- [app/Http/Controllers/NewsController.php](C:\xampp\htdocs\asmphp3\asm-php-3\app\Http\Controllers\NewsController.php)

### Admin

- [app/Http/Controllers/Admin/ProductController.php](C:\xampp\htdocs\asmphp3\asm-php-3\app\Http\Controllers\Admin\ProductController.php)
- [app/Http/Controllers/Admin/OrderController.php](C:\xampp\htdocs\asmphp3\asm-php-3\app\Http\Controllers\Admin\OrderController.php)
- [app/Http/Controllers/Admin/CouponController.php](C:\xampp\htdocs\asmphp3\asm-php-3\app\Http\Controllers\Admin\CouponController.php)
- [app/Http/Controllers/Admin/NewsController.php](C:\xampp\htdocs\asmphp3\asm-php-3\app\Http\Controllers\Admin\NewsController.php)

### Giao diện

- [resources/views/layouts/client.blade.php](C:\xampp\htdocs\asmphp3\asm-php-3\resources\views\layouts\client.blade.php)
- [resources/views/news/index.blade.php](C:\xampp\htdocs\asmphp3\asm-php-3\resources\views\news\index.blade.php)
- [resources/views/news/show.blade.php](C:\xampp\htdocs\asmphp3\asm-php-3\resources\views\news\show.blade.php)
- [resources/views/admin/news/index.blade.php](C:\xampp\htdocs\asmphp3\asm-php-3\resources\views\admin\news\index.blade.php)

## Cài đặt dự án

### 1. Clone hoặc mở source

```powershell
cd C:\xampp\htdocs\asmphp3\asm-php-3
```

### 2. Cài dependency backend và frontend

```powershell
composer install
npm install
```

### 3. Tạo file môi trường

```powershell
Copy-Item .env.example .env
```

### 4. Tạo database

Ví dụ tạo DB bằng MySQL trong XAMPP:

```powershell
cd C:\xampp\mysql\bin
.\mysql -u root -e "CREATE DATABASE asm_php_3 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 5. Cấu hình `.env`

Ví dụ:

```env
APP_NAME="ASM PHP 3"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=asm_php_3
DB_USERNAME=root
DB_PASSWORD=
```

Nếu dùng đăng nhập Google, bổ sung thêm:

```env
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```

### 6. Generate app key, migrate và link storage

```powershell
php artisan key:generate
php artisan migrate
php artisan storage:link
```

### 7. Chạy dự án

Backend:

```powershell
php artisan serve
```

Frontend dev:

```powershell
npm run dev
```

Hoặc dùng script tổng:

```powershell
composer run dev
```

## Lệnh hữu ích

Chạy test:

```powershell
php artisan test
```

Build frontend:

```powershell
npm run build
```

Xem route:

```powershell
php artisan route:list
```

## Deploy hosting

- Mau env cho InterData: [`.env.interdata.example`](.env.interdata.example)
- Huong dan deploy subdomain InterData: [`DEPLOY_INTERDATA.md`](DEPLOY_INTERDATA.md)

## Migration nổi bật

- `orders`, `order_items`
- `categories`, `products`
- `coupons`
- `news_articles`
- `attributes`, `attribute_values`, `product_variants`
- `wishlists`

## Ghi chú vận hành

### Coupon

- Coupon được áp tại checkout
- Nếu giỏ hàng thay đổi và không còn đủ điều kiện, coupon sẽ tự bị gỡ khỏi session
- Đơn hàng lưu riêng:
  - `subtotal_amount`
  - `discount_amount`
  - `coupon_id`
  - `coupon_code`

### Tin tức

- Bài viết có thể ở trạng thái draft hoặc published
- Bài viết public phải có `published_at` hợp lệ
- Nếu admin chọn giờ đăng trong tương lai, bài sẽ chưa xuất hiện ngoài `/tin-tuc`
- Layout public đã có meta SEO và schema cho bài viết

### Timezone

Hiện ứng dụng đang dùng timezone mặc định trong [config/app.php](C:\xampp\htdocs\asmphp3\asm-php-3\config\app.php). Nếu triển khai thực tế cho Việt Nam hoặc Thái Lan, nên cấu hình timezone phù hợp để lịch đăng bài và thời gian đơn hàng hiển thị đúng theo địa phương.

## Kiểm thử hiện có

Các test hiện đã có trong dự án:

- test ví dụ của Laravel
- test checkout + coupon
- test module tin tức public

Chạy toàn bộ:

```powershell
php artisan test
```

## Hướng phát triển tiếp

- CKEditor / TinyMCE cho phần nội dung bài viết
- Sitemap XML cho bài viết và sản phẩm
- Tìm kiếm bài viết
- Phân loại bài viết theo chuyên mục / tag
- Upload nhiều ảnh cho sản phẩm và bài viết
- Dashboard thống kê thực tế theo doanh thu / bài viết / coupon

## License

Dự án phục vụ mục đích học tập và phát triển nội bộ.
