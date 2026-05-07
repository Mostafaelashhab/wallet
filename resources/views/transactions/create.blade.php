@php
    $titles = ['expense' => __('app.tx_log_expense'), 'income' => __('app.tx_log_income'), 'transfer' => __('app.tx_log_transfer')];
@endphp
<x-layouts.app :title="$titles[$type]">
    <x-header-bar :title="$titles[$type]" :back="route('dashboard')" />

    <div class="px-5">
        {{-- Type switcher --}}
        <div class="grid grid-cols-3 gap-1 glass-soft rounded-full p-1 text-xs font-bold mb-4">
            @foreach (['expense' => __('app.tx_expense'), 'income' => __('app.tx_income'), 'transfer' => __('app.tx_transfer')] as $k => $v)
                <a href="{{ route('transactions.create') }}?type={{ $k }}"
                   class="text-center py-2 rounded-full {{ $type === $k ? 'bg-white text-ink-950' : 'text-white/60' }}">
                    {{ $v }}
                </a>
            @endforeach
        </div>

        <form method="POST" action="{{ route('transactions.store') }}" enctype="multipart/form-data" class="glass space-y-4 p-5">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">
            <input type="hidden" name="currency" value="EGP">

            {{-- Amount hero --}}
            <div class="text-center pb-3 border-b border-white/10">
                <p class="text-[11px] text-white/55 uppercase tracking-widest">{{ __('app.amount') }}</p>
                <div class="flex items-center justify-center gap-2 mt-1">
                    <input type="number" name="amount" min="0.01" step="0.01" required inputmode="decimal"
                           class="display-amount text-5xl text-center w-48 bg-transparent outline-none placeholder:text-white/20 text-white"
                           placeholder="0">
                    <span class="text-sm font-semibold text-white/40">{{ __('app.currency_symbol') }}</span>
                </div>
            </div>

            {{-- Quick action chips --}}
            <div class="flex items-center gap-2 justify-center flex-wrap">
                <button type="button" id="voice-btn" class="chip flex items-center gap-1.5">
                    <x-icon name="mic" :size="14" /> {{ __('app.tx_voice') }}
                </button>
                <label for="attachment-input" class="chip flex items-center gap-1.5 cursor-pointer">
                    <x-icon name="camera" :size="14" /> {{ __('app.tx_receipt') }}
                </label>
                <input id="attachment-input" type="file" name="attachment" accept="image/*" capture="environment" class="hidden">
                <button type="button" id="loc-btn" class="chip flex items-center gap-1.5">
                    <x-icon name="pin" :size="14" /> {{ __('app.tx_location') }}
                </button>
            </div>
            <p id="action-out" class="text-xs text-white/55 text-center -mt-2"></p>

            <div>
                <label class="text-xs font-semibold text-white/70">{{ __('app.description') }}</label>
                <input type="text" name="description" id="desc" required class="input mt-1"
                       placeholder="{{ $type === 'income' ? __('app.tx_placeholder_income') : ($type === 'transfer' ? __('app.tx_placeholder_transfer') : __('app.tx_placeholder_expense')) }}">
            </div>

            <div>
                <label class="text-xs font-semibold text-white/70">{{ $type === 'transfer' ? __('app.tx_from_account') : ($type === 'income' ? __('app.tx_in_account') : __('app.tx_from_account')) }}</label>
                <div class="grid grid-cols-2 gap-2 mt-1">
                    @foreach ($accounts as $a)
                        <label class="glass-soft rounded-2xl p-3 cursor-pointer has-[:checked]:bg-white/15 has-[:checked]:border-indigo-400/60">
                            <input type="radio" name="account_id" value="{{ $a->id }}" class="sr-only" {{ $loop->first ? 'checked' : '' }} required>
                            <div class="flex items-center gap-2">
                                <div class="w-9 h-9 rounded-xl grid place-items-center text-white shrink-0" style="background: {{ $a->color }}">
                                    <x-icon name="{{ $a->type === 'cash' ? 'cash' : ($a->type === 'bank' ? 'bank' : ($a->type === 'card' ? 'card' : ($a->type === 'savings' ? 'piggy' : 'mobile'))) }}" :size="16" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold truncate text-white">{{ $a->name }}</p>
                                    <p class="text-[11px] text-white/50">{{ number_format($a->balance(), 0) }} {{ $a->currency }}</p>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            @if ($type === 'transfer')
                <div>
                    <label class="text-xs font-semibold text-white/70">{{ __('app.tx_to_account') }}</label>
                    <select name="transfer_to_account_id" required class="input mt-1">
                        <option value="">{{ __('app.tx_choose') }}</option>
                        @foreach ($accounts as $a)
                            <option value="{{ $a->id }}">{{ $a->name }} ({{ number_format($a->balance(), 0) }} {{ $a->currency }})</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if ($type !== 'transfer')
                <div>
                    <label class="text-xs font-semibold text-white/70">{{ __('app.category') }}</label>
                    <div class="grid grid-cols-4 gap-2 mt-1">
                        @foreach ($categories as $c)
                            <label class="text-center cursor-pointer has-[:checked]:bg-white/15 has-[:checked]:border-indigo-400/60 rounded-2xl p-2 glass-soft">
                                <input type="radio" name="category_id" value="{{ $c->id }}" class="sr-only">
                                <div class="w-10 h-10 mx-auto rounded-xl grid place-items-center text-white" style="background: {{ $c->color }}">
                                    <x-icon name="{{ $c->icon_name }}" :size="18" />
                                </div>
                                <p class="text-[11px] mt-1 font-semibold leading-tight text-white">{{ $c->name() }}</p>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            <div>
                <label class="text-xs font-semibold text-white/70">{{ __('app.date') }}</label>
                <input type="datetime-local" name="occurred_at" required class="input mt-1" value="{{ now()->format('Y-m-d\TH:i') }}">
            </div>

            <input type="hidden" name="location_lat" id="location_lat">
            <input type="hidden" name="location_lng" id="location_lng">
            <input type="hidden" name="location_name" id="location_name">

            <button class="btn-primary">{{ __('app.save') }}</button>
        </form>
    </div>

    @push('scripts')
    <script>
        const out = document.getElementById('action-out');
        document.getElementById('voice-btn').addEventListener('click', async () => {
            out.textContent = '{{ __('app.tx_voice_listening') }}';
            try {
                const locale = '{{ app()->getLocale() }}' === 'ar' ? 'ar-EG' : 'en-US';
                const results = await startVoice(locale);
                const text = results[0];
                out.textContent = text;
                document.getElementById('desc').value = text;
                const m = text.match(/(\d+(?:[.,]\d+)?)/);
                if (m) document.querySelector('[name="amount"]').value = parseFloat(m[1].replace(',', '.'));
            } catch (e) { out.textContent = '{{ __('app.tx_voice_unavailable') }}'; }
        });
        document.getElementById('loc-btn').addEventListener('click', async () => {
            out.textContent = '{{ __('app.tx_loc_resolving') }}';
            try {
                const p = await getLocation();
                document.getElementById('location_lat').value = p.lat;
                document.getElementById('location_lng').value = p.lng;
                document.getElementById('location_name').value = `${p.lat.toFixed(4)}, ${p.lng.toFixed(4)}`;
                out.textContent = `${p.lat.toFixed(4)}, ${p.lng.toFixed(4)}`;
            } catch (e) { out.textContent = '{{ __('app.tx_loc_failed') }}'; }
        });
        document.getElementById('attachment-input').addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;
            out.textContent = '{{ __('app.tx_ocr_reading') }}';
            try {
                const r = await ocrReceipt(file);
                if (r.amount) {
                    document.querySelector('[name="amount"]').value = r.amount;
                    out.textContent = '{{ __('app.tx_ocr_amount') }}: ' + r.amount;
                } else {
                    out.textContent = '{{ __('app.tx_ocr_failed') }}';
                }
            } catch (err) { out.textContent = '{{ __('app.tx_ocr_failed') }}'; }
        });
    </script>
    @endpush
</x-layouts.app>
