<x-layouts.app :title="__('app.onboarding_title')">
    <div class="safe-top px-5 pt-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-white">
                <div class="w-8 h-8 rounded-full bg-white grid place-items-center">
                    <span class="text-ink-950 font-extrabold">S</span>
                </div>
                <span class="font-extrabold text-base tracking-tight">Splitty</span>
            </div>
            <form method="POST" action="{{ route('onboarding.skip') }}">
                @csrf
                <button class="text-white/55 text-sm">{{ __('app.skip') }}</button>
            </form>
        </div>
    </div>

    <div class="px-5 mt-4">
        <h1 class="text-3xl font-extrabold text-white tracking-tight">{{ __('app.onboarding_title') }}</h1>
        <p class="text-white/55 mt-1 text-sm">{{ __('app.onboarding_subtitle') }}</p>

        <form method="POST" action="{{ route('onboarding.complete') }}" class="mt-6 space-y-4">
            @csrf

            <div class="glass p-5 rounded-3xl">
                <p class="text-[11px] font-bold text-white/55 uppercase tracking-widest mb-3">{{ __('app.onboarding_balances') }}</p>
                <div class="space-y-3">
                    @foreach ($accounts as $a)
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl grid place-items-center text-white shrink-0" style="background: {{ $a->color }}">
                                <x-icon name="{{ $a->type === 'cash' ? 'cash' : ($a->type === 'bank' ? 'bank' : ($a->type === 'card' ? 'card' : ($a->type === 'savings' ? 'piggy' : 'mobile'))) }}" :size="18" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-white font-semibold text-sm truncate">{{ $a->name }}</p>
                                <p class="text-[11px] text-white/50">{{ $a->institution ?? '' }}</p>
                            </div>
                            <input type="number" step="0.01" name="balances[{{ $a->id }}]" placeholder="0" inputmode="decimal"
                                   class="input !py-2 !px-3 w-32 text-end">
                        </div>
                    @endforeach
                </div>
                <p class="text-[11px] text-white/40 mt-3 text-center">{{ __('app.onboarding_balances_hint') }}</p>
            </div>

            <div class="glass p-5 rounded-3xl">
                <p class="text-[11px] font-bold text-white/55 uppercase tracking-widest mb-3">{{ __('app.onboarding_salary') }}</p>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-semibold text-white/70">{{ __('app.amount') }}</label>
                        <input type="number" step="0.01" name="salary" class="input mt-1" placeholder="0">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-white/70">{{ __('app.tx_in_account') }}</label>
                        <select name="salary_account_id" class="input mt-1">
                            <option value="">{{ __('app.tx_choose') }}</option>
                            @foreach ($accounts as $a)
                                <option value="{{ $a->id }}" @selected($a->type === 'bank')>{{ $a->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <p class="text-[11px] text-white/40 mt-3">{{ __('app.onboarding_salary_hint') }}</p>
            </div>

            <button class="btn-primary">{{ __('app.onboarding_done') }}</button>
        </form>
    </div>
</x-layouts.app>
