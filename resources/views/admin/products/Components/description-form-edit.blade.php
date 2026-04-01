<div class="space-y-6">
    <div class="space-y-2">
        <label class="text-sm font-bold text-gray-700">Mô tả ngắn <span class="text-red-500">*</span></label>
        <input type="text" name="description" value="{{ old('description', $product->description) }}" required placeholder="Nhập mô tả ngắn gọn..."
               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition">
    </div>

    <div class="space-y-2">
        <label class="text-sm font-bold text-gray-700">Nội dung chi tiết <span class="text-red-500">*</span></label>
        <textarea name="content" rows="10" required placeholder="Nhập nội dung bài viết chi tiết..."
                  class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition">{{ old('content', $product->content) }}</textarea>
    </div>
</div>