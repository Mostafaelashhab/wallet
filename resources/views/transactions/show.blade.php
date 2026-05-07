<x-layouts.app :title="$transaction->description" hero="short">
    <x-header-bar :title="$transaction->description" :back="route('dashboard')" />

    <div class="px-5">
        @php
            [$bg, $fg, $icon] = match ($transaction->type) {
                'income' => ['bg-emerald-100', 'text-emerald-600', 'arrow-down'],
                'transfer' => ['bg-sky-100', 'text-sky-600', 'transfer'],
                default => ['bg-rose-100', 'text-rose-600', 'arrow-up'],
            };
        @endphp
        <div class="card text-center">
            <div class="w-16 h-16 mx-auto rounded-3xl grid place-items-center {{ $bg }} {{ $fg }}">
                <x-icon name="{{ $transaction->category?->icon_name ?? $icon }}" :size="32" />
            </div>
            <p class="text-3xl font-extrabold mt-3 {{ $transaction->isIncome() ? 'text-emerald-600' : ($transaction->isExpense() ? 'text-rose-600' : 'text-sky-600') }}">
                {{ $transaction->isIncome() ? '+' : ($transaction->isExpense() ? '-' : '') }}{{ number_format($transaction->amount, 2) }}
                <span class="text-sm font-medium text-stone-500">{{ $transaction->currency }}</span>
            </p>
            <p class="text-stone-500 text-sm mt-1">{{ $transaction->occurred_at->translatedFormat('M j, Y · H:i') }}</p>
            @if ($transaction->location_name)
                <p class="text-stone-400 text-xs mt-1 inline-flex items-center gap-1"><x-icon name="pin" :size="12" /> {{ $transaction->location_name }}</p>
            @endif
        </div>

        <div class="card mt-3 space-y-3 text-sm">
            <div class="flex justify-between"><span class="text-stone-500">المحفظة</span><span class="font-semibold">{{ $transaction->account->name }}</span></div>
            @if ($transaction->isTransfer() && $transaction->transferToAccount)
                <div class="flex justify-between"><span class="text-stone-500">إلى</span><span class="font-semibold">{{ $transaction->transferToAccount->name }}</span></div>
            @endif
            @if ($transaction->category)
                <div class="flex justify-between"><span class="text-stone-500">الفئة</span><span class="font-semibold">{{ $transaction->category->name() }}</span></div>
            @endif
        </div>

        @if ($transaction->attachment_path)
            <a href="{{ asset('storage/' . $transaction->attachment_path) }}" target="_blank" class="card mt-3 block">
                <p class="text-xs text-stone-500 mb-2">الإيصال</p>
                <img src="{{ asset('storage/' . $transaction->attachment_path) }}" class="rounded-2xl w-full" alt="receipt">
            </a>
        @endif

        <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" onsubmit="return confirm('حذف الحركة؟')" class="mt-4">
            @csrf @method('DELETE')
            <button class="w-full py-3 rounded-2xl bg-rose-50 text-rose-600 font-semibold flex items-center justify-center gap-2">
                <x-icon name="trash" :size="18" /> {{ __('app.delete') }}
            </button>
        </form>
    </div>
</x-layouts.app>
