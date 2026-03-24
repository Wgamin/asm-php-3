@extends('admin.layouts.master')

@section('title', 'Quản lý người dùng')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Hệ thống người dùng</h1>
            <p class="text-sm text-gray-500 mt-1">Danh sách tất cả tài khoản khách hàng và quản trị viên.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="#" class="bg-[#00C89D] hover:bg-dark-green text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-green-100 transition transform hover:-translate-y-0.5 flex items-center">
                <i class="fas fa-user-plus mr-2"></i> Thêm thành viên
            </a>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 text-[11px] uppercase tracking-widest text-gray-400 font-bold border-b border-gray-100">
                        <th class="px-6 py-5">Người dùng</th>
                        <th class="px-6 py-5">Vai trò</th>
                        <th class="px-6 py-5">Ngày tạo</th>
                        <th class="px-6 py-5 text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $user)
                    <tr class="hover:bg-green-50/20 transition group">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-4">
                                <div class="relative flex-shrink-0">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=E8F5E9&color=2E7D32&bold=true" 
                                         class="h-11 w-11 rounded-2xl shadow-sm border border-white object-cover">
                                    <span class="absolute -bottom-1 -right-1 h-3.5 w-3.5 bg-green-500 border-2 border-white rounded-full"></span>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 text-sm group-hover:text-primary-green transition">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            @if($user->role === 'admin')
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-bold bg-purple-50 text-purple-600 border border-purple-100 uppercase tracking-wide">
                                    <i class="fas fa-user-shield mr-1.5"></i> Quản trị
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-100 uppercase tracking-wide">
                                    <i class="fas fa-user mr-1.5"></i> Khách hàng
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-xs text-gray-500">
                            <div class="font-medium text-gray-700">{{ $user->created_at->format('d/m/Y') }}</div>
                            <div class="text-[10px] text-gray-400 italic">{{ $user->created_at->diffForHumans() }}</div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center space-x-2">
                                <button title="Sửa" class="h-9 w-9 bg-gray-50 text-gray-400 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition flex items-center justify-center border border-transparent hover:border-blue-100">
                                    <i class="fas fa-pen text-[10px]"></i>
                                </button>
                                
                                <form action="#" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn xóa người dùng này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Xóa" class="h-9 w-9 bg-gray-50 text-gray-400 rounded-xl hover:bg-red-50 hover:text-red-600 transition flex items-center justify-center border border-transparent hover:border-red-100">
                                        <i class="fas fa-trash-alt text-[10px]"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-users-slash text-gray-200 text-5xl mb-4"></i>
                                <p class="text-gray-400 font-medium">Chưa có người dùng nào trong hệ thống.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="px-6 py-5 bg-gray-50/50 border-t border-gray-100">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection