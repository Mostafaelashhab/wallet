<x-layouts.app :title="__('app.wallet_new')">
    <x-header-bar :title="__('app.wallet_new')" :back="route('accounts.index')" />

    <div class="px-5">
        <form method="POST" action="{{ route('accounts.store') }}" class="glass space-y-4 p-5">
            @csrf
            <input type="hidden" name="currency" value="EGP">

            <div>
                <label class="text-xs font-semibold text-white/70">{{ __('app.wallet_name') }}</label>
                <input type="text" name="name" required class="input mt-1" placeholder="{{ __('app.wallet_name_hint') }}">
            </div>

            <div>
                <label class="text-xs font-semibold text-white/70">{{ __('app.wallet_type') }}</label>
                <div class="grid grid-cols-3 gap-2 mt-1">
                    @foreach ($types as $key => $opt)
                        <label class="text-center cursor-pointer rounded-2xl p-3 glass-soft has-[:checked]:bg-white/15 has-[:checked]:border-indigo-400/60">
                            <input type="radio" name="type" value="{{ $key }}" class="sr-only" required {{ $loop->first ? 'checked' : '' }}>
                            <div class="w-10 h-10 mx-auto rounded-xl grid place-items-center text-white" style="background: {{ $opt['color'] }}">
                                <x-icon name="{{ $key === 'cash' ? 'cash' : ($key === 'bank' ? 'bank' : ($key === 'card' ? 'card' : ($key === 'savings' ? 'piggy' : 'mobile'))) }}" :size="18" />
                            </div>
                            <p class="text-[11px] mt-1.5 font-semibold text-white">{{ __('app.type_' . $key) }}</p>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="text-xs font-semibold text-white/70">{{ __('app.wallet_institution') }}</label>
                <input type="text" name="institution" class="input mt-1" placeholder="{{ __('app.wallet_institution_hint') }}">
            </div>

            <div>
                <label class="text-xs font-semibold text-white/70">{{ __('app.wallet_opening') }}</label>
                <input type="number" name="opening_balance" step="0.01" required class="input mt-1" value="0">
            </div>

            <div>
                <label class="text-xs font-semibold text-white/70">{{ __('app.wallet_color') }}</label>
                <input type="color" name="color" value="#6366F1" class="input mt-1 h-12">
            </div>

            <label class="flex items-center gap-2 text-sm text-white/80">
                <input type="checkbox" name="include_in_total" value="1" checked class="w-4 h-4 accent-indigo-500">
                {{ __('app.wallet_include_total') }}
            </label>

            <button class="btn-primary">{{ __('app.save') }}</button>
        </form>
    </div>
</x-layouts.app>
