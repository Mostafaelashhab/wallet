<x-layouts.app :title="$group->name">
    <x-header-bar :title="$group->name" :back="route('groups.index')">
        <a href="{{ route('groups.settle', $group) }}" class="chip text-stone-900">{{ __('app.settle_up') }}</a>
    </x-header-bar>

    <div class="px-5">
        <div class="card flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl grid place-items-center text-3xl" style="background: {{ $group->color }}22">{{ $group->icon }}</div>
            <div class="flex-1">
                <p class="text-xs text-stone-500">{{ $group->members->count() }} {{ __('app.friends') }} · {{ $group->currency }}</p>
                <p class="text-2xl font-bold {{ $balance >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ ($balance >= 0 ? '+' : '') . number_format($balance, 2) }}
                </p>
                <p class="text-xs text-stone-500">رصيدك في المجموعة</p>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-2 mt-4 text-center">
            <a href="{{ route('expenses.create', ['group' => $group->id]) }}" class="card p-3 tap-anim">
                <div class="text-2xl">➕</div><p class="text-xs mt-1 font-semibold">{{ __('app.add_expense') }}</p>
            </a>
            <a href="{{ route('goals.create', $group) }}" class="card p-3 tap-anim">
                <div class="text-2xl">🎯</div><p class="text-xs mt-1 font-semibold">{{ __('app.goals') }}</p>
            </a>
            <a href="{{ route('recurring.create', $group) }}" class="card p-3 tap-anim">
                <div class="text-2xl">🔁</div><p class="text-xs mt-1 font-semibold">{{ __('app.recurring') }}</p>
            </a>
        </div>

        @if ($group->goals->count())
            <h3 class="section-title mt-6 mb-2">{{ __('app.goals') }}</h3>
            <div class="space-y-3">
                @foreach ($group->goals as $goal)
                    <div class="card">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-2xl grid place-items-center text-2xl" style="background: {{ $goal->color }}22">{{ $goal->icon }}</div>
                            <div class="flex-1">
                                <p class="font-semibold">{{ $goal->name }}</p>
                                <p class="text-xs text-stone-500">{{ number_format($goal->progress(), 0) }} / {{ number_format($goal->target_amount, 0) }} {{ $goal->currency }}</p>
                            </div>
                            <form method="POST" action="{{ route('goals.contribute', $goal) }}" class="flex items-center gap-2">
                                @csrf
                                <input type="number" step="0.01" name="amount" placeholder="ساهم" class="input !py-2 !px-3 w-24 text-sm">
                                <button class="btn-ghost text-sm">+</button>
                            </form>
                        </div>
                        <div class="mt-3 h-2 bg-stone-100 rounded-full overflow-hidden">
                            <div style="width:{{ $goal->percent() }}%; background: {{ $goal->color }}" class="h-full"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <h3 class="section-title mt-6 mb-2">المصاريف</h3>
        <div class="space-y-3">
            @forelse ($group->expenses as $exp)
                <a href="{{ route('expenses.show', $exp) }}" class="card flex items-center gap-3 tap-anim">
                    <div class="w-11 h-11 rounded-2xl grid place-items-center text-2xl" style="background: {{ ($exp->category->color ?? '#FF6B35') }}22">
                        {{ $exp->category->icon ?? '💸' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold truncate">{{ $exp->description }}</p>
                        <p class="text-xs text-stone-500">{{ $exp->payer->name }} · {{ $exp->occurred_at->translatedFormat('M j, Y') }}</p>
                    </div>
                    <div class="text-end">
                        <p class="font-bold">{{ number_format($exp->amount, 2) }}</p>
                        @php $share = $exp->shareFor(auth()->user()); $isPayer = $exp->payer_id === auth()->id(); @endphp
                        @if ($isPayer)
                            <span class="amount-pill owed text-xs">{{ __('app.you_are_owed') }} {{ number_format($exp->amount - $share, 2) }}</span>
                        @elseif ($share > 0)
                            <span class="amount-pill owe text-xs">{{ __('app.you_owe') }} {{ number_format($share, 2) }}</span>
                        @else
                            <span class="amount-pill muted text-xs">{{ __('app.not_involved') }}</span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="card text-center text-stone-500">
                    <p>لسه مفيش مصاريف. ابدأ بإضافة واحد.</p>
                </div>
            @endforelse
        </div>

        <h3 class="section-title mt-6 mb-2">الأعضاء</h3>
        <div class="card space-y-3">
            @foreach ($group->members as $m)
                <div class="flex items-center gap-3">
                    <x-avatar :user="$m" :size="36" />
                    <div class="flex-1">
                        <p class="font-semibold">{{ $m->name }} {{ $m->id === $group->owner_id ? ' · ⭐' : '' }}</p>
                        <p class="text-xs text-stone-500">{{ $m->email }}</p>
                    </div>
                    @if ($m->id !== $group->owner_id && $group->owner_id === auth()->id())
                        <form method="POST" action="{{ route('groups.members.remove', [$group, $m]) }}" onsubmit="return confirm('Remove?')">
                            @csrf @method('DELETE')
                            <button class="text-stone-400 text-xs">إزالة</button>
                        </form>
                    @endif
                </div>
            @endforeach
            <form method="POST" action="{{ route('groups.members.add', $group) }}" class="flex items-center gap-2 mt-3">
                @csrf
                <input type="email" name="email" required placeholder="ضيف عضو بالإيميل" class="input !py-2 !px-3 text-sm">
                <button class="btn-ghost text-sm">{{ __('app.add') }}</button>
            </form>
            @error('email') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
</x-layouts.app>
