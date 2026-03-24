<div class="p-6 border-b border-slate-100 bg-gradient-to-r from-emerald-50 to-teal-50">
    <div class="flex items-center gap-3">
        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=10b981&color=fff&bold=true" 
             class="w-12 h-12 rounded-xl shadow-sm">
        <div>
            <p class="font-semibold text-slate-800">{{ auth()->user()->name }}</p>
            <p class="text-xs text-slate-500 mt-0.5">{{ auth()->user()->email }}</p>
            <span class="inline-flex items-center px-2 py-0.5 mt-2 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1"></span>
                Quản trị viên
            </span>
        </div>
    </div>
</div>