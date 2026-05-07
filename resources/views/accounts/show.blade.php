<x-layouts.app :title="$account->name" hero="tall">
    <x-header-bar :title="$account->name" :back="route('accounts.index')" />

    <div class="px-5 -mt-2">
        <div class="rounded-3xl p-5 text-white shadow-lg" style="background: linear-gradient(135deg, {{ $account->color }} 0%, {{ $account->color }}cc 100%);">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 rounded-2xl bg-white/25 grid place-items-center">
                    <x-icon name="{{ $account->type === 'cash' ? 'cash' : ($account->type === 'bank' ? 'bank' : ($account->type === 'card' ? 'card' : ($account->type === 'savings' ? 'piggy' : 'mobile'))) }}" :size="22" />
                </div>
                <span class="text-[10px] uppercase tracking-wider font-bold opacity-80">{{ $account->currency }}</span>
            </div>
            <p class="text-sm opacity-85 mt-3">{{ $account->institution ?: ucfirst($account->type) }}</p>
            <p class="text-3xl font-extrabold leading-tight">{{ number_format($balance, 2) }}</p>
            <p class="text-[11px] opacity-80 mt-1">رصيد افتتاحي: {{ number_format($account->opening_balance, 2) }}</p>
        </div>

        <div class="grid grid-cols-3 gap-2 mt-4">
            <a href="{{ route('transactions.create') }}?type=expense&account={{ $account->id }}" class="card !p-3 text-center tap-anim">
                <div class="w-9 h-9 mx-auto rounded-2xl grid place-items-center bg-rose-100 text-rose-600"><x-icon name="arrow-up" :size="18" /></div>
                <p class="text-[11px] mt-1 font-semibold">مصروف</p>
            </a>
            <a href="{{ route('transactions.create') }}?type=income&account={{ $account->id }}" class="card !p-3 text-center tap-anim">
                <div class="w-9 h-9 mx-auto rounded-2xl grid place-items-center bg-emerald-100 text-emerald-600"><x-icon name="arrow-down" :size="18" /></div>
                <p class="text-[11px] mt-1 font-semibold">دخل</p>
            </a>
            <a href="{{ route('transactions.create') }}?type=transfer&account={{ $account->id }}" class="card !p-3 text-center tap-anim">
                <div class="w-9 h-9 mx-auto rounded-2xl grid place-items-center bg-sky-100 text-sky-600"><x-icon name="transfer" :size="18" /></div>
                <p class="text-[11px] mt-1 font-semibold">تحويل</p>
            </a>
        </div>

        <h3 class="section-title mt-6 mb-2">آخر الحركات</h3>
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
                        <p class="text-xs text-stone-500">{{ $tx->occurred_at->translatedFormat('M j') }}</p>
                    </div>
                    <p class="font-extrabold {{ $tx->isIncome() ? 'text-emerald-600' : ($tx->isExpense() ? 'text-rose-600' : 'text-sky-600') }}">
                        {{ $tx->isIncome() ? '+' : ($tx->isExpense() ? '-' : '') }}{{ number_format($tx->amount, 0) }}
                    </p>
                </a>
            @empty
                <div class="card text-center text-stone-500 py-6">مفيش حركات في المحفظة دي.</div>
            @endforelse
        </div>
    </div>
</x-layouts.app>
