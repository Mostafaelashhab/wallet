<x-layouts.app :title="__('app.activity')">
    <x-header-bar :title="__('app.activity')" :back="route('dashboard')" />

    <div class="px-5">
        <div class="card space-y-4">
            @forelse ($items as $i)
                @php
                    $meta = $i->meta ?? [];
                    [$icon, $msg] = match ($i->action) {
                        'expense.created' => ['💸', $i->user->name . ' أضاف "' . ($meta['description'] ?? '') . '" بـ ' . number_format($meta['amount'] ?? 0, 2) . ' ' . ($meta['currency'] ?? '')],
                        'expense.deleted' => ['🗑️', $i->user->name . ' حذف مصروف'],
                        'payment.made'    => ['✅', $i->user->name . ' دفع ' . number_format($meta['amount'] ?? 0, 2) . ' ' . ($meta['currency'] ?? '') . ' (' . ($meta['method'] ?? '') . ')'],
                        'group.created'   => ['🎉', $i->user->name . ' أنشأ مجموعة "' . ($meta['name'] ?? '') . '"'],
                        'group.member.added' => ['➕', $i->user->name . ' ضيف ' . ($meta['name'] ?? 'عضو') . ' للمجموعة'],
                        'group.member.removed' => ['➖', $i->user->name . ' أزال عضو'],
                        default => ['🔔', $i->action],
                    };
                @endphp
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-stone-100 grid place-items-center text-xl">{{ $icon }}</div>
                    <div class="flex-1">
                        <p class="text-sm">{{ $msg }}</p>
                        <p class="text-xs text-stone-500 mt-1">
                            {{ $i->created_at->diffForHumans() }}
                            @if ($i->group) · في {{ $i->group->name }} @endif
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-stone-500 text-center text-sm">مفيش نشاط لسه.</p>
            @endforelse
        </div>
    </div>
</x-layouts.app>
