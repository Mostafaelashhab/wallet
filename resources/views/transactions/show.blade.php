<x-layouts.app :title="$transaction->description">
    <x-header-bar :title="$transaction->description" :back="route('dashboard')" />

    <div class="px-5">
        @php
            [$bg, $fg, $icon, $tone] = match ($transaction->type) {
                'income' => ['bg-emerald-500/20 border border-emerald-400/20', 'text-emerald-300', 'arrow-down', 'text-emerald-300'],
                'transfer' => ['bg-sky-500/20 border border-sky-400/20', 'text-sky-300', 'transfer', 'text-sky-300'],
                default => ['bg-rose-500/20 border border-rose-400/20', 'text-rose-300', 'arrow-up', 'text-rose-300'],
            };
        @endphp
        <div class="glass text-center p-6 rounded-3xl">
            <div class="w-16 h-16 mx-auto rounded-2xl grid place-items-center {{ $bg }} {{ $fg }}">
                <x-icon name="{{ $transaction->category?->icon_name ?? $icon }}" :size="28" />
            </div>
            <p class="display-amount text-4xl mt-4 num {{ $tone }}">
                {{ $transaction->isIncome() ? '+' : ($transaction->isExpense() ? '−' : '') }}{{ number_format($transaction->amount, 2) }}
            </p>
            <p class="text-white/55 text-xs mt-1">{{ $transaction->currency }} · {{ $transaction->occurred_at->translatedFormat('M j, Y · H:i') }}</p>
            @if ($transaction->location_name)
                <p class="text-white/40 text-xs mt-1 inline-flex items-center gap-1"><x-icon name="pin" :size="12" /> {{ $transaction->location_name }}</p>
            @endif
        </div>

        <div class="glass mt-3 p-4 space-y-3 text-sm rounded-2xl">
            <div class="flex justify-between"><span class="text-white/55">{{ __('app.wallet') }}</span><span class="font-semibold text-white">{{ $transaction->account->name }}</span></div>
            @if ($transaction->isTransfer() && $transaction->transferToAccount)
                <div class="flex justify-between"><span class="text-white/55">{{ __('app.tx_to_account') }}</span><span class="font-semibold text-white">{{ $transaction->transferToAccount->name }}</span></div>
            @endif
            @if ($transaction->category)
                <div class="flex justify-between"><span class="text-white/55">{{ __('app.category') }}</span><span class="font-semibold text-white">{{ $transaction->category->name() }}</span></div>
            @endif
        </div>

        @if ($transaction->attachment_path)
            <a href="{{ asset('storage/' . $transaction->attachment_path) }}" target="_blank" class="glass mt-3 block p-3 rounded-2xl">
                <p class="text-xs text-white/55 mb-2">{{ __('app.receipt') }}</p>
                <img src="{{ asset('storage/' . $transaction->attachment_path) }}" class="rounded-2xl w-full" alt="receipt">
            </a>
        @endif

        <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" onsubmit="return confirm('?')" class="mt-4">
            @csrf @method('DELETE')
            <button class="w-full py-3 rounded-2xl bg-rose-500/15 text-rose-300 font-semibold flex items-center justify-center gap-2 border border-rose-400/20">
                <x-icon name="trash" :size="18" /> {{ __('app.delete') }}
            </button>
        </form>
    </div>
</x-layouts.app>
