@extends('admin.layouts.master')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Cập nhật coupon</h2>
                <p class="text-sm text-gray-500 mt-1">Chỉnh sửa điều kiện, hạn dùng hoặc trạng thái hoạt động.</p>
            </div>
            <a href="{{ route('admin.coupons.index') }}" class="text-gray-500 hover:text-gray-700 font-medium">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại
            </a>
        </div>

        <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            @include('admin.coupons._form', ['coupon' => $coupon])

            <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                <div class="text-sm text-gray-500">
                    Đã dùng {{ $coupon->used_count }} lượt
                    @if($coupon->usage_limit)
                        / {{ $coupon->usage_limit }} lượt
                    @endif
                </div>
                <button type="submit" class="px-5 py-3 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 transition">
                    Cập nhật coupon
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
