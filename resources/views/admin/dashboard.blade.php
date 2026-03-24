@extends('admin.layouts.master')

@section('title', 'Tổng quan')

@section('content')
<!-- Header -->
<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-800">Tổng quan hệ thống</h1>
    <p class="text-slate-500 mt-1">Chào mừng bạn trở lại trang quản trị. Dưới đây là thông tin tổng quan.</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Card 1: Người dùng -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-users text-emerald-600 text-xl"></i>
            </div>
            <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">+12%</span>
        </div>
        <h3 class="text-sm font-medium text-slate-500 mb-1">Tổng người dùng</h3>
        <div class="flex items-baseline justify-between">
            <span class="text-2xl font-bold text-slate-800">1,250</span>
            <span class="text-xs text-slate-400">người</span>
        </div>
        <div class="mt-3 pt-3 border-t border-slate-100">
            <div class="flex items-center text-xs">
                <span class="text-emerald-500 font-medium">+23</span>
                <span class="text-slate-400 ml-1">so với tháng trước</span>
            </div>
        </div>
    </div>

    <!-- Card 2: Đơn hàng mới -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-shopping-bag text-blue-600 text-xl"></i>
            </div>
            <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-full">+5%</span>
        </div>
        <h3 class="text-sm font-medium text-slate-500 mb-1">Đơn hàng mới</h3>
        <div class="flex items-baseline justify-between">
            <span class="text-2xl font-bold text-slate-800">45</span>
            <span class="text-xs text-slate-400">đơn</span>
        </div>
        <div class="mt-3 pt-3 border-t border-slate-100">
            <div class="flex items-center text-xs">
                <span class="text-blue-500 font-medium">+8</span>
                <span class="text-slate-400 ml-1">so với hôm qua</span>
            </div>
        </div>
    </div>

    <!-- Card 3: Doanh thu -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-dollar-sign text-amber-600 text-xl"></i>
            </div>
            <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-1 rounded-full">+18%</span>
        </div>
        <h3 class="text-sm font-medium text-slate-500 mb-1">Doanh thu tháng</h3>
        <div class="flex items-baseline justify-between">
            <span class="text-2xl font-bold text-slate-800">48.5M</span>
            <span class="text-xs text-slate-400">VNĐ</span>
        </div>
        <div class="mt-3 pt-3 border-t border-slate-100">
            <div class="flex items-center text-xs">
                <span class="text-amber-500 font-medium">+5.2M</span>
                <span class="text-slate-400 ml-1">so với tháng trước</span>
            </div>
        </div>
    </div>

    <!-- Card 4: Báo cáo lỗi -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-rose-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-rose-600 text-xl"></i>
            </div>
            <span class="text-xs font-medium text-rose-600 bg-rose-50 px-2 py-1 rounded-full">-2</span>
        </div>
        <h3 class="text-sm font-medium text-slate-500 mb-1">Báo cáo lỗi</h3>
        <div class="flex items-baseline justify-between">
            <span class="text-2xl font-bold text-slate-800">2</span>
            <span class="text-xs text-slate-400">chưa xử lý</span>
        </div>
        <div class="mt-3 pt-3 border-t border-slate-100">
            <div class="flex items-center text-xs">
                <span class="text-rose-500 font-medium">Cần xử lý</span>
                <span class="text-slate-400 ml-1">ngay</span>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Biểu đồ doanh thu -->
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="font-semibold text-slate-800">Biểu đồ doanh thu</h3>
                <p class="text-xs text-slate-500 mt-1">7 ngày gần nhất</p>
            </div>
            <select class="text-sm border border-slate-200 rounded-lg px-3 py-2 bg-slate-50">
                <option>Tuần này</option>
                <option>Tháng này</option>
                <option>Quý này</option>
            </select>
        </div>
        
        <!-- Chart bars -->
        <div class="h-64 flex items-end justify-between gap-2">
            @foreach(['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'] as $index => $day)
            <div class="flex-1 flex flex-col items-center gap-2">
                <div class="w-full bg-emerald-50 rounded-lg relative group cursor-pointer">
                    <div class="h-32 bg-emerald-200 rounded-lg" style="height: {{ rand(40, 100) }}px"></div>
                    <!-- Tooltip -->
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block bg-slate-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap">
                        12.5M VNĐ
                    </div>
                </div>
                <span class="text-xs text-slate-500">{{ $day }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Top sản phẩm -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h3 class="font-semibold text-slate-800 mb-4">Sản phẩm bán chạy</h3>
        <div class="space-y-4">
            @foreach([
                ['name' => 'Cam sành hữu cơ', 'sales' => 234, 'revenue' => '10.5M', 'color' => 'emerald'],
                ['name' => 'Bơ sáp Đắk Lắk', 'sales' => 189, 'revenue' => '9.2M', 'color' => 'blue'],
                ['name' => 'Thanh long ruột đỏ', 'sales' => 156, 'revenue' => '7.8M', 'color' => 'amber'],
                ['name' => 'Xoài cát Hòa Lộc', 'sales' => 98, 'revenue' => '6.4M', 'color' => 'purple'],
                ['name' => 'Chuối già hương', 'sales' => 67, 'revenue' => '3.2M', 'color' => 'rose'],
            ] as $item)
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-{{ $item['color'] }}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-apple-alt text-{{ $item['color'] }}-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-slate-800">{{ $item['name'] }}</p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs text-slate-500">{{ $item['sales'] }} đơn</span>
                        <span class="text-xs text-slate-300">•</span>
                        <span class="text-xs font-medium text-emerald-600">{{ $item['revenue'] }}</span>
                    </div>
                </div>
                <div class="text-xs text-slate-400 bg-slate-50 px-2 py-1 rounded-full">
                    {{ $loop->iteration }}
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="mt-4 pt-4 border-t border-slate-100">
            <a href="#" class="text-sm text-emerald-600 hover:text-emerald-700 flex items-center justify-center gap-1">
                Xem tất cả sản phẩm
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
</div>

<!-- Tables Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Thành viên mới -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-slate-800">Thành viên mới</h3>
                    <p class="text-xs text-slate-500 mt-1">Đăng ký gần đây nhất</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                    Xem tất cả
                </a>
            </div>
        </div>
        
        <div class="divide-y divide-slate-100">
            @forelse($latestUsers as $user)
            <div class="p-4 hover:bg-slate-50 transition-colors">
                <div class="flex items-center gap-3">
                    {{-- Tạo avatar tự động dựa trên tên User --}}
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=10b981&color=fff" 
                        class="w-10 h-10 rounded-lg shadow-sm">
                    
                    <div class="flex-1">
                        <p class="font-medium text-slate-800 text-sm">{{ $user->name }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-1">{{ $user->email }}</p>
                    </div>
                    
                    <div class="text-right">
                        {{-- Hiển thị thời gian đăng ký dạng: 2 giờ trước --}}
                        <span class="text-[10px] font-medium text-slate-400">
                            {{ $user->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 text-center">
                <p class="text-sm text-slate-400 italic">Chưa có thành viên nào.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Đơn hàng gần đây -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-slate-800">Đơn hàng gần đây</h3>
                    <p class="text-xs text-slate-500 mt-1">5 đơn hàng mới nhất</p>
                </div>
                <a href="#" class="text-sm text-emerald-600 hover:text-emerald-700">
                    Xem tất cả
                </a>
            </div>
        </div>
        
        <table class="w-full">
            <thead class="bg-slate-50 text-xs text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-left">Mã ĐH</th>
                    <th class="px-4 py-3 text-left">Khách hàng</th>
                    <th class="px-4 py-3 text-left">Tổng tiền</th>
                    <th class="px-4 py-3 text-left">Trạng thái</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach([
                    ['id' => '#DH001', 'customer' => 'Nguyễn Văn An', 'total' => '1.250.000đ', 'status' => 'completed'],
                    ['id' => '#DH002', 'customer' => 'Trần Thị Bích', 'total' => '890.000đ', 'status' => 'processing'],
                    ['id' => '#DH003', 'customer' => 'Lê Hoàng Cường', 'total' => '2.100.000đ', 'status' => 'pending'],
                    ['id' => '#DH004', 'customer' => 'Phạm Minh Đức', 'total' => '450.000đ', 'status' => 'completed'],
                    ['id' => '#DH005', 'customer' => 'Hoàng Thị Hoa', 'total' => '1.890.000đ', 'status' => 'processing'],
                ] as $order)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3">
                        <span class="font-medium text-slate-800">{{ $order['id'] }}</span>
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ $order['customer'] }}</td>
                    <td class="px-4 py-3 text-sm font-medium text-slate-800">{{ $order['total'] }}</td>
                    <td class="px-4 py-3">
                        @php
                            $statusClass = [
                                'completed' => 'bg-emerald-100 text-emerald-600',
                                'processing' => 'bg-blue-100 text-blue-600',
                                'pending' => 'bg-amber-100 text-amber-600'
                            ][$order['status']] ?? 'bg-slate-100 text-slate-600';
                            
                            $statusText = [
                                'completed' => 'Hoàn thành',
                                'processing' => 'Đang xử lý',
                                'pending' => 'Chờ xác nhận'
                            ][$order['status']] ?? $order['status'];
                        @endphp
                        <span class="text-xs px-2 py-1 rounded-full {{ $statusClass }}">
                            {{ $statusText }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <a href="#" class="bg-white rounded-xl border border-slate-100 p-4 hover:shadow-md transition-shadow flex items-center gap-4">
        <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
            <i class="fas fa-plus text-emerald-600"></i>
        </div>
        <div>
            <h4 class="font-medium text-slate-800">Thêm sản phẩm mới</h4>
            <p class="text-xs text-slate-500">Cập nhật danh mục nông sản</p>
        </div>
    </a>
    
    <a href="#" class="bg-white rounded-xl border border-slate-100 p-4 hover:shadow-md transition-shadow flex items-center gap-4">
        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
            <i class="fas fa-truck text-blue-600"></i>
        </div>
        <div>
            <h4 class="font-medium text-slate-800">Quản lý vận chuyển</h4>
            <p class="text-xs text-slate-500">Cập nhật trạng thái đơn hàng</p>
        </div>
    </a>
    
    <a href="#" class="bg-white rounded-xl border border-slate-100 p-4 hover:shadow-md transition-shadow flex items-center gap-4">
        <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
            <i class="fas fa-chart-line text-amber-600"></i>
        </div>
        <div>
            <h4 class="font-medium text-slate-800">Báo cáo doanh thu</h4>
            <p class="text-xs text-slate-500">Xuất báo cáo tháng 3</p>
        </div>
    </a>
</div>
@endsection