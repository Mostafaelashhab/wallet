<x-layouts.app title="كل الحركات" hero="short">
    <x-header-bar title="الحركات" :back="route('dashboard')" />

    <div class="px-5">
        <div class="flex gap-1 bg-white/15 backdrop-blur rounded-full p-1 text-xs font-bold mb-4">
            @php $base = route('transactions.index'); @endphp
            <a href="{{ $base }}" class="text-center py-2 rounded-full flex-1 {{ !$type ? 'bg-white text-stone-900' : 'text-white/85' }}">الكل</a>
            <a href="{{ $base }}?type=expense" class="text-center py-2 rounded-full flex-1 {{ $type === 'expense' ? 'bg-white text-stone-900' : 'text-white/85' }}">مصروف</a>
            <a href="{{ $base }}?type=income" class="text-center py-2 rounded-full flex-1 {{ $type === 'income' ? 'bg-white text-stone-900' : 'text-white/85' }}">دخل</a>
            <a href="{{ $base }}?type=transfer" class="text-center py-2 rounded-full flex-1 {{ $type === 'transfer' ? 'bg-white text-stone-900' : 'text-white/85' }}">تحويل</a>
        </div>

        <div class="space-y-2.5">
            @forelse ($transactions as $tx)
                @php
                    [$bg, $fg, $icon] = match ($tx->type) {
                        'income' => ['bg-emerald-100', 'text-emerald-600', 'arrow-down'],
                        'transfer' => ['bg-sky-100', 'text-sky-600', 'transfer'],
                        default => ['bg-rose-100', 'text-rose-600', 'arrow-up'],
                    };
                @endphp
                <a href="{{ route('transactions.show', $tx) }}" class="card !p-3 flex items-center gap-3 tap-anim">
                    <div class="w-11 h-11 rounded-2xl grid place-items-center {{ $bg }} {{ $fg }}">
                        <x-icon name="{{ $tx->category?->icon_name ?? $icon }}" :size="22" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold truncate">{{ $tx->description }}</p>
                        <p class="text-xs text-stone-500 truncate">{{ $tx->account->name }} · {{ $tx->occurred_at->translatedFormat('M j') }}</p>
                    </div>
                    <p class="font-extrabold whitespace-nowrap {{ $tx->isIncome() ? 'text-emerald-600' : ($tx->isExpense() ? 'text-rose-600' : 'text-sky-600') }}">
                        {{ $tx->isIncome() ? '+' : ($tx->isExpense() ? '-' : '') }}{{ number_format($tx->amount, 0) }}
                    </p>
                </a>
            @empty
                <div class="card text-center text-stone-500 py-6">مفيش حركات بعد.</div>
            @endforelse
        </div>
    </div>
</x-layouts.app>
