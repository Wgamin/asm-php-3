@extends('layouts.client')

@section('title', 'Đăng nhập')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center bg-slate-50 py-12 px-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
        <div class="p-8">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-slate-800">Chào mừng trở lại!</h2>
                <p class="text-slate-500 mt-2 text-sm">Vui lòng đăng nhập để tiếp tục mua sắm</p>
            </div>

            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Email của bạn</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full pl-10 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-sm font-semibold text-slate-700">Mật khẩu</label>
                        <a href="#" class="text-xs text-emerald-600 hover:underline">Quên mật khẩu?</a>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" required
                            class="w-full pl-10 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                    <label for="remember" class="ml-2 text-sm text-slate-600 italic">Ghi nhớ đăng nhập</label>
                </div>

                <button type="submit" 
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-emerald-200 transition-all transform hover:-translate-y-1">
                    Đăng nhập ngay
                </button>
            </form>

            <div class="mt-10 text-center pt-6 border-t border-slate-100">
                <p class="text-slate-600 text-sm">
                    Mới đến cửa hàng lần đầu? 
                    <a href="{{ route('register') }}" class="text-emerald-600 font-bold hover:underline">Tạo tài khoản mới</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection