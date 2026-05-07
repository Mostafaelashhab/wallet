<x-layouts.app :title="__('app.budgets_create')">
    <x-header-bar :title="__('app.budgets_create')" :back="route('budgets.index')" />

    <div class="px-5">
        <form method="POST" action="{{ route('budgets.store') }}" class="glass space-y-4 p-5 rounded-3xl">
            @csrf
            <input type="hidden" name="currency" value="EGP">
            <input type="hidden" name="period" value="month">

            <div class="text-center pb-3 border-b border-white/10">
                <p class="text-[11px] text-white/55 uppercase tracking-widest">{{ __('app.budget_monthly_limit') }}</p>
                <div class="flex items-center justify-center gap-2 mt-1">
                    <input type="number" name="amount" min="1" step="0.01" required inputmode="decimal"
                           class="display-amount text-5xl text-center w-48 bg-transparent outline-none placeholder:text-white/20 text-white" placeholder="0">
                    <span class="text-sm font-semibold text-white/40">{{ __('app.currency_symbol') }}</span>
                </div>
            </div>

            <div>
                <label class="text-xs font-semibold text-white/70">{{ __('app.category') }}</label>
                @if ($categories->isEmpty())
                    <p class="text-white/55 text-sm mt-2">{{ __('app.budget_all_used') }}</p>
                @else
                    <div class="grid grid-cols-3 gap-2 mt-1">
                        @foreach ($categories as $c)
                            <label class="text-center cursor-pointer rounded-2xl p-2.5 glass-soft has-[:checked]:bg-white/15 has-[:checked]:border-indigo-400/60">
                                <input type="radio" name="category_id" value="{{ $c->id }}" class="sr-only" {{ $loop->first ? 'checked' : '' }} required>
                                <div class="w-10 h-10 mx-auto rounded-xl grid place-items-center text-white" style="background: {{ $c->color }}">
                                    <x-icon name="{{ $c->icon_name }}" :size="18" />
                                </div>
                                <p class="text-[11px] mt-1.5 font-semibold text-white leading-tight">{{ $c->name() }}</p>
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>

            <button class="btn-primary" {{ $categories->isEmpty() ? 'disabled' : '' }}>{{ __('app.save') }}</button>
        </form>
    </div>
</x-layouts.app>
