<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Đăng nhập') - Nông Sản Việt Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@400" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="admin-auth-shell">
    <main class="admin-auth-mesh min-h-screen lg:grid lg:grid-cols-[1.35fr_1fr]">
        <section class="relative hidden overflow-hidden bg-[linear-gradient(135deg,#206223,#3a7b3a)] lg:flex">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,0.18),transparent_26rem),radial-gradient(circle_at_bottom_right,rgba(255,220,194,0.15),transparent_20rem)]"></div>
            <img
                src="https://images.unsplash.com/photo-1516253593875-bd7ba052fbc5?auto=format&fit=crop&w=1400&q=80"
                alt="Nông sản xanh"
                class="absolute inset-0 h-full w-full object-cover mix-blend-overlay opacity-55"
            >
            <div class="relative z-10 flex h-full w-full flex-col justify-between p-14 text-white">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/14 backdrop-blur-md">
                        <i class="fas fa-leaf text-lg"></i>
                    </div>
                    <div>
                        <h1 class="admin-headline text-2xl font-extrabold tracking-[-0.04em]">Nông Sản Việt</h1>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-[0.2em] text-white/75">Admin Panel</p>
                    </div>
                </div>

                <div class="max-w-xl">
                    <h2 class="admin-headline text-5xl font-bold leading-[1.08] tracking-[-0.05em]">Quản trị thông minh cho hệ sinh thái nông sản bền vững.</h2>
                    <p class="mt-6 max-w-lg text-lg leading-8 text-white/82">
                        Theo dõi doanh thu, đơn hàng, khách hàng và vận hành kho vận trong một không gian quản trị thống nhất, nhẹ mắt và tập trung vào tác vụ.
                    </p>
                </div>

                <div class="grid max-w-md grid-cols-2 gap-5">
                    <div class="rounded-[1.2rem] border border-white/10 bg-white/10 p-5 backdrop-blur-md">
                        <p class="admin-headline text-2xl font-bold">12.5k+</p>
                        <p class="mt-2 text-sm text-white/72">Đơn hàng hoàn tất mỗi tháng</p>
                    </div>
                    <div class="rounded-[1.2rem] border border-white/10 bg-white/10 p-5 backdrop-blur-md">
                        <p class="admin-headline text-2xl font-bold">850</p>
                        <p class="mt-2 text-sm text-white/72">Tấn nông sản lưu chuyển mỗi ngày</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="flex min-h-screen items-center justify-center px-5 py-10 lg:px-10">
            <div class="w-full max-w-lg">
                @yield('content')
            </div>
        </section>
    </main>
</body>
</html>
