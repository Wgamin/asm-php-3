@extends('layouts.client')

@section('title', 'Giới thiệu về chúng tôi')

@section('content')
<div class="bg-slate-50 min-h-screen">
    
    {{-- 1. Hero Banner --}}
    <div class="relative bg-emerald-700 text-white py-24 overflow-hidden">
        <div class="absolute inset-0 opacity-20">
            {{-- Đổi link ảnh nền thành ảnh cánh đồng hoặc nông trại của bạn --}}
            <img src="https://images.unsplash.com/photo-1500937386664-56d1dfef4428?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80" alt="Nông nghiệp" class="w-full h-full object-cover">
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-6 uppercase">Về [Tên Website]</h1>
            <p class="mt-4 max-w-2xl text-xl mx-auto text-emerald-100">
                Đồng hành cùng sự phát triển bền vững của nền nông nghiệp Việt Nam - Từ vật tư chất lượng đến nông sản sạch.
            </p>
        </div>
    </div>

    {{-- 2. Câu chuyện & Sứ mệnh --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div class="space-y-6">
                <h2 class="text-3xl font-bold text-slate-800">Câu chuyện của chúng tôi</h2>
                <div class="prose prose-emerald text-slate-600 leading-relaxed">
                    <p>
                        Xuất phát từ tình yêu với đất đai và khát khao nâng tầm giá trị nông nghiệp, <strong>[Tên Website]</strong> được ra đời bởi 4 người trẻ đầy tâm huyết. Chúng tôi thấu hiểu những giọt mồ hôi và sự vất vả của bà con nông dân đằng sau mỗi vụ mùa.
                    </p>
                    <p>
                        Mục tiêu lớn nhất của chúng tôi là tạo ra một "hệ sinh thái khép kín": Nơi bà con tìm được những vật tư nông nghiệp, phân bón, hạt giống tốt nhất để canh tác; đồng thời cũng là kênh phân phối uy tín đưa những nông sản sạch, chất lượng cao từ mồ hôi công sức của bà con đến tận tay người tiêu dùng.
                    </p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <img src="https://images.unsplash.com/photo-1464226184884-fa280b87c399?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Làm nông" class="rounded-2xl shadow-lg w-full h-48 object-cover transform translate-y-4">
                <img src="https://images.unsplash.com/photo-1595841696650-6e3e57f00305?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Nông sản" class="rounded-2xl shadow-lg w-full h-48 object-cover -translate-y-4">
            </div>
        </div>
    </div>

    {{-- 3. Lĩnh vực hoạt động (Sản phẩm & Vật tư) --}}
    <div class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-slate-800">Chúng tôi mang đến điều gì?</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Cột Vật tư --}}
                <div class="bg-emerald-50 rounded-[2rem] p-8 border border-emerald-100 hover:shadow-xl hover:shadow-emerald-100 transition duration-300">
                    <div class="w-16 h-16 bg-emerald-500 rounded-2xl flex items-center justify-center text-white text-2xl mb-6 shadow-lg shadow-emerald-200">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-4">Vật tư & Hỗ trợ nông nghiệp</h3>
                    <p class="text-slate-600 leading-relaxed mb-6">Cung cấp hạt giống chuẩn, phân bón hữu cơ, dụng cụ làm vườn và các giải pháp nông nghiệp công nghệ cao giúp bà con tiết kiệm sức lao động, tối ưu hóa năng suất mùa màng.</p>
                    <a href="#" class="text-emerald-600 font-bold hover:text-emerald-700 flex items-center gap-2">
                        Xem vật tư <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                {{-- Cột Nông sản --}}
                <div class="bg-amber-50 rounded-[2rem] p-8 border border-amber-100 hover:shadow-xl hover:shadow-amber-100 transition duration-300">
                    <div class="w-16 h-16 bg-amber-500 rounded-2xl flex items-center justify-center text-white text-2xl mb-6 shadow-lg shadow-amber-200">
                        <i class="fas fa-apple-alt"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-4">Nông sản sạch chất lượng</h3>
                    <p class="text-slate-600 leading-relaxed mb-6">Trực tiếp thu mua và phân phối các loại nông sản, đặc sản vùng miền đạt chuẩn an toàn vệ sinh thực phẩm. Cam kết tươi ngon, không hóa chất độc hại, tốt cho sức khỏe.</p>
                    <a href="{{ route('products.index') }}" class="text-amber-600 font-bold hover:text-amber-700 flex items-center gap-2">
                        Mua nông sản <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. Đội ngũ 4 thành viên --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-slate-800">Đội ngũ sáng lập</h2>
            <p class="text-slate-500 mt-4 max-w-2xl mx-auto">4 cá tính, 1 niềm đam mê mãnh liệt với nông nghiệp Việt Nam.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            
            {{-- Thành viên 1 --}}
            <div class="bg-white rounded-3xl p-6 text-center border border-slate-100 shadow-sm hover:shadow-lg transition duration-300 group">
                <div class="w-32 h-32 mx-auto rounded-full overflow-hidden border-4 border-emerald-50 mb-4 group-hover:border-emerald-200 transition">
                    <img src="https://ui-avatars.com/api/?name=Nguyen+A&background=10b981&color=fff&size=128" alt="Team member" class="w-full h-full object-cover">
                </div>
                <h3 class="font-bold text-lg text-slate-800">Nguyễn Văn A</h3>
                <p class="text-emerald-600 text-sm font-semibold mb-3">CEO & Founder</p>
                <p class="text-xs text-slate-500 italic">"Mang công nghệ và sự tận tâm vào từng mảnh vườn của bà con."</p>
            </div>

            {{-- Thành viên 2 --}}
            <div class="bg-white rounded-3xl p-6 text-center border border-slate-100 shadow-sm hover:shadow-lg transition duration-300 group">
                <div class="w-32 h-32 mx-auto rounded-full overflow-hidden border-4 border-emerald-50 mb-4 group-hover:border-emerald-200 transition">
                    <img src="https://ui-avatars.com/api/?name=Tran+B&background=10b981&color=fff&size=128" alt="Team member" class="w-full h-full object-cover">
                </div>
                <h3 class="font-bold text-lg text-slate-800">Trần Thị B</h3>
                <p class="text-emerald-600 text-sm font-semibold mb-3">Chuyên gia Nông nghiệp</p>
                <p class="text-xs text-slate-500 italic">"Kiểm định khắt khe từng hạt giống, từng túi phân bón trước khi giao."</p>
            </div>

            {{-- Thành viên 3 --}}
            <div class="bg-white rounded-3xl p-6 text-center border border-slate-100 shadow-sm hover:shadow-lg transition duration-300 group">
                <div class="w-32 h-32 mx-auto rounded-full overflow-hidden border-4 border-emerald-50 mb-4 group-hover:border-emerald-200 transition">
                    <img src="https://ui-avatars.com/api/?name=Le+C&background=10b981&color=fff&size=128" alt="Team member" class="w-full h-full object-cover">
                </div>
                <h3 class="font-bold text-lg text-slate-800">Lê Văn C</h3>
                <p class="text-emerald-600 text-sm font-semibold mb-3">Quản trị Hệ thống (IT)</p>
                <p class="text-xs text-slate-500 italic">"Xây dựng trải nghiệm mua sắm mượt mà, dễ dàng cho mọi lứa tuổi."</p>
            </div>

            {{-- Thành viên 4 --}}
            <div class="bg-white rounded-3xl p-6 text-center border border-slate-100 shadow-sm hover:shadow-lg transition duration-300 group">
                <div class="w-32 h-32 mx-auto rounded-full overflow-hidden border-4 border-emerald-50 mb-4 group-hover:border-emerald-200 transition">
                    <img src="https://ui-avatars.com/api/?name=Pham+D&background=10b981&color=fff&size=128" alt="Team member" class="w-full h-full object-cover">
                </div>
                <h3 class="font-bold text-lg text-slate-800">Phạm Thị D</h3>
                <p class="text-emerald-600 text-sm font-semibold mb-3">Marketing & CSKH</p>
                <p class="text-xs text-slate-500 italic">"Lắng nghe, thấu hiểu và giải đáp mọi thắc mắc của khách hàng 24/7."</p>
            </div>

        </div>
    </div>

</div>
@endsection