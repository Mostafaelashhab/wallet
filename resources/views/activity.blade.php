<x-layouts.app :title="__('app.activity')">
    <x-header-bar :title="__('app.activity')" :back="route('dashboard')" />

    <div class="px-5">
        <div class="glass space-y-3 p-4 rounded-2xl">
            @forelse ($items as $i)
                @php
                    $meta = $i->meta ?? [];
                    [$icon, $bg, $fg, $msg] = match ($i->action) {
                        'tx.income'   => ['arrow-down', 'bg-emerald-500/20 border border-emerald-400/20', 'text-emerald-300', $i->user->name . ' · ' . number_format($meta['amount'] ?? 0, 2) . ' ' . ($meta['currency'] ?? '') . ' (' . ($meta['account'] ?? '') . ')'],
                        'tx.expense'  => ['arrow-up', 'bg-rose-500/20 border border-rose-400/20', 'text-rose-300', $i->user->name . ' · ' . number_format($meta['amount'] ?? 0, 2) . ' (' . ($meta['account'] ?? '') . ')'],
                        'tx.transfer' => ['transfer', 'bg-sky-500/20 border border-sky-400/20', 'text-sky-300', $i->user->name . ' · ' . number_format($meta['amount'] ?? 0, 2)],
                        'expense.created' => ['arrow-up', 'bg-rose-500/20 border border-rose-400/20', 'text-rose-300', ($meta['description'] ?? '') . ' · ' . number_format($meta['amount'] ?? 0, 2) . ' ' . ($meta['currency'] ?? '')],
                        default => ['bell', 'bg-white/10 border border-white/10', 'text-white/70', $i->action],
                    };
                @endphp
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl grid place-items-center {{ $bg }} {{ $fg }} shrink-0">
                        <x-icon name="{{ $icon }}" :size="18" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-white">{{ $msg }}</p>
                        <p class="text-[11px] text-white/40 mt-0.5">{{ $i->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-6">
                    <div class="w-12 h-12 mx-auto rounded-full bg-white/10 grid place-items-center text-white/60 border border-white/10">
                        <x-icon name="activity" :size="22" />
                    </div>
                    <p class="text-white/55 text-sm mt-3">{{ __('app.activity_empty') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.app>
