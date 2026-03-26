@extends('layouts.client')

@section('title', 'Hồ sơ cá nhân')

@section('content')
{{-- Thêm x-data để quản lý chuyển Tab --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ tab: 'info' }">
    <div class="flex flex-col md:flex-row gap-8">
        
        <div class="w-full md:w-1/4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
                <div class="relative inline-block">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=28a745&color=fff&size=128" 
                         class="h-32 w-32 rounded-full border-4 border-green-50 mx-auto shadow-sm">
                </div>
                <h2 class="mt-4 font-bold text-xl text-gray-800">{{ $user->name }}</h2>
                <p class="text-gray-500 text-sm mb-6">{{ $user->email }}</p>
                
                <div class="space-y-1 text-left">
                    <button @click="tab = 'info'" 
                        :class="tab === 'info' ? 'bg-green-50 text-primary-green font-bold' : 'text-gray-600 hover:bg-gray-50'"
                        class="w-full flex items-center space-x-3 p-3 rounded-xl transition outline-none">
                        <i class="fas fa-user-circle w-5 text-center"></i>
                        <span>Thông tin cá nhân</span>
                    </button>

                    <button @click="tab = 'orders'" 
                        :class="tab === 'orders' ? 'bg-green-50 text-primary-green font-bold' : 'text-gray-600 hover:bg-gray-50'"
                        class="w-full flex items-center space-x-3 p-3 rounded-xl transition outline-none">
                        <i class="fas fa-shopping-bag w-5 text-center"></i>
                        <span>Đơn hàng của tôi</span>
                    </button>

                    <a href="#" class="flex items-center space-x-3 p-3 rounded-xl text-gray-600 hover:bg-gray-50 transition">
                        <i class="fas fa-map-marker-alt w-5 text-center"></i>
                        <span>Sổ địa chỉ</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="w-full md:w-3/4">
            
            <div x-show="tab === 'info'" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
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
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary-green focus:ring-4 focus:ring-green-50 outline-none transition">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700 ml-1">Địa chỉ Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary-green focus:ring-4 focus:ring-green-50 outline-none transition">
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    <div class="bg-gray-50 p-6 rounded-2xl space-y-4">
                        <h4 class="font-bold text-gray-800 flex items-center">
                            <i class="fas fa-lock mr-2 text-primary-green"></i> Thay đổi mật khẩu
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-500 uppercase ml-1">Mật khẩu mới</label>
                                <input type="password" name="password" placeholder="Để trống nếu không đổi"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white focus:border-primary-green outline-none transition">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-500 uppercase ml-1">Xác nhận mật khẩu</label>
                                <input type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu mới"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white focus:border-primary-green outline-none transition">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="bg-[#28a745] hover:bg-green-700 text-white font-bold py-3 px-10 rounded-xl shadow-lg shadow-green-200 transition transform hover:-translate-y-1">
                            Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>

            <div x-show="tab === 'orders'" x-cloak class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100">
                    <h3 class="font-bold text-lg text-gray-800">Lịch sử đơn hàng</h3>
                </div>
                
                <div class="p-6">
                    @if($orders->isEmpty())
                        <div class="text-center py-12">
                            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-box-open text-gray-300 text-3xl"></i>
                            </div>
                            <p class="text-gray-500">Bạn chưa có đơn hàng nào.</p>
                            <a href="{{ route('home') }}" class="mt-4 inline-block text-emerald-600 font-bold">Mua sắm ngay</a>
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
                                        <td class="px-4 py-4 font-bold text-emerald-600">{{ $order->order_number }}</td>
                                        <td class="px-4 py-4 text-gray-500">{{ $order->created_at->format('d/m/Y') }}</td>
                                        <td class="px-4 py-4 font-bold text-gray-800">{{ number_format($order->total_amount) }}đ</td>
                                        <td class="px-4 py-4">
                                            @php
                                                $statusClass = match($order->status) {
                                                    'pending' => 'bg-amber-100 text-amber-600',
                                                    'processing' => 'bg-blue-100 text-blue-600',
                                                    'completed' => 'bg-emerald-100 text-emerald-600',
                                                    'cancelled' => 'bg-red-100 text-red-600',
                                                    default => 'bg-gray-100 text-gray-600',
                                                };
                                            @endphp
                                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusClass }}">
                                                {{ $order->status_text }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-right">
                                            <a href="#" class="text-gray-400 hover:text-emerald-600 transition">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Style bổ sung để tránh giật x-cloak --}}
<style>
    [x-cloak] { display: none !important; }
</style>
@endsection