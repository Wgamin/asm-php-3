@extends('layouts.client')

@section('content')
<div class="bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        @php $cart = session()->get('cart', []); $total = 0; @endphp

        @if(count($cart) > 0)
        <form action="{{ route('order.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                {{-- Cột trái: Form nhập liệu --}}
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800">Thông tin giao hàng</h2>
                    <div class="space-y-4">
                        <input type="text" name="full_name" value="{{ Auth::user()->name }}" required placeholder="Họ tên" class="w-full px-5 py-3 rounded-2xl bg-gray-50 border-none">
                        <input type="text" name="phone" required placeholder="Số điện thoại" class="w-full px-5 py-3 rounded-2xl bg-gray-50 border-none">
                        <textarea name="address" required placeholder="Địa chỉ cụ thể" class="w-full px-5 py-3 rounded-2xl bg-gray-50 border-none"></textarea>
                    </div>
                </div>

                {{-- Cột phải: Đơn hàng (CRUD) --}}
                <div class="bg-slate-800 text-white p-8 rounded-3xl shadow-xl h-fit">
                    <h2 class="text-2xl font-bold mb-6">Đơn hàng của bạn</h2>
                    <div class="space-y-4 mb-6" id="cart-side-items">
                        @foreach($cart as $id => $details)
                            @php $total += $details['price'] * $details['quantity'] @endphp
                            <div class="flex justify-between items-center border-b border-slate-700 pb-4 cart-row" data-id="{{ $id }}">
                                <div>
                                    <p class="font-bold text-sm">{{ $details['name'] }}</p>
                                    <div class="flex items-center gap-3 mt-2">
                                        <button type="button" class="btn-update bg-slate-700 px-2 rounded" data-action="minus">-</button>
                                        <span class="qty-text">{{ $details['quantity'] }}</span>
                                        <button type="button" class="btn-update bg-slate-700 px-2 rounded" data-action="plus">+</button>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-emerald-400 font-bold">{{ number_format($details['price'] * $details['quantity']) }}đ</p>
                                    <button type="button" class="btn-remove text-xs text-slate-500 hover:text-red-400 mt-1">Xóa</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between text-xl font-bold pt-4 border-t border-slate-700">
                        <span>TỔNG CỘNG</span>
                        <span class="text-emerald-400">{{ number_format($total) }}đ</span>
                    </div>
                    <button type="submit" class="w-full bg-emerald-500 py-4 rounded-2xl mt-8 font-bold">XÁC NHẬN ĐẶT HÀNG</button>
                </div>
            </div>
        </form>
        @else
            <div class="text-center py-20 bg-white rounded-3xl">Giỏ hàng trống! <a href="/" class="text-emerald-500 underline">Mua sắm ngay</a></div>
        @endif
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Cập nhật số lượng
        $('.btn-update').click(function() {
            let row = $(this).closest('.cart-row');
            let id = row.data('id');
            let qty = parseInt(row.find('.qty-text').text());
            let action = $(this).data('action');
            let newQty = (action === 'plus') ? qty + 1 : (qty > 1 ? qty - 1 : 1);

            $.ajax({
                url: "{{ route('cart.update') }}",
                method: "PATCH",
                data: { _token: '{{ csrf_token() }}', id: id, quantity: newQty },
                success: function() { location.reload(); }
            });
        });

        // Xóa sản phẩm
        $('.btn-remove').click(function() {
            let id = $(this).closest('.cart-row').data('id');
            if(confirm('Xóa món này?')) {
                $.ajax({
                    url: "{{ route('cart.remove') }}",
                    method: "DELETE",
                    data: { _token: '{{ csrf_token() }}', id: id },
                    success: function() { location.reload(); }
                });
            }
        });
    });
</script>
@endsection