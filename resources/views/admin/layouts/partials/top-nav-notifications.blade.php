<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" 
            class="w-10 h-10 rounded-lg hover:bg-slate-100 text-slate-600 transition relative">
        <i class="fas fa-bell"></i>
        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
    </button>
    
    <!-- Dropdown -->
    <div x-show="open" 
         @click.away="open = false"
         x-cloak
         x-transition
         class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-slate-100 py-2">
        <div class="px-4 py-2 border-b border-slate-100">
            <h3 class="font-semibold text-slate-800">Thông báo</h3>
        </div>
        <div class="max-h-96 overflow-y-auto">
            <a href="#" class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50">
                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-shopping-bag text-emerald-600 text-sm"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-800">Đơn hàng mới #12345</p>
                    <p class="text-xs text-slate-400 mt-1">5 phút trước</p>
                </div>
            </a>
            <a href="#" class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user text-blue-600 text-sm"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-800">Người dùng mới đăng ký</p>
                    <p class="text-xs text-slate-400 mt-1">1 giờ trước</p>
                </div>
            </a>
        </div>
    </div>
</div>