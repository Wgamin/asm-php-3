@extends('admin.layouts.master')

@section('title', 'Cài đặt hệ thống')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Cài đặt hệ thống</h1>
        <p class="text-sm text-gray-500">Quản lý thông tin tài khoản quản trị tối cao.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-600 rounded-2xl flex items-center">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8 border-b border-gray-50 bg-gray-50/30">
            <div class="flex items-center gap-6">
                <div class="relative">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($admin->name) }}&background=10b981&color=fff&size=128" 
                         class="w-24 h-24 rounded-3xl shadow-inner border-4 border-white">
                    <div class="absolute -bottom-2 -right-2 bg-emerald-500 text-white p-2 rounded-xl text-xs shadow-lg">
                        <i class="fas fa-shield-alt"></i> Root
                    </div>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $admin->name }}</h2>
                    <p class="text-sm text-emerald-600 font-medium italic">Administrator</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" class="p-8 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700">Tên hiển thị</label>
                    <input type="text" name="name" value="{{ old('name', $admin->name) }}" 
                        class="w-full px-5 py-3.5 rounded-2xl bg-gray-50 border border-transparent focus:bg-white focus:ring-2 focus:ring-emerald-500 transition-all outline-none">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700">Email quản trị</label>
                    <input type="email" name="email" value="{{ old('email', $admin->email) }}" 
                        class="w-full px-5 py-3.5 rounded-2xl bg-gray-50 border border-transparent focus:bg-white focus:ring-2 focus:ring-emerald-500 transition-all outline-none">
                </div>
            </div>

            <hr class="border-gray-50">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700">Mật khẩu mới</label>
                    <input type="password" name="password" placeholder="Để trống nếu không đổi"
                        class="w-full px-5 py-3.5 rounded-2xl bg-gray-50 border border-transparent focus:bg-white focus:ring-2 focus:ring-emerald-500 transition-all outline-none">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700">Xác nhận mật khẩu</label>
                    <input type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu mới"
                        class="w-full px-5 py-3.5 rounded-2xl bg-gray-50 border border-transparent focus:bg-white focus:ring-2 focus:ring-emerald-500 transition-all outline-none">
                </div>
            </div>

            <div class="pt-6 flex justify-end">
                <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-10 py-4 rounded-2xl font-bold shadow-lg transition transform hover:-translate-y-1 active:scale-95">
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection