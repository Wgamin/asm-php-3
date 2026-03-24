@extends('admin.layouts.master')

@section('title', 'Thêm khách hàng mới')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:text-emerald-600 flex items-center mb-2">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Thêm khách hàng mới</h1>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="{{ route('admin.users.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Họ tên --}}
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 ml-1">Họ và tên</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-3 rounded-2xl bg-gray-50 border border-gray-100 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all"
                        placeholder="VD: Nguyễn Văn An">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Email --}}
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 ml-1">Địa chỉ Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 rounded-2xl bg-gray-50 border border-gray-100 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all"
                        placeholder="an.nguyen@example.com">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Mật khẩu --}}
            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700 ml-1">Mật khẩu khởi tạo</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-3 rounded-2xl bg-gray-50 border border-gray-100 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all"
                    placeholder="Tối thiểu 6 ký tự">
                <p class="text-[10px] text-gray-400 mt-1 italic">* Khách hàng có thể đổi lại mật khẩu sau khi đăng nhập.</p>
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-[#00C89D] hover:bg-[#00b38d] text-white font-bold py-4 rounded-2xl shadow-lg shadow-green-100 transition transform hover:-translate-y-0.5">
                    <i class="fas fa-save mr-2"></i> Lưu thông tin khách hàng
                </button>
            </div>
        </form>
    </div>
</div>
@endsection