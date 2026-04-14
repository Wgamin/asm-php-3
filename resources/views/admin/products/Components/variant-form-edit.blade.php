<section class="admin-surface-card p-7" x-show="productType === 'variable'" x-transition>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="admin-kicker">Biến thể & thuộc tính</p>
            <h2 class="admin-headline mt-2 text-2xl font-bold tracking-[-0.03em]">Danh sách biến thể</h2>
            <p class="mt-2 text-sm text-[var(--admin-text-muted)]">Cập nhật lại biến thể hiện có hoặc thêm tổ hợp mới cho sản phẩm này.</p>
        </div>
        <button type="button" @click="addVariant()" class="admin-btn-primary">
            <i class="fas fa-plus text-sm"></i>
            Thêm biến thể
        </button>
    </div>

    <div class="space-y-5">
        <template x-for="(variant, index) in variants" :key="index">
            <article class="rounded-[1.2rem] border border-[rgba(112,122,108,0.12)] bg-[var(--admin-surface-low)] p-5 shadow-sm">
                <div class="mb-5 flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-bold text-[var(--admin-text)]">Biến thể #<span x-text="index + 1"></span></p>
                        <p class="mt-1 text-xs text-[var(--admin-text-muted)]" x-text="Object.values(variant.attributes || {}).filter(value => value !== '').join(' • ') || 'Chưa chọn thuộc tính'"></p>
                    </div>
                    <button type="button" @click="removeVariant(index)" class="admin-action-icon hover:!bg-[rgba(255,218,214,0.45)] hover:!text-[var(--admin-danger-text)]" title="Xóa biến thể">
                        <i class="fas fa-trash text-sm"></i>
                    </button>
                </div>

                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                    <div>
                        <label class="admin-field-label">SKU</label>
                        <input type="text" :name="`variants[${index}][sku]`" x-model="variant.sku" placeholder="Tự động nếu để trống">
                    </div>
                    <div>
                        <label class="admin-field-label">Giá bán</label>
                        <input type="number" :name="`variants[${index}][price]`" x-model="variant.price" min="0" required placeholder="0">
                    </div>
                    <div>
                        <label class="admin-field-label">Giá khuyến mãi</label>
                        <input type="number" :name="`variants[${index}][sale_price]`" x-model="variant.sale_price" min="0" placeholder="0">
                    </div>
                    <div>
                        <label class="admin-field-label">Giá vốn</label>
                        <input type="number" :name="`variants[${index}][cost_price]`" x-model="variant.cost_price" min="0" placeholder="0">
                    </div>
                    <div>
                        <label class="admin-field-label">Tồn kho</label>
                        <input type="number" :name="`variants[${index}][stock]`" x-model="variant.stock" min="0" required placeholder="0">
                    </div>
                </div>

                <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach($attributes as $attr)
                        <div>
                            <label class="admin-field-label">{{ $attr->name }}</label>
                            <select :name="`variants[${index}][attributes][{{ $attr->name }}]`" x-model="variant.attributes['{{ $attr->name }}']">
                                <option value="">Không dùng</option>
                                @foreach($attr->attributeValues as $val)
                                    <option value="{{ $val->value }}">{{ $val->value }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5">
                    <label class="admin-field-label">Ảnh riêng của biến thể</label>
                    <div class="flex items-center gap-3 rounded-[1rem] bg-white px-4 py-3">
                        <div class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-[0.9rem] bg-[var(--admin-surface-low)]">
                            <template x-if="variant.image_url">
                                <img :src="variant.image_url" alt="Ảnh biến thể" class="h-full w-full object-cover">
                            </template>
                            <template x-if="!variant.image_url">
                                <i class="fas fa-image text-[var(--admin-text-muted)] opacity-50"></i>
                            </template>
                        </div>
                        <label class="admin-btn-secondary cursor-pointer">
                            <i class="fas fa-upload text-sm"></i>
                            Đổi ảnh
                            <input
                                type="file"
                                accept="image/*"
                                class="hidden"
                                :name="`variants[${index}][image]`"
                                @change="
                                    const file = $event.target.files[0];
                                    if (file) {
                                        variant.image_url = URL.createObjectURL(file);
                                    }
                                "
                            >
                        </label>
                        <input type="hidden" :name="`variants[${index}][existing_image]`" :value="variant.existing_image || ''">
                    </div>
                </div>
            </article>
        </template>

        <div x-show="variants.length === 0" class="admin-empty-state rounded-[1.2rem] border border-dashed border-[rgba(112,122,108,0.16)] bg-[var(--admin-surface-low)]">
            <i class="fas fa-layer-group text-4xl opacity-30"></i>
            <p class="text-sm">Sản phẩm này chưa có biến thể nào. Bấm “Thêm biến thể” để tạo mới.</p>
        </div>
    </div>
</section>
