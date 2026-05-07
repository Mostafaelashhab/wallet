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
            <x-icon name="check" :size="18" /> {{ session('flash') }}
        </div>
    @endif

    {{-- Balance hero with sparkline --}}
    <div class="px-5 pt-3">
        <p class="text-white/50 text-xs uppercase tracking-widest">{{ __('app.net_balance') }}</p>
        <p class="display-amount text-white text-5xl mt-1 leading-none">
            {{ number_format($netWorth, 2) }}
            <span class="text-base font-semibold text-white/40">{{ __('app.currency_symbol') }}</span>
        </p>

        @if (count($sparkline) > 1)
            @php
                $min = min($sparkline); $max = max($sparkline);
                $range = max(1, $max - $min);
                $w = 360; $h = 60; $n = count($sparkline) - 1;
                $points = [];
                foreach ($sparkline as $i => $v) {
                    $x = $n > 0 ? ($i / $n) * $w : $w / 2;
                    $y = $h - (($v - $min) / $range) * $h;
                    $points[] = round($x, 1) . ',' . round($y, 1);
                }
                $line = implode(' ', $points);
                $area = '0,' . $h . ' ' . $line . ' ' . $w . ',' . $h;
            @endphp
            <div class="mt-3">
                <svg viewBox="0 0 {{ $w }} {{ $h }}" class="w-full h-12" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="spark" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="0%" stop-color="#818cf8" stop-opacity="0.5"/>
                            <stop offset="100%" stop-color="#818cf8" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                    <polygon points="{{ $area }}" fill="url(#spark)" />
                    <polyline points="{{ $line }}" fill="none" stroke="#a5b4fc" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke"/>
                </svg>
            </div>
        @endif

        <div class="grid grid-cols-2 gap-2.5 mt-4">
            <div class="glass !rounded-2xl p-3 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-300 grid place-items-center shrink-0 border border-emerald-400/20">
                    <x-icon name="arrow-down" :size="18" />
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] text-white/55">{{ __('app.income_month') }}</p>
                    <p class="font-bold text-white text-base leading-tight truncate num">+{{ number_format($monthSummary['income'], 0) }}</p>
                </div>
            </div>
            <div class="glass !rounded-2xl p-3 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-500/20 text-rose-300 grid place-items-center shrink-0 border border-rose-400/20">
                    <x-icon name="arrow-up" :size="18" />
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] text-white/55">{{ __('app.expense_month') }}</p>
                    <p class="font-bold text-white text-base leading-tight truncate num">−{{ number_format($monthSummary['expense'], 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Smart Insights --}}
    @if (!empty($insights))
        <div class="mt-6 px-5 flex items-center gap-2 mb-2">
            <x-icon name="sparkles" :size="16" class="text-indigo-300" />
            <h3 class="section-title">{{ __('app.insights') }}</h3>
        </div>
        <div class="px-5 space-y-2">
            @foreach ($insights as $ins)
                @php
                    $tones = [
                        'emerald' => ['bg-emerald-500/15 border-emerald-400/20', 'text-emerald-300'],
                        'rose'    => ['bg-rose-500/15 border-rose-400/20', 'text-rose-300'],
                        'sky'     => ['bg-sky-500/15 border-sky-400/20', 'text-sky-300'],
                        'amber'   => ['bg-amber-500/15 border-amber-400/20', 'text-amber-300'],
                    ];
                    [$cls, $iconColor] = $tones[$ins['tone']] ?? $tones['sky'];
                @endphp
                <div class="rounded-2xl border p-3 flex items-start gap-3 {{ $cls }}">
                    <div class="w-9 h-9 rounded-xl bg-white/10 grid place-items-center {{ $iconColor }} shrink-0">
                        <x-icon name="{{ $ins['icon'] }}" :size="18" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-white text-sm">{{ $ins['title'] }}</p>
                        <p class="text-[12px] text-white/65 mt-0.5">{{ $ins['body'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Wallets --}}
    <div class="mt-6 px-5 flex items-end justify-between">
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
    </div>

    {{-- Budgets --}}
    @if ($budgets->count())
        <div class="mt-6 px-5 flex items-end justify-between">
            <h3 class="section-title">{{ __('app.budgets') }}</h3>
            <a href="{{ route('budgets.index') }}" class="text-white/60 text-sm">{{ __('app.view_all') }}</a>
        </div>
        <div class="px-5 mt-3 grid grid-cols-2 gap-2.5">
            @foreach ($budgets as $b)
                @php $p = $b->p; $color = $p['state'] === 'over' ? '#F43F5E' : ($p['state'] === 'warn' ? '#F59E0B' : '#10B981'); @endphp
                <a href="{{ route('budgets.index') }}" class="glass-soft p-3 rounded-2xl block">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl grid place-items-center text-white shrink-0" style="background: {{ $b->category->color }}">
                            <x-icon name="{{ $b->category->icon_name }}" :size="16" />
                        </div>
                        <p class="font-semibold text-sm text-white truncate flex-1">{{ $b->category->name() }}</p>
                    </div>
                    <p class="text-[11px] text-white/55 mt-2 num">{{ number_format($p['spent'], 0) }} / {{ number_format($p['amount'], 0) }}</p>
                    <div class="mt-1.5 h-1.5 bg-white/8 rounded-full overflow-hidden">
                        <div class="h-full" style="width: {{ $p['percent'] }}%; background: {{ $color }};"></div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <a href="{{ route('budgets.create') }}" class="mx-5 mt-6 glass !rounded-2xl !p-4 flex items-center gap-3 tap-anim block">
            <div class="w-10 h-10 rounded-xl bg-indigo-500/20 text-indigo-300 grid place-items-center border border-indigo-400/20"><x-icon name="target" :size="18" /></div>
            <div class="flex-1">
                <p class="font-semibold text-white text-sm">{{ __('app.budgets_add_cta') }}</p>
                <p class="text-[11px] text-white/55">{{ __('app.budgets_add_subtitle') }}</p>
            </div>
            <x-icon name="chevron-right" :size="18" class="text-white/40 flip-rtl" />
        </a>
    @endif

    {{-- Subscriptions --}}
    @if ($monthlySubs > 0 || $upcoming->count())
        <div class="mt-6 px-5 flex items-end justify-between">
            <div>
                <h3 class="section-title">{{ __('app.subscriptions') }}</h3>
                <p class="section-sub num">{{ number_format($monthlySubs, 0) }} {{ __('app.currency_symbol') }} / {{ __('app.month') }}</p>
            </div>
            <a href="{{ route('subscriptions.index') }}" class="text-white/60 text-sm">{{ __('app.view_all') }}</a>
        </div>
        <div class="px-5 mt-3 space-y-2">
            @foreach ($upcoming as $s)
                <div class="glass-soft !rounded-2xl !p-3 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl grid place-items-center text-white shrink-0" style="background: {{ $s->color }}">
                        <x-icon name="{{ $s->icon_name }}" :size="18" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-white text-sm truncate">{{ $s->name }}</p>
                        <p class="text-[11px] text-amber-300">{{ __('app.subs_in_days', ['days' => max(0, $s->daysUntilBilling())]) }}</p>
                    </div>
                    <p class="font-bold text-white num text-sm">{{ number_format($s->amount, 0) }}</p>
                </div>
            @endforeach
        </div>
    @else
        <a href="{{ route('subscriptions.create') }}" class="mx-5 mt-3 glass !rounded-2xl !p-4 flex items-center gap-3 tap-anim block">
            <div class="w-10 h-10 rounded-xl bg-violet-500/20 text-violet-300 grid place-items-center border border-violet-400/20"><x-icon name="sparkles" :size="18" /></div>
            <div class="flex-1">
                <p class="font-semibold text-white text-sm">{{ __('app.subs_add_cta') }}</p>
                <p class="text-[11px] text-white/55">{{ __('app.subs_add_subtitle') }}</p>
            </div>
            <x-icon name="chevron-right" :size="18" class="text-white/40 flip-rtl" />
        </a>
    @endif

    {{-- Recent transactions --}}
    <div class="mt-6 px-5 flex items-end justify-between">
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
                @endphp
                <div class="w-10 h-10 rounded-xl grid place-items-center {{ $bg }} {{ $fg }}">
                    <x-icon name="{{ $tx->category?->icon_name ?? $icon }}" :size="20" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-white truncate text-sm">{{ $tx->description }}</p>
                    <p class="text-[11px] text-white/50 truncate">
                        {{ $tx->account->name }} · {{ $tx->occurred_at->translatedFormat('M j') }}
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
            </div>
        @endforelse
    </div>

    {{-- Shortcuts --}}
    <div class="mt-6 px-5 grid grid-cols-3 gap-2">
        <a href="{{ route('personal-recurring.index') }}" class="glass-soft p-3 rounded-2xl text-center tap-anim">
            <div class="w-10 h-10 mx-auto rounded-xl bg-sky-500/20 text-sky-300 grid place-items-center border border-sky-400/20"><x-icon name="arrow-swap" :size="18" /></div>
            <p class="text-[11px] mt-2 font-bold text-white">{{ __('app.recurring') }}</p>
        </a>
        <a href="{{ route('budgets.index') }}" class="glass-soft p-3 rounded-2xl text-center tap-anim">
            <div class="w-10 h-10 mx-auto rounded-xl bg-emerald-500/20 text-emerald-300 grid place-items-center border border-emerald-400/20"><x-icon name="target" :size="18" /></div>
            <p class="text-[11px] mt-2 font-bold text-white">{{ __('app.budgets') }}</p>
        </a>
        <a href="{{ route('subscriptions.index') }}" class="glass-soft p-3 rounded-2xl text-center tap-anim">
            <div class="w-10 h-10 mx-auto rounded-xl bg-violet-500/20 text-violet-300 grid place-items-center border border-violet-400/20"><x-icon name="sparkles" :size="18" /></div>
            <p class="text-[11px] mt-2 font-bold text-white">{{ __('app.subscriptions') }}</p>
        </a>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('pwa:installable', () => {
            // could show a hint
        });
    </script>
    @endpush
</x-layouts.app>
