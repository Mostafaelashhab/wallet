<x-layouts.app :title="__('app.tx_all')">
    <x-header-bar :title="__('app.tx_all')" :back="route('dashboard')">
        <a href="{{ route('transactions.export') }}{{ $from || $to ? '?from=' . ($from ?: '') . '&to=' . ($to ?: '') : '' }}"
           class="w-9 h-9 rounded-full bg-white/10 border border-white/10 grid place-items-center text-white" title="CSV">
            <x-icon name="arrow-down" :size="16" />
        </a>
    </x-header-bar>

    <div class="px-5">
        {{-- Search bar --}}
        <form method="GET" class="glass-soft rounded-2xl flex items-center gap-2 px-3 py-2 mb-3">
            <x-icon name="search" :size="18" class="text-white/40 shrink-0" />
            <input type="search" name="q" value="{{ $q }}" placeholder="{{ __('app.tx_search') }}"
                   class="flex-1 bg-transparent outline-none text-white text-sm placeholder:text-white/40 min-w-0">
            @if ($type)<input type="hidden" name="type" value="{{ $type }}">@endif
            <button type="button" onclick="document.getElementById('filter-sheet').classList.toggle('hidden')"
                    class="text-white/60 shrink-0"><x-icon name="cog" :size="18" /></button>
        </form>

        {{-- Filter sheet --}}
        <div id="filter-sheet" class="glass-soft rounded-2xl p-3 mb-3 {{ ($from || $to || $accountId || $categoryId) ? '' : 'hidden' }}">
            <form method="GET" class="space-y-3">
                @if ($type)<input type="hidden" name="type" value="{{ $type }}">@endif
                @if ($q)<input type="hidden" name="q" value="{{ $q }}">@endif
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-[11px] text-white/55">{{ __('app.from') }}</label>
                        <input type="date" name="from" value="{{ $from }}" class="input !py-2 !px-3 text-xs">
                    </div>
                    <div>
                        <label class="text-[11px] text-white/55">{{ __('app.to') }}</label>
                        <input type="date" name="to" value="{{ $to }}" class="input !py-2 !px-3 text-xs">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-[11px] text-white/55">{{ __('app.wallet') }}</label>
                        <select name="account" class="input !py-2 !px-3 text-xs">
                            <option value="">—</option>
                            @foreach ($accounts as $a)<option value="{{ $a->id }}" @selected($accountId == $a->id)>{{ $a->name }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[11px] text-white/55">{{ __('app.category') }}</label>
                        <select name="cat" class="input !py-2 !px-3 text-xs">
                            <option value="">—</option>
                            @foreach ($categories as $c)<option value="{{ $c->id }}" @selected($categoryId == $c->id)>{{ $c->name() }}</option>@endforeach
                        </select>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="btn-primary !py-2 text-sm">{{ __('app.apply') }}</button>
                    <a href="{{ route('transactions.index') }}" class="btn-ghost !py-2 text-sm flex-1 text-center">{{ __('app.clear') }}</a>
                </div>
            </form>
        </div>

        {{-- Type pills --}}
        <div class="flex gap-1 glass-soft rounded-full p-1 text-xs font-bold mb-4">
            @php $base = route('transactions.index'); $qs = $q ? '&q=' . urlencode($q) : ''; @endphp
            <a href="{{ $base }}{{ $q ? '?q=' . urlencode($q) : '' }}" class="text-center py-2 rounded-full flex-1 {{ !$type ? 'bg-white text-ink-950' : 'text-white/60' }}">{{ __('app.tx_filter_all') }}</a>
            <a href="{{ $base }}?type=expense{{ $qs }}" class="text-center py-2 rounded-full flex-1 {{ $type === 'expense' ? 'bg-white text-ink-950' : 'text-white/60' }}">{{ __('app.tx_expense') }}</a>
            <a href="{{ $base }}?type=income{{ $qs }}" class="text-center py-2 rounded-full flex-1 {{ $type === 'income' ? 'bg-white text-ink-950' : 'text-white/60' }}">{{ __('app.tx_income') }}</a>
            <a href="{{ $base }}?type=transfer{{ $qs }}" class="text-center py-2 rounded-full flex-1 {{ $type === 'transfer' ? 'bg-white text-ink-950' : 'text-white/60' }}">{{ __('app.tx_transfer') }}</a>
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
