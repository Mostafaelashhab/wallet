<x-layouts.app title="إضافة جديدة" hero="flat">
    <x-header-bar title="إضافة جديدة" :back="route('dashboard')" />

    <div class="px-5">
        <p class="text-stone-500 text-sm mb-4">اختار نوع الحركة اللي عاوز تسجلها.</p>

        <a href="{{ route('transactions.create') }}?type=expense" class="card mb-3 flex items-center gap-4 tap-anim">
            <div class="w-14 h-14 rounded-2xl grid place-items-center bg-rose-100 text-rose-600">
                <x-icon name="arrow-up" :size="28" />
            </div>
            <div class="flex-1">
                <p class="font-bold">سجّل مصروف</p>
                <p class="text-xs text-stone-500">صرفت كذا في كذا · من أي محفظة</p>
            </div>
            <x-icon name="chevron-right" :size="20" class="text-stone-400 flip-rtl" />
        </a>

        <a href="{{ route('transactions.create') }}?type=income" class="card mb-3 flex items-center gap-4 tap-anim">
            <div class="w-14 h-14 rounded-2xl grid place-items-center bg-emerald-100 text-emerald-600">
                <x-icon name="arrow-down" :size="28" />
            </div>
            <div class="flex-1">
                <p class="font-bold">سجّل دخل</p>
                <p class="text-xs text-stone-500">مرتب · فري لانس · هدية</p>
            </div>
            <x-icon name="chevron-right" :size="20" class="text-stone-400 flip-rtl" />
        </a>

        <a href="{{ route('transactions.create') }}?type=transfer" class="card mb-3 flex items-center gap-4 tap-anim">
            <div class="w-14 h-14 rounded-2xl grid place-items-center bg-sky-100 text-sky-600">
                <x-icon name="transfer" :size="28" />
            </div>
            <div class="flex-1">
                <p class="font-bold">تحويل بين محافظ</p>
                <p class="text-xs text-stone-500">من حساب لحساب</p>
            </div>
            <x-icon name="chevron-right" :size="20" class="text-stone-400 flip-rtl" />
        </a>

        <a href="{{ route('expenses.create') }}" class="card flex items-center gap-4 tap-anim">
            <div class="w-14 h-14 rounded-2xl grid place-items-center bg-orange-100 text-brand-600">
                <x-icon name="group" :size="28" />
            </div>
            <div class="flex-1">
                <p class="font-bold">مصروف مع الشلة</p>
                <p class="text-xs text-stone-500">قسّم على أصحابك</p>
            </div>
            <x-icon name="chevron-right" :size="20" class="text-stone-400 flip-rtl" />
        </a>
    </div>
</x-layouts.app>
