@php
    $titles = ['expense' => __('app.recurring_expense'), 'income' => __('app.recurring_income'), 'transfer' => __('app.recurring_transfer')];
@endphp
<x-layouts.app :title="$titles[$type]">
    <x-header-bar :title="$titles[$type]" :back="route('personal-recurring.index')" />

    <div class="px-5">
        <div class="grid grid-cols-3 gap-1 glass-soft rounded-full p-1 text-xs font-bold mb-4">
            @foreach (['expense' => __('app.tx_expense'), 'income' => __('app.tx_income'), 'transfer' => __('app.tx_transfer')] as $k => $v)
                <a href="{{ route('personal-recurring.create') }}?type={{ $k }}"
                   class="text-center py-2 rounded-full {{ $type === $k ? 'bg-white text-ink-950' : 'text-white/60' }}">
                    {{ $v }}
                </a>
            @endforeach
        </div>

        <form method="POST" action="{{ route('personal-recurring.store') }}" class="glass space-y-4 p-5 rounded-3xl">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">
            <input type="hidden" name="currency" value="EGP">

            <div class="text-center pb-3 border-b border-white/10">
                <p class="text-[11px] text-white/55 uppercase tracking-widest">{{ __('app.amount') }}</p>
                <div class="flex items-center justify-center gap-2 mt-1">
                    <input type="number" name="amount" min="0.01" step="0.01" required inputmode="decimal"
                           class="display-amount text-5xl text-center w-48 bg-transparent outline-none placeholder:text-white/20 text-white" placeholder="0">
                    <span class="text-sm font-semibold text-white/40">{{ __('app.currency_symbol') }}</span>
                </div>
            </div>

            <div>
                <label class="text-xs font-semibold text-white/70">{{ __('app.description') }}</label>
                <input type="text" name="description" required class="input mt-1" placeholder="{{ $type === 'income' ? __('app.tx_placeholder_income') : __('app.tx_placeholder_expense') }}">
            </div>

            <div>
                <label class="text-xs font-semibold text-white/70">{{ $type === 'income' ? __('app.tx_in_account') : __('app.tx_from_account') }}</label>
                <select name="account_id" required class="input mt-1">
                    @foreach ($accounts as $a)
                        <option value="{{ $a->id }}">{{ $a->name }}</option>
                    @endforeach
                </select>
            </div>

            @if ($type === 'transfer')
                <div>
                    <label class="text-xs font-semibold text-white/70">{{ __('app.tx_to_account') }}</label>
                    <select name="transfer_to_account_id" required class="input mt-1">
                        <option value="">{{ __('app.tx_choose') }}</option>
                        @foreach ($accounts as $a)
                            <option value="{{ $a->id }}">{{ $a->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if ($type !== 'transfer')
                <div>
                    <label class="text-xs font-semibold text-white/70">{{ __('app.category') }}</label>
                    <select name="category_id" class="input mt-1">
                        <option value="">{{ __('app.category_auto') }}</option>
                        @foreach ($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name() }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-semibold text-white/70">{{ __('app.subs_freq') }}</label>
                    <select name="frequency" required class="input mt-1">
                        <option value="monthly" selected>{{ __('app.subs_freq_monthly') }}</option>
                        <option value="weekly">{{ __('app.subs_freq_weekly') }}</option>
                        <option value="daily">{{ __('app.recurring_daily') }}</option>
                        <option value="yearly">{{ __('app.subs_freq_yearly') }}</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-white/70">{{ __('app.recurring_first_run') }}</label>
                    <input type="date" name="next_run_at" required min="{{ now()->toDateString() }}" value="{{ now()->toDateString() }}" class="input mt-1">
                </div>
            </div>

            <button class="btn-primary">{{ __('app.save') }}</button>
        </form>
    </div>
</x-layouts.app>
