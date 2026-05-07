<x-layouts.app :title="__('app.recurring')">
    <x-header-bar :title="__('app.recurring')" :back="route('dashboard')">
        <a href="{{ route('personal-recurring.create') }}" class="w-9 h-9 rounded-full bg-white/10 border border-white/10 grid place-items-center text-white">
            <x-icon name="plus" :size="18" />
        </a>
    </x-header-bar>

    <div class="px-5">
        <p class="text-white/55 text-sm mb-3">{{ __('app.recurring_subtitle') }}</p>

        <div class="space-y-2">
            @forelse ($items as $r)
                @php
                    [$bg, $fg, $icon] = match ($r->type) {
                        'income' => ['bg-emerald-500/20 border border-emerald-400/20', 'text-emerald-300', 'arrow-down'],
                        'transfer' => ['bg-sky-500/20 border border-sky-400/20', 'text-sky-300', 'transfer'],
                        default => ['bg-rose-500/20 border border-rose-400/20', 'text-rose-300', 'arrow-up'],
                    };
                @endphp
                <div class="glass !rounded-2xl !p-3 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl grid place-items-center {{ $bg }} {{ $fg }} shrink-0">
                        <x-icon name="{{ $r->category?->icon_name ?? $icon }}" :size="18" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-white text-sm truncate">{{ $r->description }}</p>
                        <p class="text-[11px] text-white/50">
                            {{ __('app.subs_freq_' . $r->frequency) }}
                            · {{ __('app.recurring_next', ['date' => $r->next_run_at->translatedFormat('M j')]) }}
                        </p>
                    </div>
                    <p class="font-bold text-white num text-sm">
                        {{ $r->type === 'income' ? '+' : ($r->type === 'expense' ? '−' : '') }}{{ number_format($r->amount, 0) }}
                    </p>
                    <form method="POST" action="{{ route('personal-recurring.destroy', $r) }}" onsubmit="return confirm('?')" class="shrink-0">
                        @csrf @method('DELETE')
                        <button class="text-white/40 px-1"><x-icon name="trash" :size="14" /></button>
                    </form>
                </div>
            @empty
                <div class="glass !rounded-3xl text-center py-8">
                    <div class="w-14 h-14 mx-auto rounded-full bg-white/10 grid place-items-center text-white/70 border border-white/10">
                        <x-icon name="arrow-swap" :size="24" />
                    </div>
                    <p class="font-bold text-white mt-3">{{ __('app.recurring_empty_title') }}</p>
                    <p class="text-white/55 text-xs mt-1 leading-relaxed">{{ __('app.recurring_empty_subtitle') }}</p>
                    <a href="{{ route('personal-recurring.create') }}" class="btn-primary mt-4 inline-flex w-auto px-5 py-2 text-sm">{{ __('app.recurring_add') }}</a>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.app>
