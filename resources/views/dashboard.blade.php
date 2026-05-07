<x-layouts.app :title="__('app.app_name')">
    {{-- Header --}}
    <div class="app-header safe-top px-5 pb-3 flex items-center justify-between">
        <div class="flex items-center gap-3 min-w-0">
            <x-avatar :user="auth()->user()" :size="40" />
            <div class="min-w-0">
                <p class="text-white/55 text-[11px] leading-none">{{ __('app.greeting') }}</p>
                <p class="font-semibold text-white text-base truncate leading-tight">{{ auth()->user()->name }}</p>
            </div>
        </div>
        <a href="{{ route('notifications') }}" class="w-10 h-10 rounded-full glass-soft grid place-items-center text-white">
            <x-icon name="bell" :size="18" />
        </a>
    </div>

    @if (session('flash'))
        <div class="mx-5 mb-3 glass-strong text-emerald-200 text-sm rounded-2xl px-4 py-2.5 flex items-center gap-2">
            <x-icon name="check" :size="18" />
            {{ session('flash') }}
        </div>
    @endif

    {{-- Balance hero --}}
    <div class="px-5 pt-3">
        <p class="text-white/50 text-xs uppercase tracking-widest">{{ __('app.net_balance') }}</p>
        <p class="display-amount text-white text-5xl mt-1 leading-none">
            {{ number_format($netWorth, 2) }}
            <span class="text-base font-semibold text-white/40">{{ __('app.currency_symbol') }}</span>
        </p>

        <div class="grid grid-cols-2 gap-2.5 mt-5">
            <div class="glass !rounded-2xl p-3 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-300 grid place-items-center shrink-0 border border-emerald-400/20">
                    <x-icon name="arrow-down" :size="18" />
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] text-white/55">{{ __('app.income_month') }}</p>
                    <p class="font-bold text-white text-base leading-tight truncate">+{{ number_format($monthSummary['income'], 0) }}</p>
                </div>
            </div>
            <div class="glass !rounded-2xl p-3 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-500/20 text-rose-300 grid place-items-center shrink-0 border border-rose-400/20">
                    <x-icon name="arrow-up" :size="18" />
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] text-white/55">{{ __('app.expense_month') }}</p>
                    <p class="font-bold text-white text-base leading-tight truncate">−{{ number_format($monthSummary['expense'], 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Wallets stack (Apple Wallet style) --}}
    <div class="mt-7 px-5 flex items-end justify-between">
        <div>
            <h3 class="section-title">{{ __('app.wallets') }}</h3>
            <p class="section-sub">{{ $accounts->count() }} {{ __('app.wallets_active') }}</p>
        </div>
        <a href="{{ route('accounts.index') }}" class="text-white/60 text-sm">{{ __('app.view_all') }}</a>
    </div>
    <div class="px-5 mt-3 wallet-stack space-y-3">
        @foreach ($accounts->take(3) as $a)
            <a href="{{ route('accounts.show', $a) }}" class="wallet-card block tap-anim" style="background: linear-gradient(135deg, {{ $a->color }} 0%, {{ $a->color }}99 60%, {{ $a->color }}cc 100%);">
                <div class="flex items-start justify-between relative">
                    <div class="w-10 h-10 rounded-xl bg-white/20 grid place-items-center backdrop-blur">
                        <x-icon name="{{ $a->type === 'cash' ? 'cash' : ($a->type === 'bank' ? 'bank' : ($a->type === 'card' ? 'card' : ($a->type === 'savings' ? 'piggy' : 'mobile'))) }}" :size="18" />
                    </div>
                    <span class="chip-on">{{ $a->currency }}</span>
                </div>
                <p class="text-[11px] text-white/75 mt-5 relative">{{ $a->institution ?: ucfirst($a->type) }}</p>
                <div class="flex items-end justify-between mt-1 relative">
                    <p class="font-semibold truncate">{{ $a->name }}</p>
                    <p class="display-amount text-2xl num">{{ number_format($a->current_balance, 0) }}</p>
                </div>
            </a>
        @endforeach
        @if ($accounts->count() < 1)
            <a href="{{ route('accounts.create') }}" class="glass-soft block rounded-3xl p-6 text-center text-white/60">
                <x-icon name="plus" :size="22" class="mx-auto" />
                <p class="text-sm mt-2 font-semibold">{{ __('app.wallet_add') }}</p>
            </a>
        @endif
    </div>

    {{-- Recent transactions --}}
    <div class="mt-7 px-5 flex items-end justify-between">
        <h3 class="section-title">{{ __('app.tx_recent') }}</h3>
        <a href="{{ route('transactions.index') }}" class="text-white/60 text-sm">{{ __('app.view_all') }}</a>
    </div>
    <div class="px-5 mt-3 space-y-2">
        @forelse ($recentTx as $tx)
            <a href="{{ route('transactions.show', $tx) }}" class="glass-soft !rounded-2xl !p-3 flex items-center gap-3 tap-anim block">
                @php
                    [$bg, $fg, $icon] = match ($tx->type) {
                        'income' => ['bg-emerald-500/20 border border-emerald-400/20', 'text-emerald-300', 'arrow-down'],
                        'transfer' => ['bg-sky-500/20 border border-sky-400/20', 'text-sky-300', 'transfer'],
                        default => ['bg-rose-500/20 border border-rose-400/20', 'text-rose-300', 'arrow-up'],
                    };
                    $catIcon = $tx->category?->icon_name ?? $icon;
                @endphp
                <div class="w-10 h-10 rounded-xl grid place-items-center {{ $bg }} {{ $fg }}">
                    <x-icon name="{{ $catIcon }}" :size="20" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-white truncate text-sm">{{ $tx->description }}</p>
                    <p class="text-[11px] text-white/50 truncate">
                        {{ $tx->account->name }}
                        @if ($tx->isTransfer() && $tx->transferToAccount) → {{ $tx->transferToAccount->name }} @endif
                        · {{ $tx->occurred_at->translatedFormat('M j') }}
                    </p>
                </div>
                <p class="font-bold text-sm whitespace-nowrap num {{ $tx->isIncome() ? 'text-emerald-300' : ($tx->isExpense() ? 'text-rose-300' : 'text-sky-300') }}">
                    {{ $tx->isIncome() ? '+' : ($tx->isExpense() ? '−' : '') }}{{ number_format($tx->amount, 0) }}
                </p>
            </a>
        @empty
            <div class="glass !rounded-3xl text-center py-8 px-5">
                <div class="w-14 h-14 mx-auto rounded-full bg-white/10 grid place-items-center text-white/70 border border-white/10">
                    <x-icon name="sparkles" :size="24" />
                </div>
                <p class="font-bold text-white mt-3">{{ __('app.tx_empty_title') }}</p>
                <p class="text-white/55 text-xs mt-1 leading-relaxed">{{ __('app.tx_empty_subtitle') }}</p>
                <div class="grid grid-cols-2 gap-2 mt-4">
                    <a href="{{ route('transactions.create') }}?type=expense" class="btn-primary !py-2.5 text-sm flex items-center justify-center gap-1.5">
                        <x-icon name="arrow-up" :size="14" /> {{ __('app.tx_log_expense') }}
                    </a>
                    <a href="{{ route('transactions.create') }}?type=income" class="btn-ghost !py-2.5 text-sm flex items-center justify-center gap-1.5">
                        <x-icon name="arrow-down" :size="14" /> {{ __('app.tx_log_income') }}
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    {{-- PWA install hint --}}
    <div id="install-hint" class="mx-5 glass mt-6 hidden">
        <div class="flex items-center gap-3 p-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-500/25 grid place-items-center text-indigo-200 border border-indigo-400/20">
                <x-icon name="sparkles" :size="18" />
            </div>
            <div class="flex-1">
                <p class="font-semibold text-sm text-white">{{ __('app.install_app') }}</p>
                <p class="text-[11px] text-white/55">{{ __('app.install_subtitle') }}</p>
            </div>
            <button onclick="installPwa().then(() => document.getElementById('install-hint').classList.add('hidden'))" class="btn-ghost text-xs !py-2 !px-3">{{ __('app.install_btn') }}</button>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('pwa:installable', () => {
            document.getElementById('install-hint')?.classList.remove('hidden');
        });
    </script>
    @endpush
</x-layouts.app>
