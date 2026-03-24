@extends('admin.layouts.master')

@section('title', 'Quản lý khách hàng')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Danh sách khách hàng</h1>
            <p class="text-sm text-gray-500 mt-1">Quản lý các tài khoản khách mua hàng trên hệ thống.</p>
        </div>
        <div class="flex items-center gap-3">
            {{-- Chuyển thành thẻ <a> để dùng được route() --}}
            <a href="{{ route('admin.users.create') }}" 
            class="bg-[#00C89D] hover:bg-[#00b38d] text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg transition transform hover:-translate-y-0.5 flex items-center">
                <i class="fas fa-user-plus mr-2"></i> Thêm khách hàng
            </a>
        </div>
    </div>

    {{-- Hiển thị thông báo thành công --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-2xl border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 text-[11px] uppercase tracking-widest text-gray-400 font-bold border-b border-gray-100">
                        <th class="px-6 py-5">Thông tin khách hàng</th>
                        <th class="px-6 py-5">Ngày tham gia</th>
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
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 text-sm group-hover:text-[#00C89D] transition">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-xs text-gray-500">
                            <div class="font-medium text-gray-700">{{ $user->created_at->format('d/m/Y') }}</div>
                            <div class="text-[10px] text-gray-400 italic">{{ $user->created_at->diffForHumans() }}</div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center space-x-2">
                                {{-- Nút sửa --}}
                                <a href="{{ route('admin.users.edit', $user->id) }}" title="Sửa thông tin" class="h-9 w-9 bg-gray-50 text-gray-400 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition flex items-center justify-center border border-transparent hover:border-blue-100">
                                    <i class="fas fa-pen text-[10px]"></i>
                                </a>
                                
                                {{-- Nút xóa --}}
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Xóa khách hàng này sẽ không thể khôi phục. Bạn chắc chắn chứ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Xóa tài khoản" class="h-9 w-9 bg-gray-50 text-gray-400 rounded-xl hover:bg-red-50 hover:text-red-600 transition flex items-center justify-center border border-transparent hover:border-red-100">
                                        <i class="fas fa-trash-alt text-[10px]"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-20 text-center text-gray-400">
                            <i class="fas fa-users-slash text-5xl mb-4 block opacity-20"></i>
                            Không tìm thấy khách hàng nào.
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