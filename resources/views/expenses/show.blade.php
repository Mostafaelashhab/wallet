<x-layouts.app :title="$expense->description">
    <x-header-bar :title="$expense->description" :back="route('dashboard')" />

    <div class="px-5">
        <div class="card text-center">
            <div class="w-16 h-16 mx-auto rounded-3xl grid place-items-center text-3xl" style="background: {{ ($expense->category->color ?? '#FF6B35') }}22">
                {{ $expense->category->icon ?? '💸' }}
            </div>
            <p class="text-3xl font-bold mt-3">{{ number_format($expense->amount, 2) }} <span class="text-sm font-medium text-stone-500">{{ $expense->currency }}</span></p>
            <p class="text-stone-500 text-sm mt-1">{{ __('app.paid_by') }} <strong>{{ $expense->payer->name }}</strong> · {{ $expense->occurred_at->translatedFormat('M j, Y · H:i') }}</p>
            @if ($expense->location_name)
                <p class="text-stone-400 text-xs mt-1">📍 {{ $expense->location_name }}</p>
            @endif
        </div>

        @if ($expense->receipt_path)
            <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank" class="card mt-3 block">
                <p class="text-xs text-stone-500 mb-2">{{ __('app.receipt') }}</p>
                <img src="{{ asset('storage/' . $expense->receipt_path) }}" class="rounded-2xl w-full" alt="receipt">
            </a>
        @endif

        <h3 class="section-title mt-5 mb-2">التقسيم</h3>
        <div class="card space-y-3">
            @foreach ($expense->splits as $s)
                <div class="flex items-center gap-3">
                    <x-avatar :user="$s->user" :size="36" />
                    <div class="flex-1">
                        <p class="font-semibold">{{ $s->user->name }}</p>
                        @if ($s->settled_at)
                            <p class="text-xs text-emerald-600">سدد ✓</p>
                        @endif
                    </div>
                    <p class="font-bold">{{ number_format($s->amount, 2) }}</p>
                </div>
            @endforeach
        </div>

        <h3 class="section-title mt-5 mb-2">التعليقات</h3>
        <div class="card space-y-3">
            @foreach ($expense->comments as $c)
                <div class="flex items-start gap-3">
                    <x-avatar :user="$c->user" :size="32" />
                    <div class="bg-stone-50 rounded-2xl px-3 py-2 flex-1">
                        <p class="text-xs text-stone-500"><strong>{{ $c->user->name }}</strong> · {{ $c->created_at->diffForHumans() }}</p>
                        <p class="text-sm">{{ $c->body }}</p>
                    </div>
                </div>
            @endforeach
            <form method="POST" action="{{ route('expenses.comments.store', $expense) }}" class="flex items-center gap-2">
                @csrf
                <input name="body" required maxlength="500" placeholder="اكتب تعليق…" class="input !py-2 !px-3 text-sm">
                <button class="btn-ghost text-sm">إرسال</button>
            </form>
        </div>

        @if ($expense->payer_id === auth()->id())
            <form method="POST" action="{{ route('expenses.destroy', $expense) }}" onsubmit="return confirm('حذف المصروف؟')" class="mt-4">
                @csrf @method('DELETE')
                <button class="w-full py-3 rounded-2xl bg-rose-50 text-rose-600 font-semibold">{{ __('app.delete') }}</button>
            </form>
        @endif
    </div>
</x-layouts.app>
