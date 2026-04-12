@extends('admin.layouts.master')

@section('title', 'Chat ho tro')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Chat ho tro</h2>
        <p class="mt-1 text-sm text-slate-500">Theo doi tin nhan khach hang va tra loi gan realtime.</p>
    </div>

    <div class="grid gap-6 xl:grid-cols-[320px_1fr]">
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4">
                <h3 class="font-bold text-slate-900">Hoi thoai</h3>
            </div>

            <div class="max-h-[680px] overflow-y-auto">
                @forelse($customers as $customer)
                    <a
                        href="{{ route('admin.chat.index', ['user' => $customer->id]) }}"
                        class="block border-b border-slate-100 px-5 py-4 transition hover:bg-slate-50 {{ $selectedCustomer && $selectedCustomer->id === $customer->id ? 'bg-emerald-50/70' : '' }}"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-slate-900">{{ $customer->name }}</p>
                                <p class="mt-1 truncate text-xs text-slate-400">{{ $customer->email }}</p>
                            </div>
                            @if(($customer->chat_unread_count ?? 0) > 0)
                                <span class="inline-flex min-h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white">
                                    {{ $customer->chat_unread_count }}
                                </span>
                            @endif
                        </div>
                        <p class="mt-2 line-clamp-2 text-sm text-slate-500">{{ $customer->chat_last_message ?: 'Chua co tin nhan' }}</p>
                        <p class="mt-2 text-[11px] uppercase tracking-wider text-slate-400">{{ $customer->chat_last_time ?: 'Moi bat dau' }}</p>
                    </a>
                @empty
                    <div class="px-5 py-12 text-center text-sm text-slate-400">
                        Chua co cuoc tro chuyen nao.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            @if($selectedCustomer)
                <div class="border-b border-slate-100 px-6 py-5">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">{{ $selectedCustomer->name }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ $selectedCustomer->email }}</p>
                        </div>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-slate-500">Polling 4s</span>
                    </div>
                </div>

                <div id="admin-chat-root" data-customer-id="{{ $selectedCustomer->id }}" class="flex min-h-[680px] flex-col">
                    <div id="admin-chat-messages" class="flex-1 space-y-4 overflow-y-auto bg-slate-50 px-6 py-6">
                        @foreach($messages as $message)
                            @php
                                $mine = $message->sender_id === auth()->id();
                            @endphp
                            <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-[75%] rounded-3xl px-4 py-3 {{ $mine ? 'bg-slate-900 text-white' : 'border border-slate-200 bg-white text-slate-800' }}">
                                    <p class="text-sm leading-6">{{ $message->message }}</p>
                                    <p class="mt-2 text-[11px] {{ $mine ? 'text-slate-300' : 'text-slate-400' }}">{{ $message->created_at?->format('H:i') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <form id="admin-chat-form" class="border-t border-slate-100 p-5">
                        @csrf
                        <div class="flex items-end gap-3">
                            <textarea id="admin-chat-input" rows="2" class="min-h-[58px] flex-1 resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-50" placeholder="Nhap noi dung phan hoi..."></textarea>
                            <button type="submit" class="inline-flex h-12 items-center justify-center rounded-2xl bg-emerald-600 px-5 font-bold text-white transition hover:bg-emerald-700">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                        <p id="admin-chat-error" class="mt-3 hidden text-sm text-red-500"></p>
                    </form>
                </div>
            @else
                <div class="flex min-h-[680px] items-center justify-center px-6 text-center text-slate-400">
                    Chua co hoi thoai nao de hien thi.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (() => {
        const root = document.getElementById('admin-chat-root');

        if (!root) {
            return;
        }

        const customerId = root.dataset.customerId;
        const messagesUrl = @json(url('/admin/chat')) + `/${customerId}/messages`;
        const sendUrl = @json(url('/admin/chat')) + `/${customerId}/messages`;
        const csrfToken = @json(csrf_token());
        const list = document.getElementById('admin-chat-messages');
        const form = document.getElementById('admin-chat-form');
        const input = document.getElementById('admin-chat-input');
        const errorBox = document.getElementById('admin-chat-error');

        const escapeHtml = (value) => String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

        const shouldStickBottom = () => list.scrollTop + list.clientHeight >= list.scrollHeight - 80;
        const scrollToBottom = () => {
            list.scrollTop = list.scrollHeight;
        };

        const renderMessages = (messages) => {
            const stick = shouldStickBottom();

            list.innerHTML = messages.map((message) => `
                <div class="flex ${message.mine ? 'justify-end' : 'justify-start'}">
                    <div class="max-w-[75%] rounded-3xl px-4 py-3 ${message.mine ? 'bg-slate-900 text-white' : 'border border-slate-200 bg-white text-slate-800'}">
                        <p class="text-sm leading-6">${escapeHtml(message.message)}</p>
                        <p class="mt-2 text-[11px] ${message.mine ? 'text-slate-300' : 'text-slate-400'}">${escapeHtml(message.time ?? '')}</p>
                    </div>
                </div>
            `).join('');

            if (stick || messages.length <= 1) {
                scrollToBottom();
            }
        };

        const fetchMessages = async () => {
            try {
                const response = await fetch(`${messagesUrl}?_=${Date.now()}`, {
                    cache: 'no-store',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    return;
                }

                const payload = await response.json();
                renderMessages(payload.messages ?? []);
            } catch (error) {
                console.error(error);
            }
        };

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            errorBox.classList.add('hidden');
            errorBox.textContent = '';

            const message = input.value.trim();

            if (!message) {
                errorBox.textContent = 'Vui long nhap noi dung.';
                errorBox.classList.remove('hidden');
                return;
            }

            try {
                const response = await fetch(sendUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ message }),
                });

                const payload = await response.json();

                if (!response.ok) {
                    errorBox.textContent = payload.message || 'Khong the gui tin nhan.';
                    errorBox.classList.remove('hidden');
                    return;
                }

                input.value = '';
                await fetchMessages();
                input.focus();
            } catch (error) {
                console.error(error);
                errorBox.textContent = 'Khong the gui tin nhan.';
                errorBox.classList.remove('hidden');
            }
        });

        fetchMessages().then(scrollToBottom);
        setInterval(fetchMessages, 4000);
    })();
</script>
@endpush
