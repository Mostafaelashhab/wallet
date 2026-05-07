<x-layouts.app title="التقارير" hero="tall">
    <x-header-bar title="التقارير" :back="route('dashboard')" />

    <div class="px-5 -mt-2">
        @php
            $prev = $month->copy()->subMonth()->format('Y-m');
            $next = $month->copy()->addMonth()->format('Y-m');
            $net = $income - $expense;
        @endphp
        <div class="flex items-center justify-between text-white">
            <a href="{{ route('reports.index') }}?month={{ $prev }}" class="w-9 h-9 rounded-full bg-white/15 grid place-items-center backdrop-blur">
                <x-icon name="chevron-right" :size="18" class="flip-rtl" />
            </a>
            <h2 class="font-bold text-lg">{{ $month->translatedFormat('F Y') }}</h2>
            <a href="{{ route('reports.index') }}?month={{ $next }}" class="w-9 h-9 rounded-full bg-white/15 grid place-items-center backdrop-blur">
                <x-icon name="chevron-left" :size="18" class="flip-rtl" />
            </a>
        </div>

        <div class="grid grid-cols-3 gap-2 mt-5">
            <div class="card !p-3 text-center">
                <p class="text-[11px] text-stone-500">دخل</p>
                <p class="font-extrabold text-emerald-600 text-lg leading-tight mt-1">{{ number_format($income, 0) }}</p>
            </div>
            <div class="card !p-3 text-center">
                <p class="text-[11px] text-stone-500">مصروف</p>
                <p class="font-extrabold text-rose-600 text-lg leading-tight mt-1">{{ number_format($expense, 0) }}</p>
            </div>
            <div class="card !p-3 text-center">
                <p class="text-[11px] text-stone-500">صافي</p>
                <p class="font-extrabold text-lg leading-tight mt-1 {{ $net >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ ($net >= 0 ? '+' : '') . number_format($net, 0) }}</p>
            </div>
        </div>

        {{-- Daily spend bar chart --}}
        @php
            $maxDaily = max(1, max($dailySpend ?: [0]));
        @endphp
        <div class="card mt-4">
            <p class="font-semibold text-sm mb-2">صرف يومي</p>
            <div class="flex items-end gap-1 h-24">
                @foreach ($dailySpend as $day => $val)
                    @php $h = max(2, ($val / $maxDaily) * 100); @endphp
                    <div class="flex-1 rounded-t-md" title="يوم {{ $day }}: {{ number_format($val, 0) }}"
                         style="height: {{ $h }}%; background: linear-gradient(180deg, #FF6B35 0%, #E94F1A 100%); opacity: {{ $val > 0 ? 1 : 0.18 }};"></div>
                @endforeach
            </div>
        </div>

        <h3 class="section-title mt-6 mb-2">المصاريف بالفئة</h3>
        <div class="card space-y-3">
            @forelse ($byCategory as $row)
                @php
                    $cat = $row['category'];
                    $pct = $expense > 0 ? round($row['total'] / $expense * 100) : 0;
                @endphp
                <div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl grid place-items-center text-white shrink-0" style="background: {{ $cat?->color ?? '#999' }}">
                            <x-icon name="{{ $cat?->icon_name ?? 'other' }}" :size="18" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="font-semibold text-sm">{{ $cat?->name() ?? 'غير مصنف' }}</p>
                                <p class="font-bold">{{ number_format($row['total'], 0) }}</p>
                            </div>
                            <div class="mt-1.5 h-1.5 bg-stone-100 rounded-full overflow-hidden">
                                <div class="h-full" style="width: {{ $pct }}%; background: {{ $cat?->color ?? '#999' }};"></div>
                            </div>
                            <p class="text-[11px] text-stone-500 mt-1">{{ $pct }}% · {{ $row['count'] }} حركة</p>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-stone-500 text-sm text-center py-3">مفيش مصاريف في الشهر ده.</p>
            @endforelse
        </div>
    </div>
</x-layouts.app>
