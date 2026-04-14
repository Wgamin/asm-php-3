<div
    class="relative"
    x-data="adminOrderNotifications(@js(route('admin.realtime.orders')))"
    x-init="init()"
>
    <button
        @click="toggle()"
        class="relative flex h-11 w-11 items-center justify-center rounded-2xl text-[var(--admin-text-muted)] transition hover:bg-[rgba(95,103,92,0.08)]"
        title="Đơn hàng mới"
    >
        <i class="fas fa-bell"></i>
        <template x-if="unreadCount > 0">
            <span class="absolute -right-1 -top-1 flex min-h-[18px] min-w-[18px] items-center justify-center rounded-full bg-[#ba1a1a] px-1 text-[10px] font-bold text-white ring-2 ring-white">
                <span x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
            </span>
        </template>
    </button>

    <div
        x-show="open"
        @click.away="open = false"
        x-cloak
        x-transition
        class="admin-glass absolute right-0 mt-3 w-[22rem] overflow-hidden rounded-[1.2rem] border border-[rgba(112,122,108,0.12)] shadow-[0_30px_60px_-30px_rgba(25,28,30,0.22)]"
    >
        <div class="flex items-start justify-between gap-4 px-5 py-4">
            <div>
                <h3 class="admin-headline text-base font-bold tracking-[-0.02em] text-[var(--admin-text)]">Thông báo đơn hàng</h3>
                <p class="mt-1 text-xs text-[var(--admin-text-muted)]">Tự làm mới mỗi 5 giây</p>
            </div>
            <button
                type="button"
                @click="refresh()"
                class="admin-action-icon h-9 w-9"
                title="Làm mới"
            >
                <i class="fas fa-rotate-right text-xs"></i>
            </button>
        </div>

        <div class="max-h-[24rem] overflow-y-auto px-2 pb-2">
            <template x-if="loading && items.length === 0">
                <div class="px-4 py-5 text-sm text-[var(--admin-text-muted)]">Đang tải thông báo...</div>
            </template>

            <template x-if="!loading && items.length === 0">
                <div class="admin-empty-state min-h-[10rem] px-4 py-6">
                    <i class="fas fa-bell-slash text-2xl opacity-30"></i>
                    <p class="text-sm">Chưa có đơn hàng mới để hiển thị.</p>
                </div>
            </template>

            <template x-for="item in items" :key="item.id">
                <a :href="item.url" class="mb-2 flex items-start gap-3 rounded-2xl px-3 py-3 transition hover:bg-[rgba(95,103,92,0.08)]">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-2xl bg-[rgba(32,98,35,0.1)] text-[#206223]">
                        <i class="fas fa-bag-shopping text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-bold text-[var(--admin-text)]" x-text="item.order_number"></p>
                        <p class="mt-1 text-xs text-[var(--admin-text-muted)]">
                            <span x-text="item.customer_name"></span>
                            <span class="mx-1">•</span>
                            <span x-text="item.total"></span>
                        </p>
                        <p class="mt-1 text-[11px] text-[rgba(95,103,92,0.8)]">
                            <span x-text="item.status"></span>
                            <span class="mx-1">•</span>
                            <span x-text="item.time"></span>
                        </p>
                    </div>
                </a>
            </template>
        </div>
    </div>
</div>

<script>
    function adminOrderNotifications(endpoint) {
        return {
            open: false,
            loading: false,
            items: [],
            unreadCount: 0,
            maxId: 0,
            poller: null,
            storageKey: 'admin-last-seen-order-id',
            endpoint,

            init() {
                this.refresh(true);
                this.poller = setInterval(() => this.refresh(false), 5000);
            },

            toggle() {
                this.open = !this.open;

                if (this.open) {
                    this.markAsSeen();
                }
            },

            async refresh(initialLoad = false) {
                this.loading = true;

                try {
                    const lastSeenId = this.getLastSeenId();
                    const separator = this.endpoint.includes('?') ? '&' : '?';
                    const response = await fetch(
                        `${this.endpoint}${separator}last_seen_id=${lastSeenId}&t=${Date.now()}`,
                        {
                            headers: { Accept: 'application/json' },
                            cache: 'no-store',
                        }
                    );

                    if (!response.ok) {
                        throw new Error('Không thể tải thông báo.');
                    }

                    const data = await response.json();
                    this.items = Array.isArray(data.items) ? data.items : [];
                    this.maxId = Number(data.max_id || 0);

                    if (initialLoad && !this.hasLastSeenId() && this.maxId > 0) {
                        this.setLastSeenId(this.maxId);
                        this.unreadCount = 0;
                    } else {
                        this.unreadCount = Number(data.unread_count || 0);
                    }

                    if (this.open) {
                        this.markAsSeen();
                    }
                } catch (error) {
                    console.error(error);
                } finally {
                    this.loading = false;
                }
            },

            markAsSeen() {
                if (this.maxId > 0) {
                    this.setLastSeenId(this.maxId);
                }

                this.unreadCount = 0;
            },

            hasLastSeenId() {
                return localStorage.getItem(this.storageKey) !== null;
            },

            getLastSeenId() {
                return Number(localStorage.getItem(this.storageKey) || 0);
            },

            setLastSeenId(value) {
                localStorage.setItem(this.storageKey, String(value));
            },
        };
    }
</script>
