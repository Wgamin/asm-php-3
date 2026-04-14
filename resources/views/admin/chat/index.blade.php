@extends('admin.layouts.master')

@section('title', 'Hỗ trợ khách hàng')

@section('content')
    <div class="mx-auto max-w-7xl space-y-8">
        <section>
            <p class="admin-kicker">Customer support</p>
            <h1 class="admin-headline mt-2 text-4xl font-bold tracking-[-0.05em] text-[var(--admin-text)]">Hỗ trợ khách hàng</h1>
            <p class="admin-copy mt-3 max-w-3xl text-sm">Theo dõi hội thoại giữa khách hàng và đội vận hành theo kiểu inbox tập trung, tối ưu cho phản hồi nhanh và làm việc nhiều phiên song song.</p>
        </section>

        <div class="grid gap-6 xl:grid-cols-[340px_1fr]">
            <section class="admin-surface-card overflow-hidden">
                <div class="border-b border-[rgba(112,122,108,0.12)] px-6 py-5">
                    <p class="admin-kicker">Inbox</p>
                    <h3 class="admin-headline mt-2 text-2xl font-bold tracking-[-0.03em]">Hội thoại</h3>
                </div>

                <div class="max-h-[720px] overflow-y-auto px-3 py-3">
                    @forelse($customers as $customer)
                        <a
                            href="{{ route('admin.chat.index', ['user' => $customer->id]) }}"
                            class="mb-2 block rounded-[1.2rem] px-4 py-4 transition {{ $selectedCustomer && $selectedCustomer->id === $customer->id ? 'bg-[rgba(32,98,35,0.1)]' : 'hover:bg-[rgba(95,103,92,0.08)]' }}"
                        >
                            <div class="flex items-start gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($customer->name) }}&background=206223&color=fff&bold=true" alt="{{ $customer->name }}" class="h-10 w-10 rounded-2xl object-cover">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-bold text-[var(--admin-text)]">{{ $customer->name }}</p>
                                            <p class="mt-1 truncate text-xs text-[var(--admin-text-muted)]">{{ $customer->email }}</p>
                                        </div>
                                        @if(($customer->chat_unread_count ?? 0) > 0)
                                            <span class="flex min-h-[20px] min-w-[20px] items-center justify-center rounded-full bg-[#ba1a1a] px-1 text-[10px] font-bold text-white">
                                                {{ $customer->chat_unread_count }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="mt-3 line-clamp-2 text-sm leading-6 text-[var(--admin-text-muted)]">{{ $customer->chat_last_message ?: 'Chưa có tin nhắn' }}</p>
                                    <p class="mt-2 text-[11px] font-semibold uppercase tracking-[0.14em] text-[rgba(95,103,92,0.7)]">{{ $customer->chat_last_time ?: 'Mới bắt đầu' }}</p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="admin-empty-state min-h-[20rem]">
                            <i class="fas fa-comments text-4xl opacity-30"></i>
                            <p class="text-sm">Chưa có hội thoại nào.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="admin-surface-card overflow-hidden">
                @if($selectedCustomer)
                    <div class="border-b border-[rgba(112,122,108,0.12)] px-6 py-5">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($selectedCustomer->name) }}&background=206223&color=fff&bold=true" alt="{{ $selectedCustomer->name }}" class="h-12 w-12 rounded-2xl object-cover">
                                <div>
                                    <h3 class="admin-headline text-2xl font-bold tracking-[-0.03em] text-[var(--admin-text)]">{{ $selectedCustomer->name }}</h3>
                                    <p class="mt-1 text-sm text-[var(--admin-text-muted)]">{{ $selectedCustomer->email }}</p>
                                </div>
                            </div>
                            <span class="admin-badge admin-badge--muted">Polling 4 giây</span>
                        </div>
                    </div>

                    <div id="admin-chat-root" data-customer-id="{{ $selectedCustomer->id }}" class="flex min-h-[720px] flex-col">
                        <div id="admin-chat-messages" class="flex-1 space-y-4 overflow-y-auto bg-[rgba(242,244,246,0.7)] px-6 py-6">
                            @foreach($messages as $message)
                                @php($mine = $message->sender_id === auth()->id())
                                <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                                    <div class="max-w-[78%] rounded-[1.25rem] px-4 py-3 {{ $mine ? 'bg-[linear-gradient(135deg,#206223,#3a7b3a)] text-white shadow-[0_20px_30px_-24px_rgba(32,98,35,0.8)]' : 'bg-white text-[var(--admin-text)] shadow-[0_20px_30px_-24px_rgba(25,28,30,0.18)]' }}">
                                        <p class="text-sm leading-7">{{ $message->message }}</p>
                                        <p class="mt-2 text-[11px] {{ $mine ? 'text-white/75' : 'text-[var(--admin-text-muted)]' }}">{{ $message->created_at?->format('H:i') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <form id="admin-chat-form" class="border-t border-[rgba(112,122,108,0.12)] px-6 py-5">
                            @csrf
                            <div class="flex items-end gap-3">
                                <textarea id="admin-chat-input" rows="2" class="min-h-[64px] flex-1 resize-none" placeholder="Nhập phản hồi cho khách hàng..."></textarea>
                                <button type="submit" class="admin-btn-primary h-12 min-w-[52px] rounded-2xl px-4">
                                    <i class="fas fa-paper-plane text-sm"></i>
                                </button>
                            </div>
                            <p id="admin-chat-error" class="mt-3 hidden text-sm text-[var(--admin-danger-text)]"></p>
                        </form>
                    </div>
                @else
                    <div class="admin-empty-state min-h-[720px]">
                        <i class="fas fa-comments text-4xl opacity-30"></i>
                        <p class="text-sm">Chưa có hội thoại nào để hiển thị.</p>
                    </div>
                @endif
            </section>
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
                    <div class="max-w-[78%] rounded-[1.25rem] px-4 py-3 ${message.mine ? 'bg-[linear-gradient(135deg,#206223,#3a7b3a)] text-white shadow-[0_20px_30px_-24px_rgba(32,98,35,0.8)]' : 'bg-white text-[var(--admin-text)] shadow-[0_20px_30px_-24px_rgba(25,28,30,0.18)]'}">
                        <p class="text-sm leading-7">${escapeHtml(message.message)}</p>
                        <p class="mt-2 text-[11px] ${message.mine ? 'text-white/75' : 'text-[var(--admin-text-muted)]'}">${escapeHtml(message.time ?? '')}</p>
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
                errorBox.textContent = 'Vui lòng nhập nội dung.';
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
                    errorBox.textContent = payload.message || 'Không thể gửi tin nhắn.';
                    errorBox.classList.remove('hidden');
                    return;
                }

                input.value = '';
                await fetchMessages();
                input.focus();
            } catch (error) {
                console.error(error);
                errorBox.textContent = 'Không thể gửi tin nhắn.';
                errorBox.classList.remove('hidden');
            }
        });

        fetchMessages().then(scrollToBottom);
        setInterval(fetchMessages, 4000);
    })();
</script>
@endpush
