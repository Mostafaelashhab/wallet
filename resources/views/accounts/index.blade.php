<x-layouts.app :title="__('app.wallets')">
    <x-header-bar :title="__('app.wallets')" :back="route('dashboard')" />

    <div class="px-5">
        <div class="text-center mt-2">
            <p class="text-white/55 text-xs uppercase tracking-widest">{{ __('app.wallet_total') }}</p>
            <p class="display-amount text-white text-4xl mt-1">
                {{ number_format($netWorth, 2) }}
                <span class="text-base text-white/40 font-semibold">{{ __('app.currency_symbol') }}</span>
            </p>
            <p class="text-white/40 text-[11px] mt-1">{{ $accounts->count() }} {{ __('app.wallets_active') }}</p>
        </div>

        <div class="mt-6 wallet-stack space-y-3">
            @foreach ($accounts as $a)
                <a href="{{ route('accounts.show', $a) }}" class="wallet-card block tap-anim" style="background: linear-gradient(135deg, {{ $a->color }} 0%, {{ $a->color }}99 60%, {{ $a->color }}cc 100%);">
                    <div class="flex items-start justify-between relative">
                        <div class="w-11 h-11 rounded-xl bg-white/20 grid place-items-center backdrop-blur">
                            <x-icon name="{{ $a->type === 'cash' ? 'cash' : ($a->type === 'bank' ? 'bank' : ($a->type === 'card' ? 'card' : ($a->type === 'savings' ? 'piggy' : 'mobile'))) }}" :size="20" />
                        </div>
                        <span class="chip-on">{{ $a->currency }}</span>
                    </div>
                    <p class="text-[11px] text-white/75 mt-7 relative">{{ $a->institution ?: ucfirst($a->type) }}</p>
                    <div class="flex items-end justify-between mt-1 relative">
                        <p class="font-semibold text-lg truncate">{{ $a->name }}</p>
                        <p class="display-amount text-2xl num">{{ number_format($a->current_balance, 0) }}</p>
                    </div>
                </a>
            @endforeach

            <a href="{{ route('accounts.create') }}" class="glass-soft block rounded-3xl p-5 grid place-items-center text-white/60">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-white/10 grid place-items-center">
                        <x-icon name="plus" :size="16" />
                    </div>
                    <span class="font-semibold text-sm">{{ __('app.wallet_new') }}</span>
                </div>
            </a>
        </div>
    </div>
</x-layouts.app>
