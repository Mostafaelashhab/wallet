<x-layouts.app title="مجموعة جديدة">
    <x-header-bar title="مجموعة جديدة" :back="route('groups.index')" />

    <div class="px-5">
        <form method="POST" action="{{ route('groups.store') }}" class="card space-y-4">
            @csrf
            <input type="hidden" name="icon" value="">
            <div>
                <label class="text-xs font-bold text-stone-600">اسم المجموعة</label>
                <input type="text" name="name" required class="input mt-1" placeholder="مثلاً: رحلة الساحل">
            </div>
            <div>
                <label class="text-xs font-bold text-stone-600">الأيقونة</label>
                <div class="grid grid-cols-5 gap-2 mt-1">
                    @foreach (['party','cake','shopping','plane','rent','pizza','ball','ring','graduation','gamepad'] as $i => $ic)
                        <label class="text-center cursor-pointer rounded-2xl p-2 bg-stone-50 has-[:checked]:bg-brand-50 has-[:checked]:ring-2 has-[:checked]:ring-brand-400">
                            <input type="radio" name="icon_name" value="{{ $ic }}" class="sr-only" {{ $i === 0 ? 'checked' : '' }}>
                            <div class="w-10 h-10 mx-auto rounded-xl grid place-items-center bg-stone-200 text-stone-700">
                                <x-icon name="{{ $ic }}" :size="20" />
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-stone-600">{{ __('app.currency') }}</label>
                    <select name="currency" class="input mt-1">
                        @foreach (['EGP','USD','EUR','GBP','SAR','AED'] as $c)
                            <option value="{{ $c }}" @selected($c === 'EGP')>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-stone-600">اللون</label>
                    <input type="color" name="color" value="#FF6B35" class="input mt-1 h-12">
                </div>
            </div>
            <label class="flex items-center gap-2 text-sm text-stone-700">
                <input type="checkbox" name="simplify_debts" value="1" checked class="w-4 h-4 accent-orange-500">
                {{ __('app.simplify_debts') }}
            </label>

            <div>
                <p class="text-xs font-bold text-stone-600 mb-2">إضافة أعضاء</p>
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
