@php
    $article = $article ?? null;
@endphp

<div class="grid gap-8 xl:grid-cols-[1.45fr_0.9fr]">
    <div class="space-y-8">
        <section class="space-y-5">
            <div>
                <p class="admin-kicker">Thông tin bài viết</p>
                <h2 class="admin-headline mt-2 text-2xl font-bold tracking-[-0.03em]">Nội dung chính</h2>
            </div>

            <div>
                <label class="admin-field-label">Tiêu đề bài viết</label>
                <input type="text" name="title" value="{{ old('title', $article?->title) }}" required>
                @error('title')
                    <p class="mt-2 text-sm text-[var(--admin-danger-text)]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="admin-field-label">Slug SEO</label>
                <input type="text" name="slug" value="{{ old('slug', $article?->slug) }}" placeholder="Để trống để hệ thống tự sinh">
                @error('slug')
                    <p class="mt-2 text-sm text-[var(--admin-danger-text)]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="admin-field-label">Mô tả ngắn</label>
                <textarea name="excerpt" rows="4" required placeholder="Tóm tắt nội dung nổi bật của bài viết.">{{ old('excerpt', $article?->excerpt) }}</textarea>
                @error('excerpt')
                    <p class="mt-2 text-sm text-[var(--admin-danger-text)]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="admin-field-label">Nội dung chi tiết</label>
                <textarea name="content" rows="14" required placeholder="Viết nội dung chi tiết cho bài viết.">{{ old('content', $article?->content) }}</textarea>
                @error('content')
                    <p class="mt-2 text-sm text-[var(--admin-danger-text)]">{{ $message }}</p>
                @enderror
            </div>
        </section>

        <section class="admin-panel-muted p-6">
            <div class="mb-5">
                <p class="admin-kicker">SEO metadata</p>
                <h3 class="admin-headline mt-2 text-2xl font-bold tracking-[-0.03em]">Tối ưu công cụ tìm kiếm</h3>
                <p class="mt-2 text-sm text-[var(--admin-text-muted)]">Nếu để trống, hệ thống sẽ dùng tiêu đề và mô tả ngắn làm dữ liệu mặc định.</p>
            </div>

            <div class="space-y-5">
                <div>
                    <label class="admin-field-label">Meta title</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title', $article?->meta_title) }}">
                    @error('meta_title')
                        <p class="mt-2 text-sm text-[var(--admin-danger-text)]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="admin-field-label">Meta description</label>
                    <textarea name="meta_description" rows="4">{{ old('meta_description', $article?->meta_description) }}</textarea>
                    @error('meta_description')
                        <p class="mt-2 text-sm text-[var(--admin-danger-text)]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="admin-field-label">Meta keywords</label>
                    <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $article?->meta_keywords) }}" placeholder="nông sản sạch, tin tức nông nghiệp, ...">
                    @error('meta_keywords')
                        <p class="mt-2 text-sm text-[var(--admin-danger-text)]">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>
    </div>

    <div class="space-y-8">
        <section x-data="{ imageUrl: @js($article?->image ? asset('storage/' . $article->image) : null) }" class="admin-surface-card p-6">
            <div class="mb-5">
                <p class="admin-kicker">Cover image</p>
                <h3 class="admin-headline mt-2 text-2xl font-bold tracking-[-0.03em]">Ảnh đại diện</h3>
            </div>

            <label class="block cursor-pointer overflow-hidden rounded-[1.2rem] border border-dashed border-[rgba(112,122,108,0.3)] bg-[var(--admin-surface-low)] p-3 transition hover:border-[rgba(32,98,35,0.42)]">
                <input
                    type="file"
                    name="image"
                    accept="image/*"
                    class="hidden"
                    {{ $article ? '' : 'required' }}
                    @change="
                        const file = $event.target.files[0];
                        if (file) {
                            imageUrl = URL.createObjectURL(file);
                        }
                    "
                >
                <template x-if="imageUrl">
                    <img :src="imageUrl" alt="Ảnh cover" class="h-72 w-full rounded-[1rem] object-cover">
                </template>
                <template x-if="!imageUrl">
                    <div class="flex h-72 flex-col items-center justify-center rounded-[1rem] bg-white text-center">
                        <i class="fas fa-image text-4xl text-[var(--admin-text-muted)] opacity-50"></i>
                        <p class="mt-3 text-sm font-semibold text-[var(--admin-text)]">Chọn ảnh bài viết</p>
                        <p class="mt-2 max-w-[16rem] text-xs leading-6 text-[var(--admin-text-muted)]">JPG, PNG hoặc WEBP. Nên dùng ảnh ngang chất lượng tốt.</p>
                    </div>
                </template>
            </label>
            @error('image')
                <p class="mt-2 text-sm text-[var(--admin-danger-text)]">{{ $message }}</p>
            @enderror
        </section>

        <section class="admin-panel-muted p-6">
            <div class="mb-5">
                <p class="admin-kicker">Xuất bản</p>
                <h3 class="admin-headline mt-2 text-2xl font-bold tracking-[-0.03em]">Trạng thái hiển thị</h3>
            </div>

            <div class="space-y-5">
                <label class="flex items-center gap-3 rounded-[1rem] bg-white px-4 py-4 text-sm text-[var(--admin-text)]">
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published', $article?->is_published ?? true) ? 'checked' : '' }}>
                    <span>Hiển thị công khai trên website</span>
                </label>

                <div>
                    <label class="admin-field-label">Thời gian đăng</label>
                    <input type="datetime-local" name="published_at" value="{{ old('published_at', $article?->published_at?->format('Y-m-d\\TH:i')) }}">
                    @error('published_at')
                        <p class="mt-2 text-sm text-[var(--admin-danger-text)]">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>
    </div>
</div>
