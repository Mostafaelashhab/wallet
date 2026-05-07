<x-layouts.app :title="$user->name" hero="short">
    <x-header-bar :title="$user->name" :back="route('friends.index')" />

    <div class="px-5">
        <div class="card flex items-center gap-4">
            <x-avatar :user="$user" :size="56" />
            <div class="flex-1">
                <p class="font-semibold">{{ $user->name }}</p>
                @php $bal = (float) $balance; @endphp
                <p class="text-xl font-bold {{ $bal >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                    @if (abs($bal) < 0.01) {{ __('app.settled_up') }}
                    @elseif ($bal > 0) {{ $user->name }} {{ __('app.owes_you') }} {{ number_format($bal, 2) }}
                    @else {{ __('app.you_owe_them') }} {{ number_format(abs($bal), 2) }}
                    @endif
                </p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2 mt-4">
            <a href="{{ route('expenses.create') }}?with={{ $user->id }}" class="btn-primary !py-3 text-sm text-center">+ {{ __('app.add_expense') }}</a>

            @if ($bal != 0)
                <button type="button" onclick="document.getElementById('settle-modal').showModal()" class="btn-ghost text-center">{{ __('app.settle_up') }}</button>
            @endif
        </div>

        <h3 class="section-title mt-6 mb-2">السجل</h3>
        <div class="space-y-3">
            @forelse ($expenses as $exp)
                <a href="{{ route('expenses.show', $exp) }}" class="card flex items-center gap-3 tap-anim">
                    <div class="w-11 h-11 rounded-2xl grid place-items-center text-white shrink-0" style="background: {{ $exp->category->color ?? '#FF6B35' }}">
                        <x-icon name="{{ $exp->category->icon_name ?? 'other' }}" :size="20" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold truncate">{{ $exp->description }}</p>
                        <p class="text-xs text-stone-500">{{ $exp->payer->name }} · {{ $exp->occurred_at->translatedFormat('M j') }}</p>
                    </div>
                    <p class="font-bold">{{ number_format($exp->amount, 2) }}</p>
                </a>
            @empty
                <div class="card text-center text-stone-500"><p>مفيش مصاريف مشتركة.</p></div>
            @endforelse
        </div>
    </div>

    <dialog id="settle-modal" class="rounded-3xl p-0 w-[92%] max-w-sm">
        <form method="POST" action="{{ route('payments.store') }}" class="p-5 space-y-3">
            @csrf
            <h3 class="font-bold text-lg">سدد لـ {{ $user->name }}</h3>
            <input type="hidden" name="payee_id" value="{{ $user->id }}">
            <input type="hidden" name="paid_at" value="{{ now()->toIso8601String() }}">
            <input type="hidden" name="currency" value="EGP">
            <div>
                <label class="text-sm font-semibold">المبلغ</label>
                <input type="number" name="amount" min="0.01" step="0.01" required class="input mt-1" value="{{ number_format(abs($balance), 2, '.', '') }}">
            </div>
            <div>
                <label class="text-sm font-semibold">طريقة الدفع</label>
                <select name="method" class="input mt-1">
                    <option value="instapay">InstaPay</option>
                    <option value="vcash">Vodafone Cash</option>
                    <option value="cash">كاش</option>
                    <option value="bank">حوالة بنكية</option>
                    <option value="other">غير ذلك</option>
                </select>
            </div>
            @if ($user->instapay_handle || $user->vodafone_cash)
                <div class="bg-stone-50 rounded-2xl p-3 text-sm">
                    <p class="font-semibold mb-1">QR للدفع المباشر</p>
                    @php
                        $payload = $user->instapay_handle ? 'https://instapay.app/pay?to=' . urlencode($user->instapay_handle) . '&amount=' . abs($balance)
                                  : 'tel:' . $user->vodafone_cash;
                        $qr = 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' . urlencode($payload);
                    @endphp
                    <img src="{{ $qr }}" alt="QR" class="mx-auto rounded-xl" loading="lazy">
                    <p class="text-center text-xs text-stone-500 mt-1 break-all">{{ $payload }}</p>
                </div>
            @endif
            <div>
                <label class="text-sm font-semibold">رقم مرجع (اختياري)</label>
                <input type="text" name="reference" class="input mt-1">
            </div>
            <button class="btn-primary">{{ __('app.save') }}</button>
            <button type="button" onclick="document.getElementById('settle-modal').close()" class="btn-ghost block w-full text-center">{{ __('app.cancel') }}</button>
        </form>
    </dialog>
</x-layouts.app>
