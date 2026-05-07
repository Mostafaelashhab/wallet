<x-layouts.app title="هدف جديد">
    <x-header-bar title="هدف جديد" :back="route('groups.show', $group)" />

    <div class="px-5">
        <form method="POST" action="{{ route('goals.store', $group) }}" class="card space-y-4">
            @csrf
            <div>
                <label class="text-sm font-semibold">اسم الهدف</label>
                <input name="name" required class="input mt-1" placeholder="مثلاً: رحلة الساحل">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-sm font-semibold">المبلغ المستهدف</label>
                    <input type="number" name="target_amount" min="1" step="0.01" required class="input mt-1">
                </div>
                <div>
                    <label class="text-sm font-semibold">{{ __('app.currency') }}</label>
                    <select name="currency" class="input mt-1">
                        @foreach (['EGP','USD','EUR'] as $c)<option value="{{ $c }}">{{ $c }}</option>@endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-sm font-semibold">الديدلاين</label>
                    <input type="date" name="deadline" class="input mt-1" min="{{ now()->addDay()->toDateString() }}">
                </div>
                <div>
                    <label class="text-sm font-semibold">الأيقونة</label>
                    <select name="icon" class="input mt-1">
                        @foreach (['🎯','🏖️','🏠','🚗','💍','🎓','🛫'] as $e)<option>{{ $e }}</option>@endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="text-sm font-semibold">اللون</label>
                <input type="color" name="color" value="#10B981" class="input mt-1 h-12">
            </div>
            <button class="btn-primary">{{ __('app.save') }}</button>
        </form>
    </div>
</x-layouts.app>
