@extends('admin.layouts.master')

@section('title', 'Chỉnh sửa khách hàng')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="mb-8">
        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:text-emerald-600 flex items-center mb-2">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Chỉnh sửa thông tin</h1>
        <p class="text-sm text-gray-400">Đang chỉnh sửa tài khoản: <span class="text-emerald-600 font-medium">{{ $user->email }}</span></p>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="p-8 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 ml-1">Họ và tên</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-3 rounded-2xl bg-gray-50 border border-gray-100 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 ml-1">Địa chỉ Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-4 py-3 rounded-2xl bg-gray-50 border border-gray-100 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700 ml-1">Mật khẩu mới (Để trống nếu không đổi)</label>
                <input type="password" name="password"
                    class="w-full px-4 py-3 rounded-2xl bg-gray-50 border border-gray-100 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all"
                    placeholder="Nhập mật khẩu mới nếu muốn thay đổi">
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 rounded-2xl shadow-lg transition transform hover:-translate-y-0.5">
                    Cập nhật ngay
                </button>
                <a href="{{ route('admin.users.index') }}" class="px-8 py-4 bg-gray-100 text-gray-500 font-bold rounded-2xl hover:bg-gray-200 transition text-center">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>
@endsection