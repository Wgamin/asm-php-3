@extends('admin.layouts.master')

@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Quản lý coupon</h2>
            <p class="text-sm text-gray-500 mt-1">Tạo và theo dõi các mã giảm giá cho checkout.</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}" class="inline-flex items-center justify-center gap-2 bg-emerald-600 text-white font-bold px-5 py-3 rounded-xl hover:bg-emerald-700 transition">
            <i class="fas fa-ticket-alt"></i>
            <span>Tạo coupon</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                <tr>
                    <th class="px-6 py-4">Coupon</th>
                    <th class="px-6 py-4">Giảm giá</th>
                    <th class="px-6 py-4">Điều kiện</th>
                    <th class="px-6 py-4">Lượt dùng</th>
                    <th class="px-6 py-4">Trạng thái</th>
                    <th class="px-6 py-4 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($coupons as $coupon)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 align-top">
                        <div class="font-bold text-gray-800">{{ $coupon->code }}</div>
                        <div class="text-sm text-gray-600">{{ $coupon->name }}</div>
                        @if($coupon->description)
                        <div class="text-xs text-gray-400 mt-1 max-w-xs">{{ $coupon->description }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 align-top">
                        <div class="font-semibold text-gray-800">{{ $coupon->value_text }}</div>
                        <div class="text-xs text-gray-500">{{ $coupon->type_text }}</div>
                        @if($coupon->max_discount_amount)
                        <div class="text-xs text-gray-400 mt-1">Tối đa {{ number_format($coupon->max_discount_amount) }}đ</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 align-top text-sm text-gray-600">
                        <div>Đơn tối thiểu: <span class="font-semibold text-gray-800">{{ number_format($coupon->min_order_amount ?? 0) }}đ</span></div>
                        <div class="mt-1">
                            Hiệu lực:
                            <span class="font-semibold text-gray-800">
                                {{ $coupon->starts_at ? $coupon->starts_at->format('d/m/Y H:i') : 'Ngay' }}
                                -
                                {{ $coupon->expires_at ? $coupon->expires_at->format('d/m/Y H:i') : 'Không giới hạn' }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 align-top text-sm text-gray-600">
                        <div><span class="font-semibold text-gray-800">{{ $coupon->used_count }}</span> đã dùng</div>
                        <div class="mt-1">
                            Giới hạn:
                            <span class="font-semibold text-gray-800">{{ $coupon->usage_limit ?? 'Không giới hạn' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 align-top">
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $coupon->is_active ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' }}">
                            {{ $coupon->is_active ? 'Đang bật' : 'Tạm ngưng' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 align-top">
                        <div class="flex justify-end items-center gap-3">
                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-gray-400 hover:text-emerald-600 transition">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                            <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa coupon này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-600 transition">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center text-gray-400">
                        Chưa có coupon nào. Tạo mã đầu tiên để bắt đầu chiến dịch giảm giá.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $coupons->links() }}
    </div>
</div>
@endsection
