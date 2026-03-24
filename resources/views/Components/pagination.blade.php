@props(['items'])

@if ($items->hasPages())
    <nav class="flex items-center justify-center gap-2 mt-10">
        {{-- Nút Quay lại --}}
        @if ($items->onFirstPage())
            <span class="px-3 py-2 rounded-lg border border-slate-100 text-slate-300 cursor-not-allowed">
                <i class="fas fa-chevron-left text-sm"></i>
            </span>
        @else
            <a href="{{ $items->previousPageUrl() }}" class="px-3 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-emerald-50 hover:border-emerald-300 transition">
                <i class="fas fa-chevron-left text-sm"></i>
            </a>
        @endif

        {{-- Hiển thị các số trang (Logic đơn giản hóa của Laravel) --}}
        @foreach ($items->getUrlRange(1, $items->lastPage()) as $page => $url)
            @if ($page == $items->currentPage())
                <span class="px-4 py-2 rounded-lg bg-emerald-600 text-white font-medium shadow-sm shadow-emerald-100">
                    {{ $page }}
                </span>
            @else
                {{-- Giới hạn hiển thị số trang nếu quá nhiều (VD: chỉ hiện quanh trang hiện tại) --}}
                @if ($page == 1 || $page == $items->lastPage() || abs($page - $items->currentPage()) <= 1)
                    <a href="{{ $url }}" class="px-4 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-emerald-50 transition">
                        {{ $page }}
                    </a>
                @elseif ($page == $items->currentPage() - 2 || $page == $items->currentPage() + 2)
                    <span class="px-2 text-slate-400">...</span>
                @endif
            @endif
        @endforeach

        {{-- Nút Tiếp theo --}}
        @if ($items->hasMorePages())
            <a href="{{ $items->nextPageUrl() }}" class="px-3 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-emerald-50 hover:border-emerald-300 transition">
                <i class="fas fa-chevron-right text-sm"></i>
            </a>
        @else
            <span class="px-3 py-2 rounded-lg border border-slate-100 text-slate-300 cursor-not-allowed">
                <i class="fas fa-chevron-right text-sm"></i>
            </span>
        @endif
    </nav>
@endif