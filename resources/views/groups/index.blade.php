<x-layouts.app :title="__('app.groups')">
    <x-header-bar :title="__('app.groups')" :back="route('dashboard')" />

    <div class="px-5">
        <p class="text-stone-500 text-sm mb-3">عندك في {{ $groups->count() }} مجموعة.</p>

        <div class="space-y-3">
            @foreach ($groups as $g)
                <a href="{{ route('groups.show', $g) }}" class="card flex items-center gap-3 tap-anim">
                    <div class="w-12 h-12 rounded-2xl grid place-items-center text-white" style="background: {{ $g->color }}">
                        <x-icon name="{{ $g->icon_name ?: 'group' }}" :size="22" />
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold truncate">{{ $g->name }}</p>
                        <p class="text-xs text-stone-500">{{ $g->members->count() ?? '·' }} عضو · {{ number_format($g->total, 2) }} {{ $g->currency }}</p>
                    </div>
                    @php $bal = (float) $g->my_balance; @endphp
                    @if (abs($bal) < 0.01)
                        <span class="amount-pill muted text-xs">{{ __('app.settled_up') }}</span>
                    @elseif ($bal > 0)
                        <span class="amount-pill owed text-xs">+{{ number_format($bal, 2) }}</span>
                    @else
                        <span class="amount-pill owe text-xs">{{ number_format($bal, 2) }}</span>
                    @endif
                </a>
            @endforeach
        </div>

        <a href="{{ route('groups.create') }}" class="btn-primary mt-6 flex items-center justify-center gap-2">
            <x-icon name="plus" :size="18" /> مجموعة جديدة
        </a>
    </div>
</x-layouts.app>
