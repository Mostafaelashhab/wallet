<x-layouts.app :title="__('app.settle_up')">
    <x-header-bar :title="__('app.settle_up')" :back="route('groups.show', $group)" />

    <div class="px-5">
        <div class="card mb-3">
            <p class="text-xs text-stone-500">المجموعة</p>
            <p class="font-semibold">{{ $group->icon }} {{ $group->name }}</p>
            <p class="text-sm text-stone-500 mt-1">رصيد كل عضو</p>
            <div class="space-y-2 mt-2">
                @foreach ($balances as $uid => $bal)
                    @php $u = $usersById[$uid] ?? null; @endphp
                    @if (!$u) @continue @endif
                    <div class="flex items-center gap-2">
                        <x-avatar :user="$u" :size="28" />
                        <span class="flex-1 text-sm font-medium">{{ $u->name }}</span>
                        @if (abs($bal) < 0.01)
                            <span class="amount-pill muted text-xs">0.00</span>
                        @elseif ($bal > 0)
                            <span class="amount-pill owed text-xs">+{{ number_format($bal, 2) }}</span>
                        @else
                            <span class="amount-pill owe text-xs">{{ number_format($bal, 2) }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card">
            <p class="text-sm text-stone-500">التحويلات المقترحة (بأقل عدد ممكن)</p>
            @if ($transfers->isEmpty())
                <div class="text-center py-6">
                    <div class="text-4xl">✅</div>
                    <p class="font-bold mt-2">{{ __('app.settled_up') }}</p>
                </div>
            @else
                <div class="space-y-3 mt-3">
                    @foreach ($transfers as $t)
                        @php $from = $usersById[$t['from']] ?? null; $to = $usersById[$t['to']] ?? null; @endphp
                        <div class="flex items-center gap-3 bg-stone-50 rounded-2xl p-3">
                            <x-avatar :user="$from" :size="36" />
                            <div class="flex-1">
                                <p class="text-sm">
                                    <strong>{{ $from->name }}</strong> →
                                    <strong>{{ $to->name }}</strong>
                                </p>
                                <p class="text-xs text-stone-500">{{ $group->currency }}</p>
                            </div>
                            <p class="font-bold text-brand-600">{{ number_format($t['amount'], 2) }}</p>
                            @if ($from->id === auth()->id())
                                <form method="POST" action="{{ route('payments.store') }}" onsubmit="return confirm('سددت {{ number_format($t['amount'], 2) }} لـ {{ $to->name }}؟')">
                                    @csrf
                                    <input type="hidden" name="payee_id" value="{{ $to->id }}">
                                    <input type="hidden" name="group_id" value="{{ $group->id }}">
                                    <input type="hidden" name="amount" value="{{ $t['amount'] }}">
                                    <input type="hidden" name="currency" value="{{ $group->currency }}">
                                    <input type="hidden" name="method" value="cash">
                                    <input type="hidden" name="paid_at" value="{{ now()->toIso8601String() }}">
                                    <button class="text-xs bg-emerald-500 text-white px-3 py-2 rounded-xl">سددت</button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
