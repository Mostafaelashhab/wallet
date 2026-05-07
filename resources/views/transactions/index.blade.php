<x-layouts.app :title="__('app.tx_all')">
    <x-header-bar :title="__('app.tx_all')" :back="route('dashboard')" />

    <div class="px-5">
        <div class="flex gap-1 glass-soft rounded-full p-1 text-xs font-bold mb-4">
            @php $base = route('transactions.index'); @endphp
            <a href="{{ $base }}" class="text-center py-2 rounded-full flex-1 {{ !$type ? 'bg-white text-ink-950' : 'text-white/60' }}">{{ __('app.tx_filter_all') }}</a>
            <a href="{{ $base }}?type=expense" class="text-center py-2 rounded-full flex-1 {{ $type === 'expense' ? 'bg-white text-ink-950' : 'text-white/60' }}">{{ __('app.tx_expense') }}</a>
            <a href="{{ $base }}?type=income" class="text-center py-2 rounded-full flex-1 {{ $type === 'income' ? 'bg-white text-ink-950' : 'text-white/60' }}">{{ __('app.tx_income') }}</a>
            <a href="{{ $base }}?type=transfer" class="text-center py-2 rounded-full flex-1 {{ $type === 'transfer' ? 'bg-white text-ink-950' : 'text-white/60' }}">{{ __('app.tx_transfer') }}</a>
        </div>

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
                        <p class="text-[11px] text-white/50 truncate">{{ $tx->account->name }} · {{ $tx->occurred_at->translatedFormat('M j') }}</p>
                    </div>
                    <p class="font-bold whitespace-nowrap num {{ $tx->isIncome() ? 'text-emerald-300' : ($tx->isExpense() ? 'text-rose-300' : 'text-sky-300') }}">
                        {{ $tx->isIncome() ? '+' : ($tx->isExpense() ? '−' : '') }}{{ number_format($tx->amount, 0) }}
                    </p>
                </a>
            @empty
                <div class="glass !rounded-3xl text-center py-8">
                    <p class="text-white/55 text-sm">{{ __('app.tx_empty_recent') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.app>
