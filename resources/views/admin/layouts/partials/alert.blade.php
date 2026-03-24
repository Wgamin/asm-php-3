<div class="mb-6">
    <div class="p-4 rounded-xl {{ session('success') ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
        <div class="flex items-center gap-3">
            <i class="fas {{ session('success') ? 'fa-check-circle' : 'fa-exclamation-circle' }}"></i>
            <span class="text-sm font-medium">{{ session('success') ?? session('error') }}</span>
        </div>
    </div>
</div>