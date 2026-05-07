<x-layouts.app :title="__('app.budgets')">
    <x-header-bar :title="__('app.budgets')" :back="route('dashboard')">
        <a href="{{ route('budgets.create') }}" class="w-9 h-9 rounded-full bg-white/10 border border-white/10 grid place-items-center text-white">
            <x-icon name="plus" :size="18" />
        </a>
    </x-header-bar>

    <div class="px-5">
        <div class="glass p-5 rounded-3xl text-center">
            <p class="text-[11px] text-white/55 uppercase tracking-widest">{{ __('app.budget_total_month', ['month' => $month->translatedFormat('F')]) }}</p>
            <p class="display-amount text-3xl text-white mt-1">{{ number_format($totalSpent, 0) }} <span class="text-sm text-white/40">/ {{ number_format($totalLimit, 0) }} {{ __('app.currency_symbol') }}</span></p>
            @if ($totalLimit > 0)
                @php $totPct = min(100, round($totalSpent / $totalLimit * 100)); @endphp
                <div class="mt-3 h-2 bg-white/8 rounded-full overflow-hidden">
                    <div class="h-full" style="width: {{ $totPct }}%; background: {{ $totPct >= 100 ? '#F43F5E' : ($totPct >= 80 ? '#F59E0B' : '#10B981') }};"></div>
                </div>
                <p class="text-[11px] text-white/55 mt-2">{{ $totPct }}%</p>
            @endif
        </div>

        <div class="mt-4 space-y-3">
            @forelse ($budgets as $b)
                @php $p = $b->p; $color = $p['state'] === 'over' ? '#F43F5E' : ($p['state'] === 'warn' ? '#F59E0B' : '#10B981'); @endphp
                <div class="glass p-4 rounded-2xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl grid place-items-center text-white shrink-0" style="background: {{ $b->category->color }}">
                            <x-icon name="{{ $b->category->icon_name }}" :size="18" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-white text-sm truncate">{{ $b->category->name() }}</p>
                            <p class="text-[11px] text-white/55 num">{{ number_format($p['spent'], 0) }} / {{ number_format($p['amount'], 0) }} {{ $b->currency }}</p>
                        </div>
                        <form method="POST" action="{{ route('budgets.destroy', $b) }}" onsubmit="return confirm('?')">
                            @csrf @method('DELETE')
                            <button class="text-white/40"><x-icon name="trash" :size="16" /></button>
                        </form>
                    </div>
                    <div class="mt-3 h-2 bg-white/8 rounded-full overflow-hidden">
                        <div class="h-full transition-all" style="width: {{ $p['percent'] }}%; background: {{ $color }};"></div>
                    </div>
                    <div class="flex items-center justify-between mt-2 text-[11px]">
                        <span class="text-white/55">{{ $p['percent'] }}%</span>
                        <span class="num {{ $p['state'] === 'over' ? 'text-rose-300' : 'text-white/55' }}">
                            @if ($p['state'] === 'over')
                                {{ __('app.budget_over_by', ['amount' => number_format($p['spent'] - $p['amount'], 0)]) }}
                            @else
                                {{ __('app.budget_remaining', ['amount' => number_format($p['remaining'], 0)]) }}
                            @endif
                        </span>
                    </div>
                </div>
            @empty
                <div class="glass !rounded-3xl text-center py-8 px-5">
                    <div class="w-14 h-14 mx-auto rounded-full bg-white/10 grid place-items-center text-white/70 border border-white/10">
                        <x-icon name="target" :size="24" />
                    </div>
                    <p class="font-bold text-white mt-3">{{ __('app.budgets_empty_title') }}</p>
                    <p class="text-white/55 text-xs mt-1">{{ __('app.budgets_empty_subtitle') }}</p>
                    <a href="{{ route('budgets.create') }}" class="btn-primary mt-4 inline-flex w-auto px-5 py-2 text-sm">{{ __('app.budgets_create') }}</a>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.app>
