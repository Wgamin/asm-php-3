@extends('admin.layouts.master')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Chỉnh sửa bài viết</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $article->title }}</p>
            </div>
            <a href="{{ route('admin.news.index') }}" class="text-gray-500 hover:text-gray-700 font-medium">Quay lại</a>
        </div>

        <form action="{{ route('admin.news.update', $article) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
            @csrf
            @method('PUT')
            @include('admin.news._form', ['article' => $article])

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.news.index') }}" class="px-6 py-3 text-gray-500 font-bold hover:text-gray-700 transition">Hủy</a>
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-10 rounded-xl shadow-lg shadow-green-100 transition">
                    Cập nhật bài viết
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
