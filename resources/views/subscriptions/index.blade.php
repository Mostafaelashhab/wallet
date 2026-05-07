<x-layouts.app :title="__('app.subscriptions')">
    <x-header-bar :title="__('app.subscriptions')" :back="route('dashboard')">
        <a href="{{ route('subscriptions.create') }}" class="w-9 h-9 rounded-full bg-white/10 border border-white/10 grid place-items-center text-white">
            <x-icon name="plus" :size="18" />
        </a>
    </x-header-bar>

    <div class="px-5">
        <div class="glass p-5 rounded-3xl text-center">
            <p class="text-[11px] text-white/55 uppercase tracking-widest">{{ __('app.subs_monthly_total') }}</p>
            <p class="display-amount text-3xl text-white mt-1 num">{{ number_format($monthlyTotal, 0) }} <span class="text-sm text-white/40">{{ __('app.currency_symbol') }}</span></p>
            <p class="text-[11px] text-white/55 mt-1">{{ $subscriptions->where('active', true)->count() }} {{ __('app.subs_active') }}</p>
        </div>

        @if ($upcoming->count())
            <h3 class="section-title mt-6 mb-2">{{ __('app.subs_upcoming') }}</h3>
            <div class="space-y-2">
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
        @endif

        <h3 class="section-title mt-6 mb-2">{{ __('app.subs_all') }}</h3>
        <div class="space-y-2">
            @forelse ($subscriptions as $s)
                <div class="glass !rounded-2xl !p-3 flex items-center gap-3 {{ $s->active ? '' : 'opacity-50' }}">
                    <div class="w-11 h-11 rounded-xl grid place-items-center text-white shrink-0" style="background: {{ $s->color }}">
                        <x-icon name="{{ $s->icon_name }}" :size="20" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-white text-sm truncate">{{ $s->name }}</p>
                        <p class="text-[11px] text-white/50">
                            {{ __('app.subs_freq_' . $s->frequency) }}
                            · {{ __('app.subs_next', ['date' => $s->next_billing_at->translatedFormat('M j')]) }}
                        </p>
                    </div>
                    <div class="text-end">
                        <p class="font-bold text-white num">{{ number_format($s->amount, 0) }}</p>
                        <p class="text-[10px] text-white/40">{{ $s->currency }}</p>
                    </div>
                    <form method="POST" action="{{ route('subscriptions.destroy', $s) }}" onsubmit="return confirm('?')" class="shrink-0">
                        @csrf @method('DELETE')
                        <button class="text-white/40 px-1"><x-icon name="trash" :size="14" /></button>
                    </form>
                </div>
            @empty
                <div class="glass !rounded-3xl text-center py-8">
                    <div class="w-14 h-14 mx-auto rounded-full bg-white/10 grid place-items-center text-white/70 border border-white/10">
                        <x-icon name="sparkles" :size="24" />
                    </div>
                    <p class="font-bold text-white mt-3">{{ __('app.subs_empty_title') }}</p>
                    <p class="text-white/55 text-xs mt-1">{{ __('app.subs_empty_subtitle') }}</p>
                    <a href="{{ route('subscriptions.create') }}" class="btn-primary mt-4 inline-flex w-auto px-5 py-2 text-sm">{{ __('app.subs_add') }}</a>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.app>
