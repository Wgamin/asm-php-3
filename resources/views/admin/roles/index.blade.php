@extends('admin.layouts.master')

@section('title', 'Quản lý người dùng')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Người dùng hệ thống</h1>
            <p class="text-sm text-gray-500 mt-1">Quản lý tài khoản khách hàng và nhân viên quản trị.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative">
                <input type="text" placeholder="Tìm kiếm người dùng..." 
                       class="pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:ring-4 focus:ring-green-50 focus:border-primary-green outline-none transition w-full md:w-64 text-sm">
                <i class="fas fa-search absolute left-4 top-3.5 text-gray-400 text-sm"></i>
            </div>
            <a href="#" class="bg-primary-green hover:bg-dark-green text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-green-100 transition transform hover:-translate-y-0.5 flex items-center whitespace-nowrap">
                <i class="fas fa-user-plus mr-2"></i> Thêm mới
            </a>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50">
                    <tr class="text-[11px] uppercase tracking-widest text-gray-400 font-bold border-b border-gray-100">
                        <th class="px-6 py-5">Thành viên</th>
                        <th class="px-6 py-5">Vai trò</th>
                        <th class="px-6 py-5">Trạng thái</th>
                        <th class="px-6 py-5">Ngày tham gia</th>
                        <th class="px-6 py-5 text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $user)
                    <tr class="hover:bg-green-50/20 transition group">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-4">
                                <div class="relative">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random&color=fff" 
                                         class="h-11 w-11 rounded-2xl shadow-sm border border-white">
                                    <div class="absolute -bottom-1 -right-1 h-3.5 w-3.5 bg-green-500 border-2 border-white rounded-full"></div>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 text-sm group-hover:text-primary-green transition">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            @if($user->role === 'admin')
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-[11px] font-bold bg-purple-50 text-purple-600 border border-purple-100 uppercase">
                                    <i class="fas fa-shield-alt mr-1.5"></i> Admin
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-[11px] font-bold bg-blue-50 text-blue-600 border border-blue-100 uppercase">
                                    <i class="fas fa-user mr-1.5"></i> Thành viên
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex items-center text-xs font-medium text-green-600">
                                <span class="h-1.5 w-1.5 rounded-full bg-green-500 mr-2"></span> Hoạt động
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <p class="text-xs text-gray-500 font-medium">{{ $user->created_at->format('d/m/Y') }}</p>
                            <p class="text-[10px] text-gray-300 italic">{{ $user->created_at->diffForHumans() }}</p>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <button class="h-9 w-9 bg-gray-50 text-gray-400 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition flex items-center justify-center border border-transparent hover:border-blue-100">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                <form action="#" method="POST" onsubmit="return confirm('Xóa người dùng này?')">
                                    @csrf @method('DELETE')
                                    <button class="h-9 w-9 bg-gray-50 text-gray-400 rounded-xl hover:bg-red-50 hover:text-red-600 transition flex items-center justify-center border border-transparent hover:border-red-100">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <i class="fas fa-users-slash text-gray-200 text-5xl mb-4"></i>
                            <p class="text-gray-400 font-medium">Không tìm thấy người dùng nào.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-5 bg-gray-50/50 border-t border-gray-100">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection