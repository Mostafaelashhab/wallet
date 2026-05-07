<x-layouts.app :title="__('app.app_name')" hero="tall">
    <div class="safe-top px-5 pt-3 text-white">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <x-avatar :user="auth()->user()" :size="40" />
                <div>
                    <p class="text-white/70 text-xs">أهلاً</p>
                    <p class="font-bold text-base leading-tight">{{ auth()->user()->name }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('notifications') }}" class="w-10 h-10 rounded-full bg-white/15 grid place-items-center backdrop-blur">
                    <x-icon name="bell" :size="18" />
                </a>
            </div>
        </div>

        {{-- Net worth hero --}}
        <div class="mt-6">
            <p class="text-white/75 text-xs">صافي الثروة</p>
            <p class="text-4xl font-extrabold tracking-tight mt-1 leading-none">
                {{ number_format($netWorth, 2) }}
                <span class="text-base font-semibold text-white/70">EGP</span>
            </p>
            <div class="flex items-center gap-3 mt-3 text-xs">
                <span class="inline-flex items-center gap-1.5 bg-emerald-500/25 backdrop-blur rounded-full px-3 py-1.5">
                    <x-icon name="arrow-down" :size="14" />
                    دخل الشهر {{ number_format($monthSummary['income'], 0) }}
                </span>
                <span class="inline-flex items-center gap-1.5 bg-rose-500/25 backdrop-blur rounded-full px-3 py-1.5">
                    <x-icon name="arrow-up" :size="14" />
                    صرفت {{ number_format($monthSummary['expense'], 0) }}
                </span>
            </div>
        </div>
    </div>

    @if (session('flash'))
        <div class="mx-5 mt-3 bg-emerald-500/90 text-white text-sm rounded-2xl px-4 py-2.5 shadow-lg flex items-center gap-2">
            <x-icon name="check" :size="18" />
            {{ session('flash') }}
        </div>
    @endif

    {{-- Quick action tiles --}}
    <div class="px-5 mt-6 grid grid-cols-3 gap-2">
        <a href="{{ route('transactions.create') }}?type=expense" class="card !p-3 text-center tap-anim">
            <div class="w-10 h-10 mx-auto rounded-2xl grid place-items-center bg-rose-100 text-rose-600">
                <x-icon name="arrow-up" :size="20" />
            </div>
            <p class="text-[12px] mt-2 font-bold">مصروف</p>
        </a>
        <a href="{{ route('transactions.create') }}?type=income" class="card !p-3 text-center tap-anim">
            <div class="w-10 h-10 mx-auto rounded-2xl grid place-items-center bg-emerald-100 text-emerald-600">
                <x-icon name="arrow-down" :size="20" />
            </div>
            <p class="text-[12px] mt-2 font-bold">دخل</p>
        </a>
        <a href="{{ route('transactions.create') }}?type=transfer" class="card !p-3 text-center tap-anim">
            <div class="w-10 h-10 mx-auto rounded-2xl grid place-items-center bg-sky-100 text-sky-600">
                <x-icon name="transfer" :size="20" />
            </div>
            <p class="text-[12px] mt-2 font-bold">تحويل</p>
        </a>
    </div>

    {{-- Wallets row (horizontal scroll) --}}
    <div class="mt-6 px-5 flex items-end justify-between">
        <h3 class="section-title">المحافظ</h3>
        <a href="{{ route('accounts.index') }}" class="text-stone-500 text-sm">عرض الكل</a>
    </div>
    <div class="mt-3 px-5 flex gap-3 overflow-x-auto pb-2 -mx-5 px-5 snap-x snap-mandatory" style="scrollbar-width: none;">
        @foreach ($accounts as $a)
            <a href="{{ route('accounts.show', $a) }}" class="snap-start shrink-0 w-44 rounded-3xl p-4 text-white shadow-lg" style="background: linear-gradient(135deg, {{ $a->color }} 0%, {{ $a->color }}cc 100%);">
                <div class="flex items-start justify-between">
                    <div class="w-10 h-10 rounded-2xl bg-white/25 grid place-items-center">
                        <x-icon name="{{ $a->type === 'cash' ? 'cash' : ($a->type === 'bank' ? 'bank' : ($a->type === 'card' ? 'card' : ($a->type === 'savings' ? 'piggy' : 'mobile'))) }}" :size="20" />
                    </div>
                    <span class="text-[10px] uppercase tracking-wider font-bold opacity-80">{{ $a->currency }}</span>
                </div>
                <p class="mt-3 text-xs opacity-85 truncate">{{ $a->name }}</p>
                <p class="text-xl font-extrabold leading-tight tracking-tight">{{ number_format($a->current_balance, 0) }}</p>
            </a>
        @endforeach
        <a href="{{ route('accounts.create') }}" class="snap-start shrink-0 w-44 rounded-3xl p-4 grid place-items-center bg-white/40 border-2 border-dashed border-stone-300">
            <div class="text-center">
                <div class="w-10 h-10 mx-auto rounded-2xl bg-white grid place-items-center text-stone-500">
                    <x-icon name="plus" :size="20" />
                </div>
                <p class="text-xs font-semibold mt-2 text-stone-600">محفظة جديدة</p>
            </div>
        </a>
    </div>

    {{-- Recent transactions --}}
    <div class="mt-6 px-5 flex items-end justify-between">
        <h3 class="section-title">آخر الحركات</h3>
        <a href="{{ route('transactions.index') }}" class="text-stone-500 text-sm">عرض الكل</a>
    </div>
    <div class="px-5 mt-3 space-y-2.5">
        @forelse ($recentTx as $tx)
            <a href="{{ route('transactions.show', $tx) }}" class="card !p-3 flex items-center gap-3 tap-anim">
                @php
                    [$bg, $fg, $icon] = match ($tx->type) {
                        'income' => ['bg-emerald-100', 'text-emerald-600', 'arrow-down'],
                        'transfer' => ['bg-sky-100', 'text-sky-600', 'transfer'],
                        default => ['bg-rose-100', 'text-rose-600', 'arrow-up'],
                    };
                    $catIcon = $tx->category?->icon_name ?? $icon;
                @endphp
                <div class="w-11 h-11 rounded-2xl grid place-items-center {{ $bg }} {{ $fg }}">
                    <x-icon name="{{ $catIcon }}" :size="22" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold truncate">{{ $tx->description }}</p>
                    <p class="text-xs text-stone-500 truncate">
                        {{ $tx->account->name }}
                        @if ($tx->isTransfer() && $tx->transferToAccount) → {{ $tx->transferToAccount->name }} @endif
                        · {{ $tx->occurred_at->translatedFormat('M j') }}
                    </p>
                </div>
                <p class="font-extrabold whitespace-nowrap {{ $tx->isIncome() ? 'text-emerald-600' : ($tx->isExpense() ? 'text-rose-600' : 'text-sky-600') }}">
                    {{ $tx->isIncome() ? '+' : ($tx->isExpense() ? '-' : '') }}{{ number_format($tx->amount, 0) }}
                </p>
            </a>
        @empty
            <div class="card text-center text-stone-500 py-6">
                <p>لسه مفيش حركات. اضغط على ➕ لتسجيل أول مصروف أو دخل.</p>
            </div>
        @endforelse
    </div>

    {{-- Groups (secondary) --}}
    @if ($groups->count())
        <div class="mt-6 px-5 flex items-end justify-between">
            <h3 class="section-title">شلتك</h3>
            <a href="{{ route('groups.index') }}" class="text-stone-500 text-sm">عرض الكل</a>
        </div>
        <div class="mt-3 px-5 grid grid-cols-2 gap-2.5">
            @foreach ($groups as $g)
                <a href="{{ route('groups.show', $g) }}" class="card !p-3 tap-anim">
                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 rounded-xl grid place-items-center text-white" style="background: {{ $g->color }}">
                            <x-icon name="{{ $g->icon_name ?: 'group' }}" :size="18" />
                        </div>
                        <p class="font-semibold text-sm truncate flex-1">{{ $g->name }}</p>
                    </div>
                    @php $bal = (float) $g->my_balance; @endphp
                    <p class="text-xs mt-2 {{ abs($bal) < 0.01 ? 'text-stone-500' : ($bal > 0 ? 'text-emerald-600' : 'text-rose-600') }} font-semibold">
                        @if (abs($bal) < 0.01) مفيش حسابات
                        @elseif ($bal > 0) ليك {{ number_format($bal, 0) }}
                        @else عليك {{ number_format(abs($bal), 0) }}
                        @endif
                    </p>
                </a>
            @endforeach
        </div>
    @endif

    {{-- PWA install hint --}}
    <div id="install-hint" class="mx-5 card mt-6 hidden">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-brand-100 grid place-items-center text-brand-600">
                <x-icon name="sparkles" :size="20" />
            </div>
            <div class="flex-1">
                <p class="font-semibold text-sm">{{ __('app.install_pwa') }}</p>
                <p class="text-xs text-stone-500">خلي Splitty على الـ home screen.</p>
            </div>
            <button onclick="installPwa().then(() => document.getElementById('install-hint').classList.add('hidden'))" class="btn-ghost text-sm">ثبّت</button>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('pwa:installable', () => {
            document.getElementById('install-hint')?.classList.remove('hidden');
        });
    </script>
    @endpush
</x-layouts.app>
