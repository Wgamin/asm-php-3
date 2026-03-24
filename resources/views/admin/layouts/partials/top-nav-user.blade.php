<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" 
            class="flex items-center gap-2 pl-3 pr-2 py-1.5 rounded-xl hover:bg-slate-100 transition">
        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=10b981&color=fff" 
             class="w-8 h-8 rounded-lg">
        <i class="fas fa-chevron-down text-xs text-slate-400" :class="open ? 'rotate-180' : ''"></i>
    </button>
    
    <div x-show="open" 
         @click.away="open = false"
         x-cloak
         x-transition
         class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-slate-100 py-1">
        <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50">
            <i class="fas fa-user w-4"></i>
            <span>Hồ sơ</span>
        </a>
        <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50">
            <i class="fas fa-cog w-4"></i>
            <span>Cài đặt</span>
        </a>
        <div class="border-t border-slate-100 my-1"></div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50">
                <i class="fas fa-sign-out-alt w-4"></i>
                <span>Đăng xuất</span>
            </button>
        </form>
    </div>
</div>