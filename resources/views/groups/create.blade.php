<x-layouts.app title="إضافة مجموعة">
    <x-header-bar title="مجموعة جديدة" :back="route('groups.index')" />

    <div class="px-5">
        <form method="POST" action="{{ route('groups.store') }}" class="card space-y-4">
            @csrf
            <div>
                <label class="text-sm font-semibold text-stone-700">اسم المجموعة</label>
                <input type="text" name="name" required class="input mt-1" placeholder="مثلاً: رحلة الساحل">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-sm font-semibold text-stone-700">الأيقونة</label>
                    <select name="icon" class="input mt-1">
                        @foreach (['🎉','🎂','🛍️','✈️','🏠','🍕','⚽','💍','🎓','🎮'] as $emoji)
                            <option value="{{ $emoji }}">{{ $emoji }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-semibold text-stone-700">{{ __('app.currency') }}</label>
                    <select name="currency" class="input mt-1">
                        @foreach (['EGP','USD','EUR','GBP','SAR','AED'] as $c)
                            <option value="{{ $c }}" @selected($c === 'EGP')>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="text-sm font-semibold text-stone-700">لون المجموعة</label>
                <input type="color" name="color" value="#FF6B35" class="input mt-1 h-12">
            </div>
            <label class="flex items-center gap-2 text-sm text-stone-700">
                <input type="checkbox" name="simplify_debts" value="1" checked class="w-4 h-4 accent-orange-500">
                {{ __('app.simplify_debts') }} (تقليل عدد التحويلات تلقائياً)
            </label>

            <div>
                <p class="text-sm font-semibold text-stone-700 mb-2">إضافة أعضاء (من الأصحاب)</p>
                <div class="grid grid-cols-2 gap-2 max-h-56 overflow-y-auto">
                    @foreach (auth()->user()->friends()->get() as $f)
                        <label class="flex items-center gap-2 bg-stone-50 rounded-xl px-3 py-2">
                            <input type="checkbox" name="members[]" value="{{ $f->id }}" class="w-4 h-4 accent-orange-500">
                            <x-avatar :user="$f" :size="28" />
                            <span class="text-sm truncate">{{ $f->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <button class="btn-primary">{{ __('app.save') }}</button>
        </form>
    </div>
</x-layouts.app>
