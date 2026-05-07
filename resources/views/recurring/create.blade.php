<x-layouts.app title="مصروف دوري">
    <x-header-bar title="مصروف دوري" :back="route('groups.show', $group)" />

    <div class="px-5">
        <form method="POST" action="{{ route('recurring.store', $group) }}" class="card space-y-4">
            @csrf
            <div>
                <label class="text-sm font-semibold">{{ __('app.description') }}</label>
                <input name="description" required class="input mt-1" placeholder="مثلاً: إيجار الشقة">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-sm font-semibold">{{ __('app.amount') }}</label>
                    <input type="number" name="amount" min="0.01" step="0.01" required class="input mt-1">
                </div>
                <div>
                    <label class="text-sm font-semibold">{{ __('app.currency') }}</label>
                    <select name="currency" class="input mt-1">
                        @foreach (['EGP','USD','EUR'] as $c)<option>{{ $c }}</option>@endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="text-sm font-semibold">{{ __('app.category') }}</label>
                <select name="category_id" class="input mt-1">
                    <option value="">— —</option>
                    @foreach ($categories as $c)<option value="{{ $c->id }}">{{ $c->icon }} {{ $c->name() }}</option>@endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-sm font-semibold">التكرار</label>
                    <select name="frequency" class="input mt-1">
                        <option value="monthly">شهري</option>
                        <option value="weekly">أسبوعي</option>
                        <option value="daily">يومي</option>
                        <option value="yearly">سنوي</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-semibold">أول تشغيل</label>
                    <input type="date" name="next_run_at" required min="{{ now()->toDateString() }}" value="{{ now()->toDateString() }}" class="input mt-1">
                </div>
            </div>
            <div>
                <label class="text-sm font-semibold">طريقة التقسيم</label>
                <select name="split_type" class="input mt-1">
                    <option value="equal">{{ __('app.split_equally') }}</option>
                    <option value="exact">{{ __('app.split_exact') }}</option>
                    <option value="percent">{{ __('app.split_percent') }}</option>
                    <option value="shares">{{ __('app.split_shares') }}</option>
                </select>
            </div>
            <button class="btn-primary">{{ __('app.save') }}</button>
        </form>
    </div>
</x-layouts.app>
