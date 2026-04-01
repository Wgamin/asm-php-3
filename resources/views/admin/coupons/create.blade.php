@extends('admin.layouts.master')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Tạo coupon mới</h2>
                <p class="text-sm text-gray-500 mt-1">Thiết lập mã giảm giá để người dùng dùng tại trang checkout.</p>
            </div>
            <a href="{{ route('admin.coupons.index') }}" class="text-gray-500 hover:text-gray-700 font-medium">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại
            </a>
        </div>

        <form action="{{ route('admin.coupons.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            @include('admin.coupons._form')

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.coupons.index') }}" class="px-5 py-3 rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-50 transition">
                    Hủy
                </a>
                <button type="submit" class="px-5 py-3 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 transition">
                    Lưu coupon
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
