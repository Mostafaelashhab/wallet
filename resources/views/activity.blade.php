<x-layouts.app :title="__('app.activity')" hero="short">
    <x-header-bar :title="__('app.activity')" :back="route('dashboard')" />

    <div class="px-5">
        <div class="card space-y-4">
            @forelse ($items as $i)
                @php
                    $meta = $i->meta ?? [];
                    [$icon, $bg, $fg, $msg] = match ($i->action) {
                        'expense.created' => ['arrow-up', 'bg-rose-100', 'text-rose-600', $i->user->name . ' أضاف "' . ($meta['description'] ?? '') . '" بـ ' . number_format($meta['amount'] ?? 0, 2) . ' ' . ($meta['currency'] ?? '')],
                        'expense.deleted' => ['trash', 'bg-stone-100', 'text-stone-500', $i->user->name . ' حذف مصروف'],
                        'payment.made'    => ['check', 'bg-emerald-100', 'text-emerald-600', $i->user->name . ' دفع ' . number_format($meta['amount'] ?? 0, 2) . ' ' . ($meta['currency'] ?? '') . ' (' . ($meta['method'] ?? '') . ')'],
                        'tx.income'       => ['arrow-down', 'bg-emerald-100', 'text-emerald-600', $i->user->name . ' سجل دخل ' . number_format($meta['amount'] ?? 0, 2) . ' (' . ($meta['account'] ?? '') . ')'],
                        'tx.expense'      => ['arrow-up', 'bg-rose-100', 'text-rose-600', $i->user->name . ' صرف ' . number_format($meta['amount'] ?? 0, 2) . ' من ' . ($meta['account'] ?? '')],
                        'tx.transfer'     => ['transfer', 'bg-sky-100', 'text-sky-600', $i->user->name . ' حوّل ' . number_format($meta['amount'] ?? 0, 2)],
                        'group.created'   => ['party', 'bg-orange-100', 'text-brand-600', $i->user->name . ' أنشأ مجموعة "' . ($meta['name'] ?? '') . '"'],
                        'group.member.added' => ['plus', 'bg-stone-100', 'text-stone-600', $i->user->name . ' ضيف ' . ($meta['name'] ?? 'عضو') . ' للمجموعة'],
                        'group.member.removed' => ['x', 'bg-stone-100', 'text-stone-500', $i->user->name . ' أزال عضو'],
                        default => ['bell', 'bg-stone-100', 'text-stone-500', $i->action],
                    };
                @endphp
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-2xl grid place-items-center {{ $bg }} {{ $fg }}">
                        <x-icon name="{{ $icon }}" :size="18" />
                    </div>
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
