@extends('admin.layouts.master')

@section('title', 'Tạo bài viết')

@section('content')
    <div class="mx-auto max-w-6xl space-y-8">
        <section class="flex items-end justify-between gap-4">
            <div>
                <p class="admin-kicker">Content & CMS</p>
                <h1 class="admin-headline mt-2 text-4xl font-bold tracking-[-0.05em] text-[var(--admin-text)]">Tạo bài viết mới</h1>
                <x-admin-info class="mt-3">
                    Soạn nội dung, tối ưu SEO và cấu hình xuất bản trực tiếp từ màn quản trị.
                </x-admin-info>
            </div>
            <a href="{{ route('admin.news.index') }}" class="admin-btn-secondary">
                <i class="fas fa-arrow-left text-sm"></i>
                Quay lại
            </a>
        </section>

        <section class="admin-surface-card p-7">
            <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @include('admin.news._form')

                <div class="flex justify-end gap-3 border-t border-[rgba(112,122,108,0.12)] pt-6">
                    <a href="{{ route('admin.news.index') }}" class="admin-btn-ghost">Hủy</a>
                    <button type="submit" class="admin-btn-primary">
                        <i class="fas fa-floppy-disk text-sm"></i>
                        Lưu bài viết
                    </button>
                </div>
            </form>
        </section>
    </div>
@endsection
