@extends('layouts.client')

@section('title', 'Hồ sơ cá nhân')

@section('content')
{{-- Cập nhật x-data để bao gồm tab wishlist --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ tab: 'info' }">
    <div class="flex flex-col md:flex-row gap-8">
        
        {{-- Sidebar bên trái --}}
        <div class="w-full md:w-1/4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center sticky top-24">
                <div class="relative inline-block group">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=28a745&color=fff&size=128" 
                         class="h-32 w-32 rounded-full border-4 border-green-50 mx-auto shadow-sm group-hover:border-green-200 transition-all">
                </div>
                <h2 class="mt-4 font-bold text-xl text-gray-800">{{ $user->name }}</h2>
                <p class="text-gray-500 text-sm mb-6">{{ $user->email }}</p>
                
                <div class="space-y-1 text-left">
                    <button @click="tab = 'info'" 
                        :class="tab === 'info' ? 'bg-green-50 text-emerald-600 font-bold' : 'text-gray-600 hover:bg-gray-50'"
                        class="w-full flex items-center space-x-3 p-3 rounded-xl transition outline-none">
                        <i class="fas fa-user-circle w-5 text-center"></i>
                        <span>Thông tin cá nhân</span>
                    </button>

                    <button @click="tab = 'orders'" 
                        :class="tab === 'orders' ? 'bg-green-50 text-emerald-600 font-bold' : 'text-gray-600 hover:bg-gray-50'"
                        class="w-full flex items-center space-x-3 p-3 rounded-xl transition outline-none">
                        <i class="fas fa-shopping-bag w-5 text-center"></i>
                        <span>Đơn hàng của tôi</span>
                    </button>

                    {{-- BỔ SUNG TAB WISHLIST --}}
                    <button @click="tab = 'wishlist'" 
                        :class="tab === 'wishlist' ? 'bg-green-50 text-emerald-600 font-bold' : 'text-gray-600 hover:bg-gray-50'"
                        class="w-full flex items-center space-x-3 p-3 rounded-xl transition outline-none">
                        <i class="fas fa-heart w-5 text-center text-red-400"></i>
                        <span>Sản phẩm yêu thích</span>
                    </button>

                    <a href="{{ route('logout') }}" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                       class="w-full flex items-center space-x-3 p-3 rounded-xl text-red-500 hover:bg-red-50 transition">
                        <i class="fas fa-sign-out-alt w-5 text-center"></i>
                        <span>Đăng xuất</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                </div>
            </div>
        </div>

        {{-- Content bên phải --}}
        <div class="w-full md:w-3/4">
            
            {{-- Tab 1: Thông tin cá nhân --}}
            <div x-show="tab === 'info'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-gray-800">Cài đặt tài khoản</h3>
                    <span class="text-xs bg-gray-100 text-gray-500 px-3 py-1 rounded-full uppercase tracking-wider">Thành viên từ {{ $user->created_at->format('m/Y') }}</span>
                </div>

                @if(session('success'))
                    <div class="mx-8 mt-4 p-4 bg-emerald-100 text-emerald-700 rounded-xl flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST" class="p-8 space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700 ml-1">Họ và tên</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-50 outline-none transition">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700 ml-1">Địa chỉ Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-50 outline-none transition">
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-2xl space-y-4 border border-dashed border-gray-200">
                        <h4 class="font-bold text-gray-800 flex items-center">
                            <i class="fas fa-lock mr-2 text-emerald-600"></i> Thay đổi mật khẩu
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-500 uppercase ml-1">Mật khẩu mới</label>
                                <input type="password" name="password" placeholder="Để trống nếu không đổi"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white focus:border-emerald-500 outline-none transition">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-500 uppercase ml-1">Xác nhận mật khẩu</label>
                                <input type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu mới"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white focus:border-emerald-500 outline-none transition">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-10 rounded-xl shadow-lg shadow-emerald-200 transition transform hover:-translate-y-1">
                            Cập nhật hồ sơ
                        </button>
                    </div>
                </form>
            </div>

            {{-- Tab 2: Lịch sử đơn hàng --}}
            <div x-show="tab === 'orders'" x-cloak x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100">
                    <h3 class="font-bold text-lg text-gray-800">Lịch sử đơn hàng</h3>
                </div>
                
                <div class="p-6">
                    @if($orders->isEmpty())
                        <div class="text-center py-12">
                            <i class="fas fa-box-open text-gray-200 text-6xl mb-4"></i>
                            <p class="text-gray-500">Bạn chưa có đơn hàng nào.</p>
                            <a href="{{ route('products.index') }}" class="mt-4 inline-block text-emerald-600 font-bold hover:underline">Mua sắm ngay</a>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="text-xs uppercase text-gray-400 font-bold border-b border-gray-50">
                                        <th class="px-4 py-4">Mã đơn</th>
                                        <th class="px-4 py-4">Ngày đặt</th>
                                        <th class="px-4 py-4">Tổng tiền</th>
                                        <th class="px-4 py-4">Trạng thái</th>
                                        <th class="px-4 py-4 text-right">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($orders as $order)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-4 py-4 font-bold text-emerald-600">#{{ $order->id }}</td>
                                        <td class="px-4 py-4 text-gray-500 text-sm">{{ $order->created_at->format('d/m/Y') }}</td>
                                        <td class="px-4 py-4 font-bold text-gray-800">{{ number_format($order->total_amount) }}đ</td>
                                        <td class="px-4 py-4">
                                            @php
                                                $statusMap = [
                                                    'pending' => ['bg-amber-100 text-amber-600', 'Chờ xử lý'],
                                                    'processing' => ['bg-blue-100 text-blue-600', 'Đang giao'],
                                                    'completed' => ['bg-emerald-100 text-emerald-600', 'Đã nhận'],
                                                    'cancelled' => ['bg-red-100 text-red-600', 'Đã hủy'],
                                                ];
                                                $currentStatus = $statusMap[$order->status] ?? ['bg-gray-100 text-gray-600', $order->status];
                                            @endphp
                                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $currentStatus[0] }}">
                                                {{ $currentStatus[1] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-right">
                                            <button class="text-gray-400 hover:text-emerald-600 transition"><i class="fas fa-eye"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Tab 3: BỔ SUNG TAB SẢN PHẨM YÊU THÍCH --}}
            <div x-show="tab === 'wishlist'" x-cloak x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100">
                    <h3 class="font-bold text-lg text-gray-800">Sản phẩm bạn đã thích</h3>
                </div>
                <div class="p-6">
                    @php $wishlist = $user->wishlists; @endphp
                    @if($wishlist->isEmpty())
                        <div class="text-center py-12">
                            <i class="far fa-heart text-gray-200 text-6xl mb-4"></i>
                            <p class="text-gray-500">Danh sách yêu thích đang trống.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($wishlist as $product)
                                <x-product-card :product="$product" />
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection