<div class="space-y-6">
    <div>
        <p class="admin-kicker">Nội dung storefront</p>
        <h3 class="admin-headline mt-2 text-2xl font-bold tracking-[-0.03em]">Mô tả bán hàng</h3>
    </div>

    <div>
        <div class="flex items-center gap-2">
            <label class="admin-field-label">Mô tả ngắn</label>
            <x-admin-info>
                Phần này thường xuất hiện trong danh sách sản phẩm và thẻ tóm tắt.
            </x-admin-info>
        </div>
        <textarea name="description" rows="3" required placeholder="Tóm tắt đặc điểm nổi bật của sản phẩm trong 1-2 câu.">{{ old('description') }}</textarea>
    </div>

    <div>
        <label class="admin-field-label">Nội dung chi tiết</label>
        <textarea name="content" rows="10" required placeholder="Viết nội dung chi tiết về nguồn gốc, cách bảo quản, giá trị dinh dưỡng và thông tin bán hàng khác.">{{ old('content') }}</textarea>
    </div>
</div>
