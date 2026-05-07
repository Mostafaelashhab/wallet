<x-layouts.app :title="__('app.app_name')">
    <x-header-bar />

    <div class="px-5 -mt-1">
        @if (session('flash'))
            <div class="bg-white/15 text-white text-sm rounded-2xl px-4 py-2 mb-3">{{ session('flash') }}</div>
        @endif

        <div class="grid grid-cols-2 gap-3">
            <div class="bg-stone-900 text-white rounded-3xl p-5">
                <p class="text-3xl font-bold">{{ number_format($iOwe, 2) }}</p>
                <p class="text-sm text-white/70 mt-1">{{ __('app.you_owe') }}</p>
            </div>
            <div class="bg-stone-900 text-white rounded-3xl p-5 relative overflow-hidden">
                <p class="text-3xl font-bold text-emerald-300">{{ number_format($owedToMe, 2) }}</p>
                <p class="text-sm text-white/70 mt-1">{{ __('app.you_are_owed') }}</p>
            </div>
        </div>

        {{-- Pending bills (groups) --}}
        <div class="mt-6 flex items-center justify-between">
            <h3 class="section-title">{{ __('app.pending_bills') }}</h3>
            <a href="{{ route('groups.index') }}" class="text-stone-500 text-sm">{{ __('app.view_all') }}</a>
        </div>

        <div class="mt-3 space-y-3">
            @forelse ($groups as $g)
                <a href="{{ route('groups.show', $g) }}" class="card flex items-center gap-3 tap-anim">
                    <div class="w-12 h-12 rounded-2xl grid place-items-center text-2xl" style="background: {{ $g->color }}22">
                        <span>{{ $g->icon }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold truncate">{{ $g->name }}</p>
                        <p class="text-xs text-stone-500">{{ $g->updated_at->translatedFormat('M j, Y') }}</p>
                    </div>
                    <div class="text-end">
                        <p class="font-bold">{{ number_format($g->totalSpent(), 2) }}</p>
                        @php $bal = (float) $g->my_balance; @endphp
                        @if (abs($bal) < 0.01)
                            <span class="amount-pill muted text-xs mt-1 inline-block">{{ __('app.settled_up') }}</span>
                        @elseif ($bal > 0)
                            <span class="amount-pill owed text-xs mt-1 inline-block">+{{ number_format($bal, 2) }}</span>
                        @else
                            <span class="amount-pill owe text-xs mt-1 inline-block">{{ number_format($bal, 2) }}</span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="card text-center text-stone-500">
                    <p>لسه مفيش مجموعات.</p>
                    <a href="{{ route('groups.create') }}" class="btn-primary mt-3 inline-block">إضافة أول مجموعة</a>
                </div>
            @endforelse
        </div>

        {{-- Friends --}}
        <div class="mt-7 flex items-center justify-between">
            <h3 class="section-title">{{ __('app.friends') }}</h3>
            <a href="{{ route('friends.index') }}" class="text-stone-500 text-sm">{{ __('app.view_all') }}</a>
        </div>
        <div class="mt-3 space-y-3">
            @forelse ($friends as $f)
                <a href="{{ route('friends.show', $f) }}" class="card flex items-center gap-3 tap-anim">
                    <x-avatar :user="$f" :size="44" />
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold truncate">{{ $f->name }}</p>
                        @php $bal = (float) $f->balance_with_me; @endphp
                        <p class="text-xs text-stone-500">
                            @if (abs($bal) < 0.01) {{ __('app.settled_up') }}
                            @elseif ($bal > 0) {{ $f->name }} {{ __('app.owes_you') }} <strong class="text-emerald-600">{{ number_format($bal, 2) }}</strong>
                            @else {{ __('app.you_owe_them') }} <strong class="text-rose-600">{{ number_format(abs($bal), 2) }}</strong>
                            @endif
                        </p>
                    </div>
                    <svg width="20" height="20" class="text-stone-400 flip-rtl" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 6l6 6-6 6"/></svg>
                </a>
            @empty
                <div class="card text-center text-stone-500">
                    <p>اضف أصحابك عشان تقسم معاهم.</p>
                    <a href="{{ route('friends.index') }}" class="btn-primary mt-3 inline-block">إضافة صديق</a>
                </div>
            @endforelse
        </div>

        {{-- PWA install hint --}}
        <div id="install-hint" class="card mt-7 hidden">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-brand-100 grid place-items-center">📲</div>
                <div class="flex-1">
                    <p class="font-semibold">{{ __('app.install_pwa') }}</p>
                    <p class="text-xs text-stone-500">خلي Splitty على الـ home screen.</p>
                </div>
                <button onclick="installPwa().then(() => document.getElementById('install-hint').classList.add('hidden'))" class="btn-ghost text-sm">{{ __('app.install_pwa') }}</button>
            </div>
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
