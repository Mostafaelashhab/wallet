<x-layouts.app :title="__('app.subs_add')">
    <x-header-bar :title="__('app.subs_add')" :back="route('subscriptions.index')" />

    <div class="px-5">
        <form method="POST" action="{{ route('subscriptions.store') }}" class="glass space-y-4 p-5 rounded-3xl">
            @csrf
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
                <label class="text-xs font-semibold text-white/70">{{ __('app.subs_name') }}</label>
                <input type="text" name="name" required class="input mt-1" placeholder="Netflix · Spotify · Anghami">
            </div>

            <div>
                <label class="text-xs font-semibold text-white/70">{{ __('app.subs_freq') }}</label>
                <div class="grid grid-cols-3 gap-1 mt-1 bg-white/5 rounded-full p-1 text-xs font-bold border border-white/10">
                    @foreach (['weekly', 'monthly', 'yearly'] as $i => $f)
                        <label class="text-center cursor-pointer rounded-full py-2 has-[:checked]:bg-white has-[:checked]:text-ink-950 text-white/60">
                            <input type="radio" name="frequency" value="{{ $f }}" class="sr-only" {{ $f === 'monthly' ? 'checked' : '' }}>
                            <span>{{ __('app.subs_freq_' . $f) }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-semibold text-white/70">{{ __('app.subs_next_billing') }}</label>
                    <input type="date" name="next_billing_at" required class="input mt-1" value="{{ now()->addMonth()->toDateString() }}">
                </div>
                <div>
                    <label class="text-xs font-semibold text-white/70">{{ __('app.tx_from_account') }}</label>
                    <select name="account_id" class="input mt-1">
                        <option value="">{{ __('app.tx_choose') }}</option>
                        @foreach ($accounts as $a)
                            <option value="{{ $a->id }}">{{ $a->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="text-xs font-semibold text-white/70">{{ __('app.subs_color') }}</label>
                <input type="color" name="color" value="#6366F1" class="input mt-1 h-12">
            </div>

            <div>
                <label class="text-xs font-semibold text-white/70">{{ __('app.subs_cancel_url') }}</label>
                <input type="url" name="cancel_url" class="input mt-1" placeholder="https://...">
            </div>

            <label class="flex items-center gap-2 text-sm text-white/80">
                <input type="checkbox" name="auto_log" value="1" checked class="w-4 h-4 accent-indigo-500">
                {{ __('app.subs_auto_log') }}
            </label>

            <button class="btn-primary">{{ __('app.save') }}</button>
        </form>
    </div>
</x-layouts.app>
