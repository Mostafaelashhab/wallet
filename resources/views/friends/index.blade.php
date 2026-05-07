<x-layouts.app :title="__('app.friends')">
    <x-header-bar :title="__('app.friends')" :back="route('dashboard')" />

    <div class="px-5">
        @if ($pending->count())
            <h3 class="section-title mb-2 text-white">طلبات معلقة</h3>
            <div class="card space-y-3 mb-4">
                @foreach ($pending as $p)
                    <div class="flex items-center gap-3">
                        <x-avatar :user="$p->requester" :size="36" />
                        <div class="flex-1"><p class="font-semibold">{{ $p->requester->name }}</p></div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="card space-y-3">
            @forelse ($friends as $f)
                <a href="{{ route('friends.show', $f) }}" class="flex items-center gap-3 tap-anim">
                    <x-avatar :user="$f" :size="42" />
                    <div class="flex-1">
                        <p class="font-semibold">{{ $f->name }}</p>
                        <p class="text-xs text-stone-500">{{ $f->email }}</p>
                    </div>
                    @php $bal = (float) $f->balance_with_me; @endphp
                    @if (abs($bal) < 0.01)
                        <span class="amount-pill muted text-xs">{{ __('app.settled_up') }}</span>
                    @elseif ($bal > 0)
                        <span class="amount-pill owed text-xs">+{{ number_format($bal, 2) }}</span>
                    @else
                        <span class="amount-pill owe text-xs">{{ number_format($bal, 2) }}</span>
                    @endif
                </a>
            @empty
                <p class="text-stone-500 text-sm text-center">مفيش أصحاب لسه.</p>
            @endforelse
        </div>

        <form method="POST" action="{{ route('friends.store') }}" class="card mt-4">
            @csrf
            <p class="text-sm font-semibold text-stone-700 mb-2">إضافة صديق</p>
            <div class="flex gap-2">
                <input type="email" name="email" required class="input !py-2 !px-3 text-sm" placeholder="ايميل الصاحب">
                <button class="btn-ghost text-sm">{{ __('app.add') }}</button>
            </div>
            @error('email') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
        </form>
    </div>
</x-layouts.app>
