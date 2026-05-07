<x-layouts.app :title="$account->name">
    <x-header-bar :title="$account->name" :back="route('accounts.index')" />

    <div class="px-5">
        <div class="wallet-card" style="background: linear-gradient(135deg, {{ $account->color }} 0%, {{ $account->color }}99 60%, {{ $account->color }}cc 100%);">
            <div class="flex items-start justify-between relative">
                <div class="w-12 h-12 rounded-2xl bg-white/20 grid place-items-center backdrop-blur">
                    <x-icon name="{{ $account->type === 'cash' ? 'cash' : ($account->type === 'bank' ? 'bank' : ($account->type === 'card' ? 'card' : ($account->type === 'savings' ? 'piggy' : 'mobile'))) }}" :size="22" />
                </div>
                <span class="chip-on">{{ $account->currency }}</span>
            </div>
            <p class="text-xs text-white/75 mt-7 relative">{{ $account->institution ?: ucfirst($account->type) }}</p>
            <p class="text-base font-semibold relative">{{ $account->name }}</p>
            <p class="display-amount text-3xl mt-2 num relative">{{ number_format($balance, 2) }}</p>
            <p class="text-[11px] text-white/65 mt-2 relative">{{ __('app.wallet_opening_short') }}: {{ number_format($account->opening_balance, 2) }}</p>
        </div>

        <div class="grid grid-cols-3 gap-2 mt-4">
            <a href="{{ route('transactions.create') }}?type=expense&account={{ $account->id }}" class="glass !rounded-2xl !p-3 text-center tap-anim">
                <div class="w-10 h-10 mx-auto rounded-xl grid place-items-center bg-rose-500/20 border border-rose-400/20 text-rose-300">
                    <x-icon name="arrow-up" :size="18" />
                </div>
                <p class="text-[11px] mt-1.5 font-bold text-white">{{ __('app.tx_expense') }}</p>
            </a>
            <a href="{{ route('transactions.create') }}?type=income&account={{ $account->id }}" class="glass !rounded-2xl !p-3 text-center tap-anim">
                <div class="w-10 h-10 mx-auto rounded-xl grid place-items-center bg-emerald-500/20 border border-emerald-400/20 text-emerald-300">
                    <x-icon name="arrow-down" :size="18" />
                </div>
                <p class="text-[11px] mt-1.5 font-bold text-white">{{ __('app.tx_income') }}</p>
            </a>
            <a href="{{ route('transactions.create') }}?type=transfer&account={{ $account->id }}" class="glass !rounded-2xl !p-3 text-center tap-anim">
                <div class="w-10 h-10 mx-auto rounded-xl grid place-items-center bg-sky-500/20 border border-sky-400/20 text-sky-300">
                    <x-icon name="transfer" :size="18" />
                </div>
                <p class="text-[11px] mt-1.5 font-bold text-white">{{ __('app.tx_transfer') }}</p>
            </a>
        </div>

        <h3 class="section-title mt-6 mb-2">{{ __('app.tx_recent') }}</h3>
        <div class="space-y-2">
            @forelse ($transactions as $tx)
                @php
                    [$bg, $fg, $icon] = match ($tx->type) {
                        'income' => ['bg-emerald-500/20 border border-emerald-400/20', 'text-emerald-300', 'arrow-down'],
                        'transfer' => ['bg-sky-500/20 border border-sky-400/20', 'text-sky-300', 'transfer'],
                        default => ['bg-rose-500/20 border border-rose-400/20', 'text-rose-300', 'arrow-up'],
                    };
                @endphp
                <a href="{{ route('transactions.show', $tx) }}" class="glass-soft !rounded-2xl !p-3 flex items-center gap-3 tap-anim block">
                    <div class="w-10 h-10 rounded-xl grid place-items-center {{ $bg }} {{ $fg }}">
                        <x-icon name="{{ $tx->category?->icon_name ?? $icon }}" :size="20" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-white truncate text-sm">{{ $tx->description }}</p>
                        <p class="text-[11px] text-white/50">{{ $tx->occurred_at->translatedFormat('M j') }}</p>
                    </div>
                    <p class="font-bold num {{ $tx->isIncome() ? 'text-emerald-300' : ($tx->isExpense() ? 'text-rose-300' : 'text-sky-300') }}">
                        {{ $tx->isIncome() ? '+' : ($tx->isExpense() ? '−' : '') }}{{ number_format($tx->amount, 0) }}
                    </p>
                </a>
            @empty
                <div class="glass !rounded-3xl text-center py-8">
                    <div class="w-14 h-14 mx-auto rounded-full bg-white/10 grid place-items-center text-white/70 border border-white/10">
                        <x-icon name="sparkles" :size="22" />
                    </div>
                    <p class="text-white/55 text-sm mt-3">{{ __('app.tx_empty_account') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.app>
