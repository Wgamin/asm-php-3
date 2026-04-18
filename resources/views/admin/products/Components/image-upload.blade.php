<div x-data="imageUploadCreate()">
    <div class="space-y-6">
        <div>
            <p class="admin-kicker">Thư viện ảnh</p>
            <h3 class="admin-headline mt-2 text-2xl font-bold tracking-[-0.03em]">Ảnh sản phẩm</h3>
        </div>

        <div>
            <div class="flex items-center gap-2">
                <label class="admin-field-label">Ảnh đại diện chính</label>
                <x-admin-info>
                    JPG, PNG hoặc WEBP. Nên dùng ảnh vuông để hiển thị đồng bộ.
                </x-admin-info>
            </div>
            <label class="block cursor-pointer overflow-hidden rounded-[1.2rem] border border-dashed border-[rgba(112,122,108,0.3)] bg-[var(--admin-surface-low)] p-3 transition hover:border-[rgba(32,98,35,0.42)]">
                <input type="file" name="image" accept="image/*" required class="hidden" @change="previewMainImage">
                <template x-if="mainImageUrl">
                    <img :src="mainImageUrl" alt="Ảnh đại diện" class="h-56 w-full rounded-[1rem] object-cover">
                </template>
                <template x-if="!mainImageUrl">
                    <div class="flex h-56 flex-col items-center justify-center rounded-[1rem] bg-white text-center">
                        <i class="fas fa-camera text-3xl text-[var(--admin-text-muted)] opacity-60"></i>
                        <p class="mt-3 text-sm font-semibold text-[var(--admin-text)]">Chọn ảnh đại diện</p>
                    </div>
                </template>
            </label>
        </div>

        <div>
            <label class="admin-field-label">Ảnh phụ / gallery</label>
            <div class="grid grid-cols-2 gap-3">
                <template x-for="(url, index) in galleryUrls" :key="index">
                    <div class="relative overflow-hidden rounded-[1rem] bg-white shadow-sm">
                        <img :src="url" alt="Ảnh phụ" class="h-28 w-full object-cover">
                        <button type="button" @click="removeGalleryImage(index)" class="absolute right-2 top-2 flex h-8 w-8 items-center justify-center rounded-full bg-black/55 text-white">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                </template>

                <label class="flex h-28 cursor-pointer flex-col items-center justify-center rounded-[1rem] border border-dashed border-[rgba(112,122,108,0.3)] bg-[var(--admin-surface-low)] text-center transition hover:border-[rgba(32,98,35,0.42)]">
                    <input type="file" name="images[]" multiple accept="image/*" class="hidden" @change="previewGallery">
                    <i class="fas fa-plus text-lg text-[var(--admin-text-muted)]"></i>
                    <p class="mt-2 text-xs font-semibold uppercase tracking-[0.16em] text-[var(--admin-text-muted)]">Thêm ảnh</p>
                </label>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function imageUploadCreate() {
            return {
                mainImageUrl: null,
                galleryUrls: [],
                previewMainImage(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.mainImageUrl = URL.createObjectURL(file);
                    }
                },
                previewGallery(event) {
                    Array.from(event.target.files).forEach((file) => {
                        this.galleryUrls.push(URL.createObjectURL(file));
                    });
                },
                removeGalleryImage(index) {
                    this.galleryUrls.splice(index, 1);
                },
            };
        }
    </script>
@endpush
