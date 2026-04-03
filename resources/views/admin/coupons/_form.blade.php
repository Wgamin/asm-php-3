@php
    $coupon = $coupon ?? null;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Mã coupon</label>
        <input type="text" name="code" value="{{ old('code', $coupon?->code) }}" required
               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-emerald-500 outline-none">
        @error('code')
            <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Tên hiển thị</label>
        <input type="text" name="name" value="{{ old('name', $coupon?->name) }}" required
               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-emerald-500 outline-none">
        @error('name')
            <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Kiểu giảm giá</label>
        <select name="type" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-emerald-500 outline-none bg-white">
            <option value="fixed" {{ old('type', $coupon?->type) === 'fixed' ? 'selected' : '' }}>Tiền mặt</option>
            <option value="percent" {{ old('type', $coupon?->type) === 'percent' ? 'selected' : '' }}>Phần trăm</option>
        </select>
        @error('type')
            <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Giá trị</label>
        <input type="number" step="0.01" min="0.01" name="value" value="{{ old('value', $coupon?->value) }}" required
               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-emerald-500 outline-none">
        @error('value')
            <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Đơn tối thiểu</label>
        <input type="number" step="0.01" min="0" name="min_order_amount" value="{{ old('min_order_amount', $coupon?->min_order_amount) }}"
               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-emerald-500 outline-none">
        @error('min_order_amount')
            <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Giảm tối đa</label>
        <input type="number" step="0.01" min="0" name="max_discount_amount" value="{{ old('max_discount_amount', $coupon?->max_discount_amount) }}"
               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-emerald-500 outline-none">
        @error('max_discount_amount')
            <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Giới hạn lượt dùng</label>
        <input type="number" min="1" name="usage_limit" value="{{ old('usage_limit', $coupon?->usage_limit) }}"
               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-emerald-500 outline-none">
        @error('usage_limit')
            <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Trạng thái</label>
        <label class="inline-flex items-center gap-3 bg-gray-50 px-4 py-3 rounded-xl border border-gray-200">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $coupon?->is_active ?? true) ? 'checked' : '' }}>
            <span class="text-sm text-gray-700">Cho phép sử dụng coupon này</span>
        </label>
        @error('is_active')
            <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Bắt đầu</label>
        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $coupon?->starts_at?->format('Y-m-d\\TH:i')) }}"
               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-emerald-500 outline-none">
        @error('starts_at')
            <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Hết hạn</label>
        <input type="datetime-local" name="expires_at" value="{{ old('expires_at', $coupon?->expires_at?->format('Y-m-d\\TH:i')) }}"
               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-emerald-500 outline-none">
        @error('expires_at')
            <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-gray-700 mb-2">Mô tả</label>
        <textarea name="description" rows="4"
                  class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-emerald-500 outline-none">{{ old('description', $coupon?->description) }}</textarea>
        @error('description')
            <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
        @enderror
    </div>
</div>
