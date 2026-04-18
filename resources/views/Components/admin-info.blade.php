@props(['class' => ''])

<div
    x-data="{ open: false }"
    @keydown.escape.window="open = false"
    {{ $attributes->merge(['class' => trim('relative inline-flex '.$class)]) }}
>
    <button
        type="button"
        @click="open = !open"
        class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-[rgba(112,122,108,0.18)] bg-white text-[11px] font-bold leading-none text-[var(--admin-text-muted,#64748b)] transition hover:border-[#206223] hover:text-[#206223]"
        aria-label="Hiện mô tả"
        :aria-expanded="open.toString()"
    >
        i
    </button>

    <div
        x-show="open"
        x-cloak
        @click.away="open = false"
        x-transition.origin.top.left
        class="absolute left-0 top-full z-30 mt-2 w-[min(26rem,calc(100vw-2rem))] max-w-sm rounded-[1rem] border border-[rgba(112,122,108,0.16)] bg-white/95 p-4 text-sm leading-6 text-[var(--admin-text-muted,#64748b)] shadow-[0_24px_48px_-28px_rgba(25,28,30,0.28)] backdrop-blur"
    >
        {{ $slot }}
    </div>
</div>
