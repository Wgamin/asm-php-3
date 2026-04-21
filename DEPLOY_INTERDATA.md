# Deploy Len Subdomain InterData

Tai lieu nay dung cho repo Laravel 12 hien tai khi dua len hosting InterData bang subdomain, uu tien kieu shared hosting/cPanel.

## 1. Yeu Cau

- PHP `8.2` hoac cao hon
- MySQL
- cPanel co tao duoc subdomain
- SSL cho subdomain
- Neu co Terminal/SSH thi deploy se gon hon

Repo nay can:

- `vendor/`
- `public/build/`
- `storage` symlink de phuc vu anh upload `/storage/...`

## 2. Cau Truc Thu Muc Khuyen Nghi

Dat source Laravel ngoai web root va tro document root cua subdomain vao thu muc `public`.

Vi du:

```text
/home/CPANEL_USER/apps/nongsanviet
/home/CPANEL_USER/apps/nongsanviet/public
```

Subdomain:

```text
https://shop-test.example.com
DocumentRoot => /home/CPANEL_USER/apps/nongsanviet/public
```

## 3. Tao Subdomain Tren InterData

Trong cPanel:

1. Vao `Domains`.
2. Tao domain/subdomain moi.
3. Nhap subdomain can dung.
4. Bo `Share document root`.
5. Dat `Document Root` tro thang vao thu muc `public` cua source Laravel.

Neu panel cua ban khong cho tro document root vao `public`, dung phuong an phu:

- Dat source Laravel ngoai `public_html`
- Copy noi dung trong thu muc `public/` ra document root cua subdomain
- Sua `index.php` de tro dung vao `../bootstrap/app.php` va `../vendor/autoload.php`

Tuy nhien, chi dung cach nay khi bat buoc.

## 4. Chuan Bi Source Truoc Khi Upload

Lam tren may local:

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

Sau do kiem tra:

- da co `vendor/`
- da co `public/build/`
- khong con file `public/hot`

`public/hot` phai bi xoa, neu khong site len host se de mat CSS/JS do van tro vao Vite dev server local.

## 5. Upload Len Hosting

Upload toan bo source len thu muc du an, vi du:

```text
/home/CPANEL_USER/apps/nongsanviet
```

Khong can upload:

- `node_modules/`
- `.git/`

Can upload:

- `vendor/`
- `public/build/`
- `storage/`
- `bootstrap/`
- `config/`
- `resources/`
- `routes/`
- `app/`
- `database/`

## 6. Tao File Moi Truong

Repo da co file mau:

```text
.env.interdata.example
```

Copy thanh `.env` tren hosting va sua cac gia tri toi thieu sau:

```env
APP_NAME="Nong San Viet"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://shop-test.example.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=interdata_db_name
DB_USERNAME=interdata_db_user
DB_PASSWORD=change_me
```

Chi de mot dong `APP_URL`.

## 7. Cau Hinh Payment Callback

Tat ca gateway phai tro ve dung subdomain dang deploy:

```env
VNPAY_RETURN_URL="${APP_URL}/payment/vnpay-return"

MOMO_REDIRECT_URL="${APP_URL}/payment/momo-return"
MOMO_IPN_URL="${APP_URL}/payment/momo/ipn"

ZALOPAY_CALLBACK_URL="${APP_URL}/payment/zalopay/callback"
ZALOPAY_REDIRECT_URL="${APP_URL}/payment/zalopay-return"
```

Neu dang test:

- `VNPay` dung sandbox
- `MoMo` dung test credentials
- `ZaloPay` dung sandbox app id/key

Neu len production:

- doi lai credential production cua tung cong
- doi endpoint neu nha cung cap yeu cau

## 8. Google Login

Neu dung dang nhap Google, redirect URI phai trung voi subdomain:

```env
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

Dong thoi cap nhat URI nay trong Google Cloud Console.

## 9. Chay Lenh Khoi Tao Neu Co Terminal/SSH

```bash
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Neu app da co du lieu san:

- import database truoc
- sau do khong can `migrate --force` neu khong muon chay migration moi

## 10. Neu Khong Co Terminal/SSH

Lam theo cach nay:

1. O local, chay:

```bash
php artisan key:generate --show
```

2. Copy key vao `.env` tren hosting:

```env
APP_KEY=base64:...
```

3. Tao database va import file `.sql` bang `phpMyAdmin`.
4. Build frontend tren local bang `npm run build` truoc khi upload.
5. Neu hosting khong tao duoc symlink cho `storage:link`, can nhieu nhat la support tu InterData hoac SSH.

## 11. Quyen Thu Muc

Can ghi duoc vao:

- `storage/`
- `bootstrap/cache/`

Neu site bao loi cache, session, upload, day la hai thu muc can check dau tien.

## 12. Sau Deploy Can Test Ngay

1. Trang chu co CSS binh thuong.
2. Anh san pham/news/avatar load duoc tu `/storage/...`.
3. Dang ky/dang nhap hoat dong.
4. Tao don `COD` thanh cong.
5. Payment `VNPay`, `MoMo`, `ZaloPay` redirect va quay ve dung subdomain.
6. Neu dung Google login, callback ve dung domain.

## 13. Checklist Nhanh

```text
[ ] PHP 8.2+
[ ] Subdomain da tro document root vao /public
[ ] SSL da bat
[ ] APP_ENV=production
[ ] APP_DEBUG=false
[ ] APP_URL dung subdomain https
[ ] DB da tao va da import/migrate
[ ] vendor da upload
[ ] public/build da upload
[ ] public/hot da xoa
[ ] storage:link da tao
[ ] storage va bootstrap/cache ghi duoc
[ ] callback payment tro dung subdomain
```

## 14. Loi Hay Gap

### Mat CSS

Thuong do con file `public/hot` hoac chua upload `public/build`.

### Anh upload khong hien

Thuong do chua `storage:link`.

### Payment khong quay ve

Thuong do:

- `APP_URL` sai
- callback/return URL van tro local
- domain chua SSL

### Loi 500 sau khi sua env

Chay lai:

```bash
php artisan optimize:clear
php artisan config:cache
```

## 15. Lenh Deploy Gon

Neu co SSH, day la bo lenh gon nhat:

```bash
cd /home/CPANEL_USER/apps/nongsanviet
php artisan optimize:clear
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
