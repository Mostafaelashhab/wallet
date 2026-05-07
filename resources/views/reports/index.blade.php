<x-layouts.app :title="__('app.reports')">
    <x-header-bar :title="__('app.reports')" :back="route('dashboard')" />

    <div class="px-5">
        @php
            $prev = $month->copy()->subMonth()->format('Y-m');
            $next = $month->copy()->addMonth()->format('Y-m');
            $net = $income - $expense;
        @endphp

        {{-- Month nav --}}
        <div class="glass flex items-center justify-between !p-3 rounded-2xl">
            <a href="{{ route('reports.index') }}?month={{ $prev }}" class="w-9 h-9 rounded-full bg-white/10 grid place-items-center text-white">
                <x-icon name="chevron-right" :size="18" class="flip-rtl" />
            </a>
            <div class="text-center">
                <p class="font-semibold text-white">{{ $month->translatedFormat('F Y') }}</p>
                <p class="text-[11px] text-white/50">{{ __('app.monthly_report') }}</p>
            </div>
            <a href="{{ route('reports.index') }}?month={{ $next }}" class="w-9 h-9 rounded-full bg-white/10 grid place-items-center text-white">
                <x-icon name="chevron-left" :size="18" class="flip-rtl" />
            </a>
        </div>

        <div class="grid grid-cols-3 gap-2 mt-3">
            <div class="glass !rounded-2xl p-3 text-center">
                <div class="w-9 h-9 mx-auto rounded-xl bg-emerald-500/20 text-emerald-300 grid place-items-center border border-emerald-400/20">
                    <x-icon name="arrow-down" :size="16" />
                </div>
                <p class="text-[11px] text-white/55 mt-2">{{ __('app.income') }}</p>
                <p class="font-bold text-emerald-300 text-base leading-tight num">{{ number_format($income, 0) }}</p>
            </div>
            <div class="glass !rounded-2xl p-3 text-center">
                <div class="w-9 h-9 mx-auto rounded-xl bg-rose-500/20 text-rose-300 grid place-items-center border border-rose-400/20">
                    <x-icon name="arrow-up" :size="16" />
                </div>
                <p class="text-[11px] text-white/55 mt-2">{{ __('app.expense') }}</p>
                <p class="font-bold text-rose-300 text-base leading-tight num">{{ number_format($expense, 0) }}</p>
            </div>
            <div class="glass !rounded-2xl p-3 text-center">
                <div class="w-9 h-9 mx-auto rounded-xl bg-white/10 text-white grid place-items-center border border-white/10">
                    <x-icon name="sparkles" :size="16" />
                </div>
                <p class="text-[11px] text-white/55 mt-2">{{ __('app.net') }}</p>
                <p class="font-bold text-base leading-tight num {{ $net >= 0 ? 'text-emerald-300' : 'text-rose-300' }}">
                    {{ $net >= 0 ? '+' : '' }}{{ number_format($net, 0) }}
                </p>
            </div>
        </div>

        @php $maxDaily = max(1, max($dailySpend ?: [0])); @endphp
        <div class="glass mt-4 p-4 rounded-2xl">
            <div class="flex items-center justify-between mb-3">
                <p class="font-semibold text-sm text-white">{{ __('app.daily_spend') }}</p>
                <span class="text-[11px] text-white/40">{{ count($dailySpend) }} {{ app()->getLocale() === 'ar' ? 'يوم' : 'days' }}</span>
            </div>
            @if ($expense > 0)
                <div class="flex items-end gap-1 h-24">
                    @foreach ($dailySpend as $day => $val)
                        @php $h = $val > 0 ? max(8, ($val / $maxDaily) * 100) : 4; @endphp
                        <div class="flex-1 rounded-t-md min-h-[4px]"
                             style="height: {{ $h }}%; background: {{ $val > 0 ? 'linear-gradient(180deg,#818cf8 0%,#6366F1 100%)' : 'rgba(255,255,255,0.06)' }};"></div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6">
                    <div class="w-12 h-12 mx-auto rounded-full bg-white/10 grid place-items-center text-white/60 border border-white/10">
                        <x-icon name="chart" :size="22" />
                    </div>
                    <p class="text-white/55 text-sm mt-2">{{ __('app.no_spend_month') }}</p>
                </div>
            @endif
        </div>

        <h3 class="section-title mt-6 mb-2">{{ __('app.expenses_by_category') }}</h3>
        <div class="glass space-y-3 p-4 rounded-2xl">
            @forelse ($byCategory as $row)
                @php
                    $cat = $row['category'];
                    $pct = $expense > 0 ? round($row['total'] / $expense * 100) : 0;
                @endphp
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl grid place-items-center text-white shrink-0" style="background: {{ $cat?->color ?? '#64748B' }}">
                        <x-icon name="{{ $cat?->icon_name ?? 'other' }}" :size="18" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="font-semibold text-sm text-white">{{ $cat?->name() ?? __('app.uncategorized') }}</p>
                            <p class="font-bold text-sm text-white num">{{ number_format($row['total'], 0) }}</p>
                        </div>
                        <div class="mt-1.5 h-1.5 bg-white/8 rounded-full overflow-hidden">
                            <div class="h-full" style="width: {{ $pct }}%; background: {{ $cat?->color ?? '#999' }};"></div>
                        </div>
                        <p class="text-[11px] text-white/40 mt-1">{{ $pct }}% · {{ $row['count'] }} {{ app()->getLocale() === 'ar' ? 'حركة' : 'tx' }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-6">
                    <div class="w-12 h-12 mx-auto rounded-full bg-white/10 grid place-items-center text-white/60 border border-white/10">
                        <x-icon name="search" :size="22" />
                    </div>
                    <p class="text-white/55 text-sm mt-2">{{ $expense > 0 ? __('app.no_expenses_month') : __('app.log_first_expense') }}</p>
                    @if ($expense == 0)
                        <a href="{{ route('transactions.create') }}?type=expense" class="btn-primary !py-2 mt-3 inline-flex w-auto px-5 text-sm">
                            {{ __('app.tx_log_expense') }}
                        </a>
                    @endif
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.app>
