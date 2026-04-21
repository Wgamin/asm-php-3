@extends('layouts.client')

@section('title', 'Xác nhận OTP')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center bg-slate-50 py-12 px-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
        <div class="p-8">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-slate-800">Nhập OTP để đổi mật khẩu</h2>
                <div class="mt-3 flex justify-center">
                    <x-admin-info>
                        Kiểm tra email của bạn, nhập mã OTP và đặt mật khẩu mới.
                    </x-admin-info>
                </div>
            </div>

            @if($errors->any())
                <div class="bg-red-50 text-red-600 text-sm p-3 rounded-xl mb-4 border border-red-100 text-center font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email', $email) }}" required
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none"
                            placeholder="ban@example.com">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Mã OTP</label>
                    <input type="text" name="otp" value="{{ old('otp') }}" inputmode="numeric" maxlength="6" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl tracking-[0.4em] text-center text-lg font-bold focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none"
                        placeholder="123456">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Mật khẩu mới</label>
                        <input type="password" name="password" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none"
                            placeholder="••••••••">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Xác nhận mật khẩu</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all outline-none"
                            placeholder="••••••••">
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-emerald-200 transition-all transform hover:-translate-y-0.5 active:scale-[0.98]">
                    Xác nhận OTP và đổi mật khẩu
                </button>
            </form>

            <div class="mt-8 text-center pt-6 border-t border-slate-100">
                <p class="text-slate-600 text-sm">
                    Chưa nhận được mã?
                    <a href="{{ route('password.request') }}" class="text-emerald-600 font-bold hover:underline">Gửi lại OTP</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
