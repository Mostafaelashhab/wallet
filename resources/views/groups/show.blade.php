<x-layouts.app :title="$group->name">
    <x-header-bar :title="$group->name" :back="route('groups.index')">
        <a href="{{ route('groups.settle', $group) }}" class="chip text-stone-900 text-xs">{{ __('app.settle_up') }}</a>
    </x-header-bar>

    <div class="px-5">
        <div class="card flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl grid place-items-center text-white" style="background: {{ $group->color }}">
                <x-icon name="{{ $group->icon_name ?: 'group' }}" :size="26" />
            </div>
            <div class="flex-1">
                <p class="text-xs text-stone-500">{{ $group->members->count() }} {{ __('app.friends') }} · {{ $group->currency }}</p>
                <p class="text-2xl font-extrabold {{ $balance >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ ($balance >= 0 ? '+' : '') . number_format($balance, 2) }}
                </p>
                <p class="text-xs text-stone-500">رصيدك في المجموعة</p>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-2 mt-4 text-center">
            <a href="{{ route('expenses.create', ['group' => $group->id]) }}" class="card !p-3 tap-anim">
                <div class="w-10 h-10 mx-auto rounded-2xl grid place-items-center bg-orange-100 text-brand-600">
                    <x-icon name="plus" :size="20" />
                </div>
                <p class="text-[12px] mt-1.5 font-bold">{{ __('app.add_expense') }}</p>
            </a>
            <a href="{{ route('goals.create', $group) }}" class="card !p-3 tap-anim">
                <div class="w-10 h-10 mx-auto rounded-2xl grid place-items-center bg-emerald-100 text-emerald-600">
                    <x-icon name="target" :size="20" />
                </div>
                <p class="text-[12px] mt-1.5 font-bold">{{ __('app.goals') }}</p>
            </a>
            <a href="{{ route('recurring.create', $group) }}" class="card !p-3 tap-anim">
                <div class="w-10 h-10 mx-auto rounded-2xl grid place-items-center bg-sky-100 text-sky-600">
                    <x-icon name="arrow-swap" :size="20" />
                </div>
                <p class="text-[12px] mt-1.5 font-bold">{{ __('app.recurring') }}</p>
            </a>
        </div>

        @if ($group->goals->count())
            <h3 class="section-title mt-6 mb-2">{{ __('app.goals') }}</h3>
            <div class="space-y-3">
                @foreach ($group->goals as $goal)
                    <div class="card">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-2xl grid place-items-center text-white" style="background: {{ $goal->color }}">
                                <x-icon name="{{ $goal->icon_name ?: 'target' }}" :size="20" />
                            </div>
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
                    <div class="w-11 h-11 rounded-2xl grid place-items-center text-white shrink-0" style="background: {{ $exp->category->color ?? '#FF6B35' }}">
                        <x-icon name="{{ $exp->category->icon_name ?? 'other' }}" :size="20" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold truncate">{{ $exp->description }}</p>
                        <p class="text-xs text-stone-500">{{ $exp->payer->name }} · {{ $exp->occurred_at->translatedFormat('M j, Y') }}</p>
                    </div>
                    <div class="text-end">
                        <p class="font-bold">{{ number_format($exp->amount, 2) }}</p>
                        @php $share = $exp->shareFor(auth()->user()); $isPayer = $exp->payer_id === auth()->id(); @endphp
                        @if ($isPayer)
                            <span class="amount-pill owed text-xs">+{{ number_format($exp->amount - $share, 2) }}</span>
                        @elseif ($share > 0)
                            <span class="amount-pill owe text-xs">-{{ number_format($share, 2) }}</span>
                        @else
                            <span class="amount-pill muted text-xs">{{ __('app.not_involved') }}</span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="card text-center text-stone-500 py-6"><p>لسه مفيش مصاريف.</p></div>
            @endforelse
        </div>

        <h3 class="section-title mt-6 mb-2">الأعضاء</h3>
        <div class="card space-y-3">
            @foreach ($group->members as $m)
                <div class="flex items-center gap-3">
                    <x-avatar :user="$m" :size="36" />
                    <div class="flex-1">
                        <p class="font-semibold flex items-center gap-1">{{ $m->name }} @if ($m->id === $group->owner_id) <x-icon name="crown" :size="14" class="text-amber-500" /> @endif</p>
                        <p class="text-xs text-stone-500">{{ $m->email }}</p>
                    </div>
                    @if ($m->id !== $group->owner_id && $group->owner_id === auth()->id())
                        <form method="POST" action="{{ route('groups.members.remove', [$group, $m]) }}" onsubmit="return confirm('Remove?')">
                            @csrf @method('DELETE')
                            <button class="text-stone-400 text-xs"><x-icon name="x" :size="14" /></button>
                        </form>
                    @endif
                </div>
            @endforeach
            <form method="POST" action="{{ route('groups.members.add', $group) }}" class="flex items-center gap-2 mt-3">
                @csrf
                <input type="email" name="email" required placeholder="ضيف عضو بالإيميل" class="input !py-2 !px-3 text-sm">
                <button class="btn-ghost text-sm"><x-icon name="plus" :size="16" /></button>
            </form>
            @error('email') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
</x-layouts.app>
