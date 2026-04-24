@extends('layouts.client')

@section('title', 'Hồ sơ cá nhân')

@php
    $activeTab = old('active_tab', session('profile_tab', request('tab', 'info')));
    $addressIdFromOld = (int) old('address_id', 0);
    
    // Đảm bảo $addresses luôn tồn tại dưới dạng collection để tránh lỗi crash trang
    if (!isset($addresses)) {
        $addresses = collect();
    }

    if (!isset($editingAddress)) {
        $editingAddress = null;
    }
    
    if (!$editingAddress && $addressIdFromOld > 0 && $addresses->isNotEmpty()) {
        $editingAddress = $addresses->firstWhere('id', $addressIdFromOld);
    }
    
    if (!isset($defaultAddress)) {
        $defaultAddress = $addresses->isNotEmpty() 
            ? ($addresses->firstWhere('is_default', true) ?? $addresses->first())
            : null;
    }
    
    $wishlist = $user->wishlists ?? collect();
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ tab: '{{ $activeTab }}' }">
    <div class="flex flex-col md:flex-row gap-8">
        <div class="w-full md:w-1/4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center sticky top-24">
                <img
                    src="{{ $user->avatar_url }}"
                    class="h-32 w-32 rounded-full border-4 border-green-50 mx-auto shadow-sm object-cover"
                    alt="{{ $user->name }}"
                >

                <h2 class="mt-4 font-bold text-xl text-gray-800">{{ $user->name }}</h2>
                <p class="text-gray-500 text-sm">{{ $user->email }}</p>
                <p class="text-gray-400 text-xs mt-1">{{ $user->phone ?: 'Chưa cập nhật số điện thoại' }}</p>

                <div class="space-y-1 text-left mt-6">
                    <button @click="tab = 'info'" :class="tab === 'info' ? 'bg-green-50 text-emerald-600 font-bold' : 'text-gray-600 hover:bg-gray-50'" class="w-full flex items-center space-x-3 p-3 rounded-xl transition outline-none">
                        <i class="fas fa-user-circle w-5 text-center"></i>
                        <span>Thông tin cá nhân</span>
                    </button>

                    <button @click="tab = 'addresses'" :class="tab === 'addresses' ? 'bg-green-50 text-emerald-600 font-bold' : 'text-gray-600 hover:bg-gray-50'" class="w-full flex items-center space-x-3 p-3 rounded-xl transition outline-none">
                        <i class="fas fa-map-marker-alt w-5 text-center"></i>
                        <span>Địa chỉ giao hàng</span>
                    </button>

                    <button @click="tab = 'orders'" :class="tab === 'orders' ? 'bg-green-50 text-emerald-600 font-bold' : 'text-gray-600 hover:bg-gray-50'" class="w-full flex items-center space-x-3 p-3 rounded-xl transition outline-none">
                        <i class="fas fa-shopping-bag w-5 text-center"></i>
                        <span>Đơn hàng của tôi</span>
                    </button>

                    <button @click="tab = 'wishlist'" :class="tab === 'wishlist' ? 'bg-green-50 text-emerald-600 font-bold' : 'text-gray-600 hover:bg-gray-50'" class="w-full flex items-center space-x-3 p-3 rounded-xl transition outline-none">
                        <i class="fas fa-heart w-5 text-center text-red-400"></i>
                        <span>Yêu thích</span>
                    </button>

                    <button @click="tab = 'compare'" :class="tab === 'compare' ? 'bg-green-50 text-emerald-600 font-bold' : 'text-gray-600 hover:bg-gray-50'" class="w-full flex items-center justify-between p-3 rounded-xl transition outline-none">
                        <span class="flex items-center space-x-3">
                            <i class="fas fa-scale-balanced w-5 text-center text-sky-500"></i>
                            <span>So sánh</span>
                        </span>
                        <span class="text-[10px] min-w-5 h-5 px-1 inline-flex items-center justify-center rounded-full bg-sky-100 text-sky-700 font-bold">
                            {{ $compareProducts->count() }}
                        </span>
                    </button>

                    <form action="{{ route('logout') }}" method="POST" class="pt-2">
                        @csrf
                        <button type="submit" class="w-full flex items-center space-x-3 p-3 rounded-xl text-red-500 hover:bg-red-50 transition">
                            <i class="fas fa-sign-out-alt w-5 text-center"></i>
                            <span>Đăng xuất</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="w-full md:w-3/4 space-y-6">
            <div x-show="tab === 'info'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-gray-800">Cài đặt tài khoản</h3>
                    <span class="text-xs bg-gray-100 text-gray-500 px-3 py-1 rounded-full uppercase tracking-wider">Thành viên từ {{ $user->created_at->format('m/Y') }}</span>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
                    @csrf
                    <input type="hidden" name="active_tab" value="info">

                    <div class="grid grid-cols-1 xl:grid-cols-[280px_1fr] gap-6">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
                            <h4 class="font-bold text-slate-800">Ảnh đại diện</h4>
                            <p class="text-sm text-slate-500 mt-1">Tải ảnh JPG, PNG hoặc WEBP, tối đa 2MB.</p>

                            <img
                                src="{{ $user->avatar_url }}"
                                alt="{{ $user->name }}"
                                class="mt-5 h-40 w-40 rounded-3xl object-cover border border-slate-200 shadow-sm mx-auto"
                            >

                            <div class="mt-5">
                                <label class="text-sm font-semibold text-slate-700 block mb-2">Chọn ảnh mới</label>
                                <input type="file" name="avatar" accept="image/png,image/jpeg,image/webp" class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-xl file:border-0 file:bg-emerald-100 file:px-4 file:py-2 file:font-semibold file:text-emerald-700 hover:file:bg-emerald-200">
                                @error('avatar') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                            </div>

                            @if($user->avatar)
                                <label class="mt-4 flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3">
                                    <input type="checkbox" name="remove_avatar" value="1" class="w-4 h-4 text-red-600 rounded border-slate-300" {{ old('remove_avatar') ? 'checked' : '' }}>
                                    <span class="text-sm text-slate-700">Xóa ảnh hiện tại</span>
                                </label>
                            @endif
                        </div>

                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Họ và tên</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-50 outline-none transition">
                                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Địa chỉ email</label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-50 outline-none transition">
                                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-2 md:col-span-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Số điện thoại</label>
                                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Nhập số điện thoại liên hệ" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-50 outline-none transition">
                                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="rounded-2xl border border-emerald-100 bg-emerald-50/70 p-5">
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                                    <div>
                                        <h4 class="font-bold text-emerald-900">Địa chỉ mặc định</h4>
                                        <p class="text-sm text-emerald-700 mt-1">Địa chỉ này sẽ được ưu tiên sẵn khi bạn checkout.</p>
                                    </div>

                                    <button type="button" @click="tab = 'addresses'" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-white text-emerald-700 font-semibold border border-emerald-200 hover:bg-emerald-100 transition">
                                        <i class="fas fa-pen"></i>
                                        <span>Quản lý địa chỉ</span>
                                    </button>
                                </div>

                                @if($defaultAddress)
                                    <div class="mt-4 rounded-2xl bg-white/90 border border-white px-5 py-4">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h5 class="font-bold text-slate-800">{{ $defaultAddress->full_name }}</h5>
                                            <span class="text-[10px] uppercase tracking-widest font-black bg-emerald-600 text-white px-2 py-1 rounded-full">Mặc định</span>
                                        </div>
                                        <p class="text-sm text-slate-500 mt-1">{{ $defaultAddress->phone }}</p>
                                        <p class="text-sm text-slate-700 mt-3 leading-6">{{ $defaultAddress->full_address }}</p>
                                    </div>
                                @else
                                    <div class="mt-4 rounded-2xl border border-dashed border-emerald-200 bg-white/80 px-5 py-6 text-sm text-slate-600">
                                        Bạn chưa có địa chỉ mặc định. Hãy thêm địa chỉ giao hàng đầu tiên để dùng nhanh khi đặt hàng.
                                    </div>
                                @endif
                            </div>

                            <div class="bg-gray-50 p-6 rounded-2xl space-y-4 border border-dashed border-gray-200">
                                <h4 class="font-bold text-gray-800 flex items-center">
                                    <i class="fas fa-lock mr-2 text-emerald-600"></i> Thay đổi mật khẩu
                                </h4>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="text-xs font-bold text-gray-500 uppercase ml-1">Mật khẩu mới</label>
                                        <input type="password" name="password" placeholder="Để trống nếu không đổi" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white focus:border-emerald-500 outline-none transition">
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-xs font-bold text-gray-500 uppercase ml-1">Xác nhận mật khẩu</label>
                                        <input type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu mới" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white focus:border-emerald-500 outline-none transition">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-10 rounded-xl shadow-lg shadow-emerald-200 transition transform hover:-translate-y-1">
                            Cập nhật hồ sơ
                        </button>
                    </div>
                </form>
            </div>

            <div x-show="tab === 'addresses'" x-cloak x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-lg text-gray-800">Địa chỉ giao hàng</h3>
                        <p class="text-sm text-gray-500 mt-1">Quản lý nhiều địa chỉ và chọn một địa chỉ mặc định để checkout nhanh hơn.</p>
                    </div>
                    <span class="text-xs bg-emerald-50 text-emerald-700 px-3 py-1 rounded-full font-bold uppercase tracking-wider">{{ $addresses->count() }} địa chỉ</span>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-[1.05fr_0.95fr] gap-6 p-6">
                    <div class="bg-slate-50 rounded-2xl border border-slate-100 p-6">
                        <div class="flex items-center justify-between gap-4 mb-5">
                            <div>
                                <h4 class="font-bold text-slate-800">{{ $editingAddress ? 'Cập nhật địa chỉ' : 'Thêm địa chỉ mới' }}</h4>
                                <p class="text-sm text-slate-500 mt-1">{{ $editingAddress ? 'Sửa địa chỉ đang chọn ở danh sách bên phải.' : 'Địa chỉ mới có thể đặt làm mặc định ngay lúc tạo.' }}</p>
                            </div>

                            @if($editingAddress)
                                <a href="{{ route('profile', ['tab' => 'addresses']) }}" class="text-sm font-semibold text-slate-500 hover:text-emerald-600">Hủy sửa</a>
                            @endif
                        </div>

                        <form action="{{ $editingAddress ? route('profile.addresses.update', $editingAddress) : route('profile.addresses.store') }}" method="POST" class="space-y-5">
                            @csrf
                            @if($editingAddress)
                                @method('PUT')
                                <input type="hidden" name="address_id" value="{{ old('address_id', $editingAddress->id) }}">
                            @endif
                            <input type="hidden" name="active_tab" value="addresses">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="text-sm font-semibold text-slate-700 block mb-2">Người nhận</label>
                                    <input type="text" name="full_name" value="{{ old('full_name', $editingAddress?->full_name) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition">
                                    @error('full_name') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="text-sm font-semibold text-slate-700 block mb-2">Số điện thoại</label>
                                    <input type="text" name="phone" value="{{ old('phone', $editingAddress?->phone) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition">
                                    @error('phone') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                <div>
                                    <label class="text-sm font-semibold text-slate-700 block mb-2">Tỉnh / Thành</label>
                                    <input type="text" name="province" value="{{ old('province', $editingAddress?->province) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition">
                                    @error('province') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="text-sm font-semibold text-slate-700 block mb-2">Quận / Huyện</label>
                                    <input type="text" name="district" value="{{ old('district', $editingAddress?->district) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition">
                                    @error('district') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="text-sm font-semibold text-slate-700 block mb-2">Phường / Xã</label>
                                    <input type="text" name="ward" value="{{ old('ward', $editingAddress?->ward) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition">
                                    @error('ward') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-slate-700 block mb-2">Địa chỉ cụ thể</label>
                                <textarea name="address_line" rows="4" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition">{{ old('address_line', $editingAddress?->address_line) }}</textarea>
                                @error('address_line') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                            </div>

                            <label class="flex items-center gap-3 rounded-xl bg-white border border-slate-200 px-4 py-3">
                                <input type="checkbox" name="is_default" value="1" class="w-4 h-4 text-emerald-600 rounded border-slate-300" {{ old('is_default', $editingAddress ? $editingAddress->is_default : $addresses->isEmpty()) ? 'checked' : '' }}>
                                <span class="text-sm font-medium text-slate-700">Đặt làm địa chỉ mặc định</span>
                            </label>

                            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                                <button type="submit" class="inline-flex justify-center items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-3 rounded-xl transition">
                                    <i class="fas fa-save"></i>
                                    <span>{{ $editingAddress ? 'Cập nhật địa chỉ' : 'Lưu địa chỉ mới' }}</span>
                                </button>

                                @if($editingAddress)
                                    <a href="{{ route('profile', ['tab' => 'addresses']) }}" class="inline-flex justify-center items-center gap-2 px-6 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 transition">Quay lại tạo mới</a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <div class="space-y-4">
                        @forelse($addresses as $address)
                            <div class="rounded-2xl border {{ $address->is_default ? 'border-emerald-200 bg-emerald-50/60' : 'border-slate-200 bg-white' }} p-5 shadow-sm">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h4 class="font-bold text-slate-800">{{ $address->full_name }}</h4>
                                            @if($address->is_default)
                                                <span class="text-[10px] uppercase tracking-widest font-black bg-emerald-600 text-white px-2 py-1 rounded-full">Mặc định</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-slate-500 mt-1">{{ $address->phone }}</p>
                                    </div>

                                    <div class="text-xs text-slate-400 whitespace-nowrap">{{ $address->created_at->format('d/m/Y') }}</div>
                                </div>

                                <div class="mt-4 rounded-xl bg-white/80 border border-white px-4 py-3 text-sm text-slate-600 leading-6">
                                    {{ $address->full_address }}
                                </div>

                                <div class="mt-4 flex flex-wrap gap-2">
                                    @if(!$address->is_default)
                                        <form action="{{ route('profile.addresses.default', $address) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-100 text-emerald-700 font-semibold hover:bg-emerald-200 transition">Đặt mặc định</button>
                                        </form>
                                    @endif

                                    <a href="{{ route('profile', ['tab' => 'addresses', 'edit_address' => $address->id]) }}" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50 transition">Chỉnh sửa</a>

                                    <form action="{{ route('profile.addresses.destroy', $address) }}" method="POST" onsubmit="return confirm('Xóa địa chỉ này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-4 py-2 rounded-xl bg-red-50 text-red-600 font-semibold hover:bg-red-100 transition">Xóa</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-6 py-12 text-center text-slate-500">
                                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-white border border-slate-200 text-emerald-600 mb-4">
                                    <i class="fas fa-map-marked-alt"></i>
                                </div>
                                <p class="font-semibold text-slate-700">Bạn chưa có địa chỉ giao hàng nào.</p>
                                <p class="text-sm mt-2">Thêm địa chỉ đầu tiên để sử dụng khi checkout.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

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
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($orders as $order)
                                        <tr class="hover:bg-gray-50/50 transition">
                                            <td class="px-4 py-4 font-bold text-emerald-600">#{{ $order->id }}</td>
                                            <td class="px-4 py-4 text-gray-500 text-sm">{{ $order->created_at->format('d/m/Y') }}</td>
                                            <td class="px-4 py-4 font-bold text-gray-800">{{ number_format($order->total_amount) }}d</td>
                                            <td class="px-4 py-4">
                                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $order->status_color }}">{{ $order->status_text }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div x-show="tab === 'wishlist'" x-cloak x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100">
                    <h3 class="font-bold text-lg text-gray-800">Sản phẩm bạn đã thích</h3>
                </div>

                <div class="p-6">
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

            <div x-show="tab === 'compare'" x-cloak x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-lg text-gray-800">So sánh</h3>
                    </div>

                    @if($compareProducts->isNotEmpty())
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('compare.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-semibold transition">
                                <i class="fas fa-table-columns"></i>
                                <span>Bảng so sánh</span>
                            </a>

                            <form action="{{ route('compare.clear') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 font-semibold transition">
                                    <i class="fas fa-trash"></i>
                                    <span>Xóa tất cả</span>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                <div class="p-6">
                    @if($compareProducts->isEmpty())
                        <div class="text-center py-12">
                            <i class="fas fa-scale-balanced text-gray-200 text-6xl mb-4"></i>
                            <p class="text-gray-500">Bạn chưa thêm sản phẩm nào vào danh sách so sánh.</p>
                            <a href="{{ route('products.index') }}" class="mt-4 inline-block text-sky-600 font-bold hover:underline">Chọn sản phẩm để so sánh</a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($compareProducts as $product)
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
