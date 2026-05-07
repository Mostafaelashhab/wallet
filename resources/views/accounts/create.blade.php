<x-layouts.app title="محفظة جديدة" hero="short">
    <x-header-bar title="محفظة جديدة" :back="route('accounts.index')" />

    <div class="px-5">
        <form method="POST" action="{{ route('accounts.store') }}" class="card space-y-4">
            @csrf
            <input type="hidden" name="currency" value="EGP">

            <div>
                <label class="text-xs font-bold text-stone-600">اسم المحفظة</label>
                <input type="text" name="name" required class="input mt-1" placeholder="مثلاً: حساب CIB">
            </div>

            <div>
                <label class="text-xs font-bold text-stone-600">النوع</label>
                <div class="grid grid-cols-3 gap-2 mt-1">
                    @foreach ($types as $key => $opt)
                        <label class="text-center cursor-pointer rounded-2xl p-3 bg-stone-50 has-[:checked]:bg-brand-50 has-[:checked]:ring-2 has-[:checked]:ring-brand-400">
                            <input type="radio" name="type" value="{{ $key }}" class="sr-only" required {{ $loop->first ? 'checked' : '' }}>
                            <div class="w-10 h-10 mx-auto rounded-xl grid place-items-center text-white" style="background: {{ $opt['color'] }}">
                                <x-icon name="{{ $key === 'cash' ? 'cash' : ($key === 'bank' ? 'bank' : ($key === 'card' ? 'card' : ($key === 'savings' ? 'piggy' : 'mobile'))) }}" :size="18" />
                            </div>
                            <p class="text-[11px] mt-1 font-semibold">{{ $opt['label_ar'] }}</p>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-stone-600">الجهة (اختياري)</label>
                <input type="text" name="institution" class="input mt-1" placeholder="CIB · Vodafone Cash · InstaPay">
            </div>

            <div>
                <label class="text-xs font-bold text-stone-600">الرصيد الافتتاحي</label>
                <input type="number" name="opening_balance" step="0.01" required class="input mt-1" value="0">
            </div>

            <div>
                <label class="text-xs font-bold text-stone-600">اللون</label>
                <input type="color" name="color" value="#3B82F6" class="input mt-1 h-12">
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="include_in_total" value="1" checked class="w-4 h-4 accent-orange-500">
                ضمّن في صافي الثروة
            </label>

            <button class="btn-primary">{{ __('app.save') }}</button>
        </form>
    </div>
</x-layouts.app>
