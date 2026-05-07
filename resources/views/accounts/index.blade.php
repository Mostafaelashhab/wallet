<x-layouts.app title="المحافظ" hero="tall">
    <x-header-bar title="المحافظ" :back="route('dashboard')" />

    <div class="px-5 -mt-2">
        <div class="text-center text-white">
            <p class="text-xs opacity-80">صافي الرصيد</p>
            <p class="text-4xl font-extrabold mt-1">{{ number_format($netWorth, 2) }} <span class="text-base text-white/70">EGP</span></p>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-3">
            @foreach ($accounts as $a)
                <a href="{{ route('accounts.show', $a) }}" class="rounded-3xl p-4 text-white shadow-lg block tap-anim" style="background: linear-gradient(135deg, {{ $a->color }} 0%, {{ $a->color }}cc 100%);">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-white/25 grid place-items-center">
                            <x-icon name="{{ $a->type === 'cash' ? 'cash' : ($a->type === 'bank' ? 'bank' : ($a->type === 'card' ? 'card' : ($a->type === 'savings' ? 'piggy' : 'mobile'))) }}" :size="24" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm opacity-85 truncate">{{ $a->institution ?: ucfirst($a->type) }}</p>
                            <p class="font-bold truncate">{{ $a->name }}</p>
                        </div>
                        <div class="text-end">
                            <p class="text-2xl font-extrabold">{{ number_format($a->current_balance, 0) }}</p>
                            <p class="text-[11px] opacity-80">{{ $a->currency }}</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <a href="{{ route('accounts.create') }}" class="btn-primary mt-6 flex items-center justify-center gap-2">
            <x-icon name="plus" :size="18" /> محفظة جديدة
        </a>
    </div>
</x-layouts.app>
