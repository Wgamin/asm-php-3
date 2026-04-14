@php
    $coupon = $coupon ?? null;
@endphp

<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
    <div>
        <label class="admin-field-label">Mã coupon</label>
        <input type="text" name="code" value="{{ old('code', $coupon?->code) }}" required>
        @error('code')
            <p class="mt-2 text-sm text-[var(--admin-danger-text)]">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="admin-field-label">Tên hiển thị</label>
        <input type="text" name="name" value="{{ old('name', $coupon?->name) }}" required>
        @error('name')
            <p class="mt-2 text-sm text-[var(--admin-danger-text)]">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="admin-field-label">Kiểu giảm giá</label>
        <select name="type">
            <option value="fixed" {{ old('type', $coupon?->type) === 'fixed' ? 'selected' : '' }}>Tiền mặt</option>
            <option value="percent" {{ old('type', $coupon?->type) === 'percent' ? 'selected' : '' }}>Phần trăm</option>
        </select>
        @error('type')
            <p class="mt-2 text-sm text-[var(--admin-danger-text)]">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="admin-field-label">Giá trị</label>
        <input type="number" step="0.01" min="0.01" name="value" value="{{ old('value', $coupon?->value) }}" required>
        @error('value')
            <p class="mt-2 text-sm text-[var(--admin-danger-text)]">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="admin-field-label">Đơn tối thiểu</label>
        <input type="number" step="0.01" min="0" name="min_order_amount" value="{{ old('min_order_amount', $coupon?->min_order_amount) }}">
        @error('min_order_amount')
            <p class="mt-2 text-sm text-[var(--admin-danger-text)]">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="admin-field-label">Giảm tối đa</label>
        <input type="number" step="0.01" min="0" name="max_discount_amount" value="{{ old('max_discount_amount', $coupon?->max_discount_amount) }}">
        @error('max_discount_amount')
            <p class="mt-2 text-sm text-[var(--admin-danger-text)]">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="admin-field-label">Giới hạn lượt dùng</label>
        <input type="number" min="1" name="usage_limit" value="{{ old('usage_limit', $coupon?->usage_limit) }}">
        @error('usage_limit')
            <p class="mt-2 text-sm text-[var(--admin-danger-text)]">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="admin-field-label">Bắt đầu</label>
        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $coupon?->starts_at?->format('Y-m-d\\TH:i')) }}">
        @error('starts_at')
            <p class="mt-2 text-sm text-[var(--admin-danger-text)]">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="admin-field-label">Hết hạn</label>
        <input type="datetime-local" name="expires_at" value="{{ old('expires_at', $coupon?->expires_at?->format('Y-m-d\\TH:i')) }}">
        @error('expires_at')
            <p class="mt-2 text-sm text-[var(--admin-danger-text)]">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-end">
        <label class="flex w-full items-center gap-3 rounded-[1rem] bg-[var(--admin-surface-low)] px-4 py-4 text-sm font-medium text-[var(--admin-text)]">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $coupon?->is_active ?? true) ? 'checked' : '' }} class="h-4 w-4 rounded border-[rgba(112,122,108,0.25)] text-[#206223] focus:ring-[#206223]">
            Cho phép sử dụng coupon này
        </label>
    </div>

    <div class="md:col-span-2">
        <label class="admin-field-label">Mô tả</label>
        <textarea name="description" rows="4">{{ old('description', $coupon?->description) }}</textarea>
        @error('description')
            <p class="mt-2 text-sm text-[var(--admin-danger-text)]">{{ $message }}</p>
        @enderror
    </div>
</div>
