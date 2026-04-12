@extends('layouts.client')

@section('title', 'Chat ho tro')

@section('content')
<div class="bg-slate-50 py-10">
    <div class="mx-auto grid max-w-6xl gap-8 px-4 lg:grid-cols-[320px_1fr]">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600">
                    <i class="fas fa-headset text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900">Chat ho tro</h1>
                    <p class="mt-1 text-sm text-slate-500">Trao doi truc tiep voi shop theo thoi gian gan realtime.</p>
                </div>
            </div>

            <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-5">
                @if($supportAdmin)
                    <p class="text-sm font-semibold text-slate-700">Dang ket noi voi</p>
                    <p class="mt-2 text-lg font-bold text-slate-900">{{ $supportAdmin->name }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $supportAdmin->email }}</p>
                @else
                    <p class="text-sm text-amber-700">Chua co tai khoan admin san sang tiep nhan hoi thoai.</p>
                @endif
            </div>

            <div class="mt-6 space-y-4 text-sm text-slate-500">
                <div class="flex items-start gap-3">
                    <i class="fas fa-circle text-[8px] mt-1.5 text-emerald-500"></i>
                    <span>Khung chat se tu dong lam moi moi 4 giay.</span>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fas fa-circle text-[8px] mt-1.5 text-emerald-500"></i>
                    <span>Phu hop cho tu van don hang, giao hang va ho tro sau mua.</span>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-5">
                <h2 class="text-lg font-bold text-slate-900">Hoi thoai voi shop</h2>
            </div>

            <div id="support-chat-root" class="flex min-h-[560px] flex-col" data-enabled="{{ $supportAdmin ? '1' : '0' }}">
                @if($supportAdmin)
                    <div id="support-chat-messages" class="flex-1 space-y-4 overflow-y-auto bg-slate-50 px-6 py-6">
                        @foreach($messages as $message)
                            @php
                                $mine = $message->sender_id === auth()->id();
                            @endphp
                            <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-[75%] rounded-3xl px-4 py-3 {{ $mine ? 'bg-emerald-600 text-white' : 'bg-white text-slate-800 border border-slate-200' }}">
                                    <p class="text-sm leading-6">{{ $message->message }}</p>
                                    <p class="mt-2 text-[11px] {{ $mine ? 'text-emerald-100' : 'text-slate-400' }}">{{ $message->created_at?->format('H:i') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <form id="support-chat-form" class="border-t border-slate-100 p-5">
                        @csrf
                        <div class="flex items-end gap-3">
                            <textarea id="support-chat-input" rows="2" class="min-h-[58px] flex-1 resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-50" placeholder="Nhap noi dung can ho tro..."></textarea>
                            <button type="submit" class="inline-flex h-12 items-center justify-center rounded-2xl bg-emerald-600 px-5 font-bold text-white transition hover:bg-emerald-700">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                        <p id="support-chat-error" class="mt-3 text-sm text-red-500 hidden"></p>
                    </form>
                @else
                    <div class="flex flex-1 items-center justify-center px-6 py-12 text-center text-slate-500">
                        Chua co admin de bat dau cuoc tro chuyen.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (() => {
        const root = document.getElementById('support-chat-root');

        if (!root || root.dataset.enabled !== '1') {
            return;
        }

        const messagesUrl = @json(route('chat.messages'));
        const sendUrl = @json(route('chat.send'));
        const csrfToken = @json(csrf_token());
        const list = document.getElementById('support-chat-messages');
        const form = document.getElementById('support-chat-form');
        const input = document.getElementById('support-chat-input');
        const errorBox = document.getElementById('support-chat-error');

        const escapeHtml = (value) => String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

        const scrollToBottom = () => {
            list.scrollTop = list.scrollHeight;
        };

        const shouldStickBottom = () => list.scrollTop + list.clientHeight >= list.scrollHeight - 80;

        const renderMessages = (messages) => {
            const stick = shouldStickBottom();

            list.innerHTML = messages.map((message) => `
                <div class="flex ${message.mine ? 'justify-end' : 'justify-start'}">
                    <div class="max-w-[75%] rounded-3xl px-4 py-3 ${message.mine ? 'bg-emerald-600 text-white' : 'bg-white text-slate-800 border border-slate-200'}">
                        <p class="text-sm leading-6">${escapeHtml(message.message)}</p>
                        <p class="mt-2 text-[11px] ${message.mine ? 'text-emerald-100' : 'text-slate-400'}">${escapeHtml(message.time ?? '')}</p>
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
