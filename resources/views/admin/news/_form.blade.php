@php
    $article = $article ?? null;
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-6">
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Tiêu đề bài viết</label>
            <input type="text" name="title" value="{{ old('title', $article?->title) }}" required
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-primary-green outline-none transition">
            @error('title')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Slug SEO</label>
            <input type="text" name="slug" value="{{ old('slug', $article?->slug) }}" placeholder="de-trong-se-tu-tao-tu-tieu-de"
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-primary-green outline-none transition">
            @error('slug')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Mô tả ngắn</label>
            <textarea name="excerpt" rows="4" required
                      class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-primary-green outline-none transition">{{ old('excerpt', $article?->excerpt) }}</textarea>
            @error('excerpt')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Nội dung chi tiết</label>
            <textarea name="content" rows="14" required
                      class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-primary-green outline-none transition">{{ old('content', $article?->content) }}</textarea>
            @error('content')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="bg-slate-50 rounded-2xl border border-slate-100 p-6 space-y-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Thiết lập SEO</h3>
                <p class="text-sm text-slate-500 mt-1">Nếu để trống, hệ thống sẽ dùng tiêu đề và mô tả ngắn làm dữ liệu SEO mặc định.</p>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Meta title</label>
                <input type="text" name="meta_title" value="{{ old('meta_title', $article?->meta_title) }}"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-primary-green outline-none transition">
                @error('meta_title')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Meta description</label>
                <textarea name="meta_description" rows="4"
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-primary-green outline-none transition">{{ old('meta_description', $article?->meta_description) }}</textarea>
                @error('meta_description')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Meta keywords</label>
                <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $article?->meta_keywords) }}" placeholder="tin tức nông sản, thực phẩm sạch, ..."
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-primary-green outline-none transition">
                @error('meta_keywords')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div x-data="{ imageUrl: '{{ $article?->image ? asset('storage/' . $article->image) : '' }}' }" class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
            <label class="block text-sm font-bold text-gray-700 mb-3">Ảnh cover</label>
            <label class="relative flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-gray-300 rounded-2xl cursor-pointer bg-gray-50 hover:border-primary-green transition overflow-hidden">
                <template x-if="imageUrl">
                    <div class="absolute inset-0 p-2 bg-white">
                        <img :src="imageUrl" class="w-full h-full object-cover rounded-xl">
                    </div>
                </template>

                <div x-show="!imageUrl" class="text-center px-4">
                    <i class="fas fa-image text-4xl text-slate-300 mb-3"></i>
                    <p class="text-sm text-slate-500"><span class="font-bold text-primary-green">Chọn ảnh bài viết</span></p>
                    <p class="text-xs text-slate-400 mt-2">JPG, PNG, WEBP tối đa 3MB</p>
                </div>

                <input type="file" name="image" class="sr-only" accept="image/*" {{ $article ? '' : 'required' }}
                       @change="
                            const file = $event.target.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = (e) => imageUrl = e.target.result;
                                reader.readAsDataURL(file);
                            }
                       ">
            </label>
            @error('image')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-sm font-black uppercase tracking-[0.2em] text-slate-500">Xuất bản</h3>

            <label class="flex items-center gap-3 text-sm text-slate-700">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published', $article?->is_published ?? true) ? 'checked' : '' }}>
                <span>Hiển thị công khai trên website</span>
            </label>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Thời gian đăng</label>
                <input type="datetime-local" name="published_at" value="{{ old('published_at', $article?->published_at?->format('Y-m-d\\TH:i')) }}"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-primary-green outline-none transition">
                @error('published_at')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</div>
