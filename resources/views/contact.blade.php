@extends('layouts.client')

@section('title', 'Liên hệ - Nông Sản Sạch')

@section('content')
<div class="bg-slate-50 py-12">
    <div class="container mx-auto px-4">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">
            
            {{-- Cột 1: Thông tin bên lề --}}
            <div class="space-y-6">
                <div>
                    <h1 class="text-4xl font-extrabold text-slate-800 mb-4">Liên hệ</h1>
                    <p class="text-slate-500 leading-relaxed">
                        Bạn có câu hỏi về sản phẩm hoặc cần hỗ trợ về đơn hàng? Đừng ngần ngại gửi tin nhắn cho chúng tôi.
                    </p>
                </div>

                <div class="space-y-4">
                    {{-- Địa chỉ --}}
                    <div class="flex items-center gap-4 p-5 bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition">
                        <div class="w-12 h-12 bg-emerald-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-100">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Địa chỉ</p>
                            <p class="text-slate-700 font-medium">Trịnh Văn Bô, Nam Từ Liêm, Hà Nội</p>
                        </div>
                    </div>

                    {{-- Điện thoại --}}
                    <div class="flex items-center gap-4 p-5 bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition">
                        <div class="w-12 h-12 bg-blue-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-blue-100">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Điện thoại</p>
                            <p class="text-slate-700 font-medium">0987 654 321</p>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="flex items-center gap-4 p-5 bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition">
                        <div class="w-12 h-12 bg-amber-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-amber-100">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Email</p>
                            <p class="text-slate-700 font-medium">lienhe@nongsansach.vn</p>
                        </div>
                    </div>
                </div>

                {{-- Mạng xã hội --}}
                <div class="pt-4">
                    <p class="text-sm font-bold text-slate-700 mb-4">Theo dõi chúng tôi:</p>
                    <div class="flex gap-3">
                        <a href="#" class="w-10 h-10 rounded-xl bg-slate-200 flex items-center justify-center text-slate-600 hover:bg-emerald-500 hover:text-white transition"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="w-10 h-10 rounded-xl bg-slate-200 flex items-center justify-center text-slate-600 hover:bg-emerald-500 hover:text-white transition"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="w-10 h-10 rounded-xl bg-slate-200 flex items-center justify-center text-slate-600 hover:bg-emerald-500 hover:text-white transition"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>

            {{-- Cột 2: Form liên hệ --}}
            <div class="lg:col-span-2">
                <div class="bg-white p-8 md:p-12 rounded-[40px] shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden">
                    {{-- Trang trí một chút --}}
                    <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-bl-full -mr-16 -mt-16"></div>

                    <form action="#" class="relative z-10 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 ml-1">Họ và tên</label>
                                <input type="text" placeholder="Nguyễn Văn A" class="w-full px-6 py-4 rounded-2xl bg-slate-50 border border-transparent focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 ml-1">Địa chỉ Email</label>
                                <input type="email" placeholder="email@gmail.com" class="w-full px-6 py-4 rounded-2xl bg-slate-50 border border-transparent focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700 ml-1">Chủ đề</label>
                            <input type="text" placeholder="Bạn cần hỗ trợ về vấn đề gì?" class="w-full px-6 py-4 rounded-2xl bg-slate-50 border border-transparent focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700 ml-1">Nội dung tin nhắn</label>
                            <textarea rows="5" placeholder="Viết tin nhắn của bạn ở đây..." class="w-full px-6 py-4 rounded-2xl bg-slate-50 border border-transparent focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none transition-all resize-none"></textarea>
                        </div>

                        <button type="button" class="group bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 px-10 rounded-2xl shadow-lg shadow-emerald-200 transition-all transform hover:-translate-y-1 flex items-center gap-3">
                            Gửi yêu cầu ngay
                            <i class="fas fa-paper-plane group-hover:translate-x-1 group-hover:-translate-y-1 transition"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Phần Bản đồ (Full width bên dưới) --}}
        <div class="mt-16 rounded-[40px] overflow-hidden shadow-inner border-8 border-white">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3723.863981044384!2d105.7445984153321!3d21.0381277928332!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x313454b991d80fd5%3A0x536c0533516a927!2zVHLhu4tuaCBWxINuIELDtCwgTmFtIFThu6sgTGnDqm0sIEjDoCBO4buZaSwgVmnhu4d0IE5hbQ!5e0!3m2!1svi!2s!4v1647854245632!5m2!1svi!2s" 
                class="w-full h-[450px]" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>
        </div>
    </div>
</div>
@endsection