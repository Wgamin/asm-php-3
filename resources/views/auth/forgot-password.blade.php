@extends('layouts.client')

@section('title', 'Quên mật khẩu')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center bg-slate-50 py-12 px-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
        <div class="p-8">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-slate-800">Quên mật khẩu?</h2>
                <div class="mt-3 flex justify-center">
                    <x-admin-info>
                        Nhập email đã đăng ký, hệ thống sẽ gửi mã OTP về hộp thư của bạn.
                    </x-admin-info>
                </div>
            </div>

            @if($errors->any())
                <div class="bg-red-50 text-red-600 text-sm p-3 rounded-xl mb-4 border border-red-100 text-center font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Email nhận OTP</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full pl-10 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all"
                            placeholder="ban@example.com">
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-emerald-200 transition-all transform hover:-translate-y-1">
                    Gửi mã OTP
                </button>
            </form>

            <div class="mt-8 text-center pt-6 border-t border-slate-100">
                <p class="text-slate-600 text-sm">
                    Nhớ mật khẩu rồi?
                    <a href="{{ route('login') }}" class="text-emerald-600 font-bold hover:underline">Quay lại đăng nhập</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
