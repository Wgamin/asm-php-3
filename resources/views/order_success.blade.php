@extends('layouts.client')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center bg-slate-50 py-12 px-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl p-10 text-center border border-slate-100">
        {{-- Icon Thành Công --}}
        <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-check text-3xl"></i>
        </div>

        <h1 class="text-3xl font-extrabold text-slate-900 mb-2">Đặt hàng thành công!</h1>
        <p class="text-slate-500 mb-8">Cảm ơn bạn đã tin dùng nông sản Việt. Đơn hàng của bạn đang được hệ thống xử lý.</p>

        <div class="bg-slate-50 rounded-2xl p-4 mb-8">
            <p class="text-sm text-slate-400 uppercase tracking-widest font-bold mb-1">Mã đơn hàng</p>
            <p class="text-xl font-mono font-bold text-emerald-600">{{ $order_number }}</p>
        </div>

        <div class="space-y-3">
            <a href="{{ route('home') }}" class="block w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 rounded-xl transition shadow-lg shadow-emerald-100">
                Tiếp tục mua sắm
            </a>
            <a href="#" class="block w-full text-slate-500 hover:text-emerald-600 font-medium py-2 transition">
                Xem chi tiết đơn hàng
            </a>
        </div>
    </div>
</div>
@endsection