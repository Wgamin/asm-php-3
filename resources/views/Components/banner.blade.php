<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<style>
    .home-swiper {
        width: 100%;
        height: 450px; /* Bạn có thể chỉnh lại chiều cao tùy ý */
    }
    .home-swiper .swiper-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    /* Tùy chỉnh màu nút điều hướng */
    .swiper-button-next, .swiper-button-prev {
        color: #10b981 !important; /* Màu xanh emerald */
    }
    .swiper-pagination-bullet-active {
        background: #10b981 !important;
    }
</style>

<div class="swiper home-swiper rounded-2xl overflow-hidden shadow-lg mb-8">
    <div class="swiper-wrapper">
        <div class="swiper-slide relative">
            <img src="https://images.unsplash.com/photo-1500651230702-0e2d8a49d4ad?q=80&w=2070" alt="Banner 1">
            <div class="absolute inset-0 bg-black/30 flex items-center p-12">
                <div class="max-w-xl text-white">
                    <h2 class="text-4xl font-bold mb-4">Nông Sản Sạch Tươi Ngon</h2>
                    <p class="mb-6 opacity-90">Cam kết chất lượng từ vườn đến bàn ăn của gia đình bạn.</p>
                    <a href="/products" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-lg font-medium transition">Mua Ngay</a>
                </div>
            </div>
        </div>

        <div class="swiper-slide relative">
            <img src="https://images.unsplash.com/photo-1464226184884-fa280b87c399?q=80&w=2070" alt="Banner 2">
            <div class="absolute inset-0 bg-black/30 flex items-center p-12">
                <div class="max-w-xl text-white">
                    <h2 class="text-4xl font-bold mb-4">Ưu Đãi Lên Đến 30%</h2>
                    <p class="mb-6 opacity-90">Dành riêng cho khách hàng mới đăng ký trong tháng này.</p>
                    <a href="/register" class="bg-white text-emerald-600 hover:bg-gray-100 px-6 py-3 rounded-lg font-medium transition">Đăng Ký Ngay</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="swiper-pagination"></div>
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const swiper = new Swiper('.home-swiper', {
            // Cấu hình cơ bản
            loop: true,
            grabCursor: true, // Hiển thị bàn tay khi di chuột vào
            speed: 1000,      // Tốc độ chuyển cảnh (1 giây)

            // Chạy tự động
            autoplay: {
                delay: 3000,                   // 3 giây chuyển 1 lần
                disableOnInteraction: false,   // Quan trọng: Khách click vào nút thì sau đó vẫn tự chạy tiếp
                pauseOnMouseEnter: true,       // Di chuột vào thì dừng (để khách đọc nội dung)
            },

            // Hiệu ứng Fade (Mờ dần)
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },

            // Phân trang & Điều hướng
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
                dynamicBullets: true, // Chấm nhỏ chấm to nhìn hiện đại hơn
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    });
</script>