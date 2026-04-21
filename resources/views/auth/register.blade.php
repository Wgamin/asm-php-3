@extends('layouts.client')

@section('title', 'Đăng ký tài khoản')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center bg-slate-50 py-12 px-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
        <div class="p-8">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-slate-800">Tạo tài khoản</h2>
                <div class="mt-3 flex justify-center">
                    <x-admin-info>
                        Gia nhập cộng đồng nông sản sạch ngay hôm nay.
                    </x-admin-info>
                </div>
            </div>

            <form action="{{ route('register') }}" method="POST" class="space-y-5">
                @csrf
                
                {{-- Họ tên --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Họ và tên</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <i class="fas fa-user text-sm"></i>
                        </span>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none"
                            placeholder="Nguyễn Văn An">
                    </div>
                    @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Địa chỉ Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <i class="fas fa-envelope text-sm"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none"
                            placeholder="an.nguyen@example.com">
                    </div>
                    @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Mật khẩu --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Mật khẩu</label>
                        <input type="password" name="password" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none"
                            placeholder="••••••••">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Xác nhận</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none"
                            placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" 
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-emerald-200 transition-all transform hover:-translate-y-0.5 active:scale-[0.98]">
                    Đăng ký thành viên
                </button>
            </form>

            <div class="mt-8 text-center pt-6 border-t border-slate-100">
                <p class="text-slate-600 text-sm">
                    Bạn đã có tài khoản? 
                    <a href="{{ route('login') }}" class="text-emerald-600 font-bold hover:underline">Đăng nhập</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
