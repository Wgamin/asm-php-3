# Prompt Cho Stitch - Redesign Full Admin Panel Nông Sản Việt

Sao chép toàn bộ prompt bên dưới vào [stitch.withgoogle.com](https://stitch.withgoogle.com/) để yêu cầu vẽ lại full giao diện admin của dự án.

---

## Prompt

Thiết kế lại **toàn bộ giao diện Admin Panel** cho một website thương mại điện tử nông sản tên **Nông Sản Việt**.

Mục tiêu:
- thiết kế lại **full bộ admin**, không chỉ 1 dashboard
- phải có **design system thống nhất**, nhìn như sản phẩm thương mại thật do team product/design làm ra
- ưu tiên **UX cho vận hành**: rõ ràng, nhanh thao tác, đọc dữ liệu tốt, nhiều bảng dữ liệu nhưng không rối
- admin này phục vụ quản lý sản phẩm, biến thể, đơn hàng, khách hàng, coupon, tin tức, chat hỗ trợ, cài đặt hệ thống
- giao diện phải mang cảm giác **professional commerce operations dashboard**, không phải landing page, không quá màu mè, không “AI slop”, không giống template student project

### Phong cách thiết kế mong muốn

Tạo một admin panel theo phong cách:
- hiện đại, gọn, nhiều dữ liệu nhưng dễ đọc
- bố cục theo kiểu **SaaS dashboard / e-commerce backoffice**
- ít màu nhưng có điểm nhấn rõ
- tone màu nên liên quan tới nông sản sạch:
  - nền sáng, sạch, dịu mắt
  - trung tính: trắng, slate, gray rất nhạt
  - accent chính: xanh lá chuyên nghiệp, không neon
  - có thể dùng amber rất nhẹ cho warning / doanh thu / trạng thái chờ
- tránh:
  - gradient lòe loẹt
  - bo góc quá mức
  - quá nhiều shadow dày
  - icon hoạt hình
  - layout “marketing website”

### Cảm giác thương hiệu cần đạt

Admin này phải cho cảm giác:
- tin cậy
- rõ ràng
- kiểm soát tốt dữ liệu
- thao tác nhanh cho người vận hành
- giống dashboard của một hệ thống bán hàng đang chạy thật

### Design system cần dựng

Hãy tạo một design system thống nhất cho toàn bộ admin:
- typography hierarchy rõ ràng
- spacing system 8pt hoặc tương tự
- border radius vừa phải
- card, table, form input, search bar, select, textarea, modal, dropdown, badge, pagination, tabs, toast, empty state, confirm dialog
- icon set thống nhất
- trạng thái màu chuẩn:
  - success
  - warning
  - danger
  - info
  - neutral
- status badge cho:
  - pending
  - processing
  - shipping
  - completed
  - cancelled
  - paid
  - unpaid
  - draft
  - published

### Layout tổng của admin

Thiết kế full shell admin gồm:
- sidebar trái cố định
- topbar phía trên
- content area rộng cho table/form
- responsive desktop-first
- tablet dùng được
- mobile ít nhất không vỡ layout

Sidebar cần có:
- logo Nông Sản Việt
- avatar admin / profile mini
- navigation group rõ ràng
- menu item có icon
- active state đẹp, dễ nhận biết
- collapse/expand sidebar state

Topbar cần có:
- breadcrumb
- search toàn cục
- chuông thông báo realtime đơn hàng mới
- dropdown user menu

### Không thiết kế public site

Chỉ làm **admin panel**. Không thiết kế storefront/public site.

---

## Các màn hình cần thiết kế lại đầy đủ

Thiết kế **tất cả các trang bên dưới**, mỗi trang phải cùng một design language.

### 1. Admin Auth

Thiết kế 2 màn hình:
- Admin Login
- Admin Register

Yêu cầu:
- giao diện gọn
- tập trung vào form
- có brand panel hoặc visual nhẹ
- input rõ ràng, error state rõ
- nút primary mạnh
- có remember me / quên mật khẩu nếu phù hợp về UI

---

### 2. Dashboard Tổng Quan

Thiết kế lại dashboard admin với dữ liệu thật, bao gồm:
- số lượng người dùng
- đơn hàng hôm nay
- doanh thu tháng
- lợi nhuận tháng
- biểu đồ doanh thu 7 ngày gần nhất
- top sản phẩm bán chạy
- thành viên mới
- đơn hàng gần đây

Yêu cầu UX:
- KPI cards phải rõ, dễ so sánh
- dùng chart area/bar phù hợp
- bảng đơn hàng gần đây gọn, đẹp
- có quick actions nếu hợp lý
- cho cảm giác “ops center”

---

### 3. Quản Lý Sản Phẩm

Thiết kế full bộ màn hình:
- Product Index
- Product Create
- Product Edit
- Product Detail / Preview

#### Product Index
Bao gồm:
- header trang
- nút thêm mới
- import file Excel/CSV
- khối hướng dẫn import
- filter:
  - keyword
  - min price
  - max price
- bảng dữ liệu sản phẩm

Trong table phải có:
- ảnh sản phẩm
- tên sản phẩm
- loại sản phẩm: thường / biến thể
- giá
- danh mục
- hành động: xem / sửa / xóa
- trạng thái empty
- pagination

#### Product Create / Edit
Đây là màn rất quan trọng. Thiết kế dạng form lớn nhiều section:
- thông tin cơ bản
  - tên sản phẩm
  - danh mục
  - loại sản phẩm
  - giá
  - giá khuyến mãi
  - khối lượng
  - tồn kho
- mô tả ngắn
- mô tả chi tiết / content editor area
- upload ảnh đại diện
- gallery ảnh
- khu quản lý biến thể
  - option name / option value
  - variant sku
  - variant price
  - variant sale price
  - variant stock
  - variant image
- submit / save draft / cancel

Yêu cầu UX:
- form phải chia section rõ ràng
- sidebar summary hoặc sticky action bar nếu phù hợp
- màn hình edit phải dễ thao tác với sản phẩm có nhiều biến thể
- phần variant phải nhìn như tool quản lý SKU thật

#### Product Detail
Trang này dùng để admin xem nhanh:
- hình ảnh
- thông tin chính
- danh mục
- giá
- biến thể
- tồn kho
- mô tả
- ảnh gallery

---

### 4. Quản Lý Danh Mục

Thiết kế trang Category Management:
- danh sách category
- có thể hiển thị cấu trúc phân cấp
- create/edit/delete
- modal hoặc drawer chỉnh sửa nhanh

Yêu cầu:
- nhìn rõ parent-child relation
- dễ quản lý nhiều danh mục

---

### 5. Quản Lý Thuộc Tính Và Giá Trị Thuộc Tính

Thiết kế trang Attributes:
- danh sách attribute
- thêm attribute
- sửa attribute
- xóa attribute
- quản lý attribute values ngay trong cùng màn hoặc side panel

Ví dụ:
- Màu sắc -> đỏ, xanh, vàng
- Kích cỡ -> S, M, L

Yêu cầu:
- UX phải tốt cho quản lý biến thể sản phẩm
- dùng nested card / expandable rows / split panel nếu hợp lý

---

### 6. Quản Lý Coupon

Thiết kế full bộ:
- Coupon Index
- Coupon Create
- Coupon Edit

Thông tin coupon:
- mã coupon
- loại giảm giá
- giá trị giảm
- điều kiện tối thiểu
- thời gian bắt đầu / kết thúc
- giới hạn số lượng
- trạng thái

Yêu cầu:
- form rõ ràng
- danh sách coupon phải đọc nhanh được coupon nào còn hiệu lực
- có badge trạng thái đẹp

---

### 7. Quản Lý Tin Tức

Thiết kế full bộ:
- News Index
- News Create
- News Edit

Mỗi bài viết có:
- title
- excerpt
- content
- slug
- meta title
- meta description
- image
- status: draft/published
- published_at

Yêu cầu:
- nhìn giống CMS mini
- có editor area lớn
- có preview meta/SEO block nếu hợp lý

---

### 8. Quản Lý Người Dùng

Thiết kế full bộ:
- User Index
- User Create
- User Edit

Thông tin:
- avatar
- name
- email
- phone
- role
- trạng thái

Yêu cầu:
- table quản lý user dễ đọc
- search/filter hợp lý
- form edit user rõ và gọn

---

### 9. Quản Lý Vai Trò / Roles

Thiết kế:
- Roles Index
- Role Create

Ngay cả khi module này chưa hoàn thiện hoàn toàn, vẫn thiết kế như một màn hình quản trị quyền cơ bản:
- danh sách role
- số user theo role
- quyền cơ bản / permission group mockup rõ ràng

---

### 10. Quản Lý Đơn Hàng

Thiết kế full bộ:
- Order Index
- Order Detail

Đây là module cực quan trọng, cần thiết kế rất kỹ.

#### Order Index
Phải có:
- filter theo:
  - mã đơn
  - khách hàng
  - trạng thái
  - ngày đặt từ / đến
- table danh sách đơn

Cột gợi ý:
- mã đơn
- khách hàng
- tổng tiền
- payment
- shipment
- trạng thái
- ngày đặt
- thao tác

Yêu cầu:
- bảng phải data-dense nhưng không rối
- badge trạng thái rõ
- thao tác xem chi tiết / xóa / lọc nhanh hợp lý
- hỗ trợ màn hình laptop tốt

#### Order Detail
Phải có các block:
- thông tin đơn hàng
- trạng thái đơn hàng
- thông tin khách hàng / giao hàng
- danh sách sản phẩm trong đơn
- tạm tính
- giảm giá
- phí ship
- tổng cộng
- payment info
- shipment info
- tracking code nếu có
- lịch sử trạng thái đơn hàng
- webhook / sync history

Yêu cầu:
- chia layout thành nhiều card rõ ràng
- tạo cảm giác người vận hành đọc 1 lần là hiểu ngay toàn bộ đơn
- có action đổi trạng thái đơn

---

### 11. Chat Hỗ Trợ Admin

Thiết kế trang admin chat support:
- danh sách cuộc hội thoại bên trái
- khung chat bên phải
- realtime / polling feel
- trạng thái đang chọn user nào

Yêu cầu:
- chat phải rõ, gọn, đúng kiểu backoffice support
- dễ đọc nhiều khách
- phân biệt tin nhắn từ khách và từ admin

---

### 12. Cài Đặt Hệ Thống

Thiết kế trang settings gồm:
- thông tin admin root
- tên hiển thị
- email
- đổi mật khẩu
- khu cấu hình kho mặc định nếu hợp lý
- khu cấu hình hệ thống cơ bản

Yêu cầu:
- feel giống trang system settings thật
- chia section rõ
- bảo mật / account / warehouse setting nên tách block

---

## Những thành phần chung bắt buộc phải có trong toàn bộ admin

Thiết kế đồng bộ cho:
- page header
- breadcrumb
- table header
- search input
- filter panel
- dropdown
- modal xác nhận xóa
- toast success/error
- empty state
- loading state / skeleton
- pagination
- tabs
- status badge
- chips / tags
- uploader area
- section divider
- action bar sticky

---

## Trạng thái UX phải thể hiện

Mỗi nhóm màn hình cần có đủ state:
- default state
- loading state
- empty state
- success state
- error state
- destructive confirm state
- validation error state

Ví dụ:
- danh sách sản phẩm không có dữ liệu
- không tìm thấy đơn hàng
- import thất bại một số dòng
- chat chưa có hội thoại
- dashboard loading

---

## Ngôn ngữ hiển thị

Toàn bộ text UI bằng **tiếng Việt có dấu**, ngắn gọn, chuẩn hành chính thương mại điện tử.

Tránh:
- text tiếng Anh lẫn tiếng Việt
- text placeholder vô nghĩa
- wording kiểu AI
- microcopy dài dòng

---

## Data model và ngữ cảnh nghiệp vụ để Stitch hiểu đúng

Đây là admin của một website bán nông sản online, có các nghiệp vụ:
- sản phẩm thường và sản phẩm biến thể
- ảnh đại diện và gallery ảnh
- cart, coupon, checkout, VNPay
- đơn hàng, payment, shipment
- địa chỉ người dùng
- review sản phẩm
- tin tức/blog
- chat hỗ trợ
- dashboard dùng dữ liệu thật

Các thực thể chính:
- users
- categories
- products
- product_variants
- product_images
- attributes
- attribute_values
- orders
- order_items
- payments
- shipments
- order_status_histories
- coupons
- news_articles
- product_reviews
- chat_messages
- warehouses

---

## Tone của thiết kế

Thiết kế cần ra cảm giác:
- chuyên nghiệp
- tinh gọn
- dữ liệu rõ
- đáng tin cậy
- đúng ngữ cảnh vận hành một hệ thống bán hàng nông sản

Không được làm theo style:
- playful startup
- banking app
- luxury fashion
- social app
- landing page marketing

Đây phải là **admin panel để vận hành thật**.

---

## Output mong muốn từ Stitch

Hãy tạo:
- full redesign admin UI
- design system cơ bản
- toàn bộ screen quan trọng kể trên
- mỗi màn có cùng một visual language
- ưu tiên desktop layout trước
- tất cả page phải nhìn như thuộc cùng một sản phẩm

Nếu cần gom màn hình theo flow, hãy chia theo nhóm:
1. Auth
2. Dashboard
3. Product management
4. Order management
5. Customer management
6. Content management
7. Support & notification
8. System settings

Nhưng cuối cùng vẫn phải cover **toàn bộ các trang admin**.

---

## Chỉ dẫn cuối cùng cho Stitch

Không chỉ đưa ra 1-2 màn hình mẫu. Hãy thiết kế **full bộ admin panel hoàn chỉnh**, với đầy đủ page list, components, tables, forms, states, filters và detail pages như mô tả ở trên.

Thiết kế phải đủ tốt để có thể dùng làm base cho việc code lại giao diện admin thực tế.

---

## Prompt Ngắn Hơn Nếu Cần

Nếu Stitch bị quá tải, dùng bản rút gọn này:

> Redesign the full Admin Panel UI for an agricultural e-commerce platform called “Nông Sản Việt”. Create a complete professional backoffice dashboard with a unified design system and all admin screens: admin auth, dashboard, product list/create/edit/detail, product import, categories, attributes and attribute values, coupons, news CMS, users, roles, orders list/detail with payment/shipment/status history, support chat, realtime order notifications, and system settings. The style must feel like a real commerce operations tool, clean, modern, data-dense, trustworthy, Vietnamese language, desktop-first, responsive, not flashy, not marketing-like, and not generic AI template design. Use a light neutral base with professional green agricultural accents. Include all states: loading, empty, success, error, validation, confirm delete, pagination, filters, table actions, large forms, sticky action bars, and clear status badges.

