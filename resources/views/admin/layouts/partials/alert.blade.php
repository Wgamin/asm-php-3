<div class="mb-6">
    <div class="rounded-[1.2rem] px-5 py-4 text-sm font-semibold {{ session('success') ? 'bg-[rgba(223,243,219,0.85)] text-[var(--admin-success-text)]' : 'bg-[rgba(255,218,214,0.72)] text-[var(--admin-danger-text)]' }}">
        <div class="flex items-center gap-3">
            <i class="fas {{ session('success') ? 'fa-check-circle' : 'fa-exclamation-circle' }}"></i>
            <span>{{ session('success') ?? session('error') }}</span>
        </div>
    </div>
</div>
