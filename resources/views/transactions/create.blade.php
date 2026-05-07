@php
    $titles = ['expense' => 'سجّل مصروف', 'income' => 'سجّل دخل', 'transfer' => 'تحويل'];
    $accentBg = ['expense' => 'bg-rose-100', 'income' => 'bg-emerald-100', 'transfer' => 'bg-sky-100'];
    $accentFg = ['expense' => 'text-rose-600', 'income' => 'text-emerald-600', 'transfer' => 'text-sky-600'];
    $iconName = ['expense' => 'arrow-up', 'income' => 'arrow-down', 'transfer' => 'transfer'];
@endphp
<x-layouts.app :title="$titles[$type]" hero="short">
    <x-header-bar :title="$titles[$type]" :back="route('dashboard')" />

    <div class="px-5">
        {{-- Type switcher pills --}}
        <div class="grid grid-cols-3 gap-1 bg-white/15 backdrop-blur rounded-full p-1 text-xs font-bold mb-4">
            @foreach (['expense' => 'مصروف','income' => 'دخل','transfer' => 'تحويل'] as $k => $v)
                <a href="{{ route('transactions.create') }}?type={{ $k }}"
                   class="text-center py-2 rounded-full {{ $type === $k ? 'bg-white text-stone-900' : 'text-white/85' }}">
                    {{ $v }}
                </a>
            @endforeach
        </div>

        <form method="POST" action="{{ route('transactions.store') }}" enctype="multipart/form-data" class="card space-y-4">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">
            <input type="hidden" name="currency" value="EGP">

            {{-- Amount hero --}}
            <div class="text-center pb-3 border-b border-stone-100">
                <p class="text-xs text-stone-500">المبلغ</p>
                <div class="flex items-center justify-center gap-2 mt-1">
                    <input type="number" name="amount" min="0.01" step="0.01" required inputmode="decimal"
                           class="text-4xl font-extrabold text-center w-44 bg-transparent outline-none placeholder:text-stone-300"
                           placeholder="0.00">
                    <span class="text-sm font-semibold text-stone-400">EGP</span>
                </div>
            </div>

            {{-- Quick action buttons (voice / receipt / location) --}}
            <div class="flex items-center gap-2 justify-center flex-wrap">
                <button type="button" id="voice-btn" class="chip text-xs flex items-center gap-1.5">
                    <x-icon name="mic" :size="14" /> صوت
                </button>
                <label for="attachment-input" class="chip text-xs flex items-center gap-1.5 cursor-pointer">
                    <x-icon name="camera" :size="14" /> إيصال
                </label>
                <input id="attachment-input" type="file" name="attachment" accept="image/*" capture="environment" class="hidden">
                <button type="button" id="loc-btn" class="chip text-xs flex items-center gap-1.5">
                    <x-icon name="pin" :size="14" /> مكان
                </button>
            </div>
            <p id="action-out" class="text-xs text-stone-500 text-center -mt-2"></p>

            <div>
                <label class="text-xs font-bold text-stone-600">{{ __('app.description') }}</label>
                <input type="text" name="description" id="desc" required class="input mt-1"
                       placeholder="{{ $type === 'income' ? 'مرتب · مشروع · هدية' : ($type === 'transfer' ? 'تحويل لحسابي' : 'مثلاً: غدا مع الشلة') }}">
            </div>

            <div>
                <label class="text-xs font-bold text-stone-600">{{ $type === 'transfer' ? 'من حساب' : ($type === 'income' ? 'في حساب' : 'من حساب') }}</label>
                <div class="grid grid-cols-2 gap-2 mt-1">
                    @foreach ($accounts as $a)
                        <label class="bg-stone-50 rounded-2xl p-3 cursor-pointer has-[:checked]:bg-brand-50 has-[:checked]:ring-2 has-[:checked]:ring-brand-400">
                            <input type="radio" name="account_id" value="{{ $a->id }}" class="sr-only" {{ $loop->first ? 'checked' : '' }} required>
                            <div class="flex items-center gap-2">
                                <div class="w-9 h-9 rounded-xl grid place-items-center text-white shrink-0" style="background: {{ $a->color }}">
                                    <x-icon name="{{ $a->type === 'cash' ? 'cash' : ($a->type === 'bank' ? 'bank' : ($a->type === 'card' ? 'card' : ($a->type === 'savings' ? 'piggy' : 'mobile'))) }}" :size="18" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold truncate">{{ $a->name }}</p>
                                    <p class="text-[11px] text-stone-500">{{ number_format($a->balance(), 0) }} {{ $a->currency }}</p>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            @if ($type === 'transfer')
                <div>
                    <label class="text-xs font-bold text-stone-600">إلى حساب</label>
                    <select name="transfer_to_account_id" required class="input mt-1">
                        <option value="">— اختار —</option>
                        @foreach ($accounts as $a)
                            <option value="{{ $a->id }}">{{ $a->name }} ({{ number_format($a->balance(), 0) }} {{ $a->currency }})</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if ($type !== 'transfer')
                <div>
                    <label class="text-xs font-bold text-stone-600">{{ __('app.category') }}</label>
                    <div class="grid grid-cols-4 gap-2 mt-1">
                        @foreach ($categories as $c)
                            <label class="text-center cursor-pointer has-[:checked]:bg-brand-50 has-[:checked]:ring-2 has-[:checked]:ring-brand-400 rounded-2xl p-2 bg-stone-50">
                                <input type="radio" name="category_id" value="{{ $c->id }}" class="sr-only">
                                <div class="w-10 h-10 mx-auto rounded-xl grid place-items-center text-white" style="background: {{ $c->color }}">
                                    <x-icon name="{{ $c->icon_name }}" :size="18" />
                                </div>
                                <p class="text-[11px] mt-1 font-semibold leading-tight">{{ $c->name() }}</p>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            <div>
                <label class="text-xs font-bold text-stone-600">{{ __('app.date') }}</label>
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
            out.textContent = 'يسمعك الآن…';
            try {
                const locale = '{{ app()->getLocale() }}' === 'ar' ? 'ar-EG' : 'en-US';
                const results = await startVoice(locale);
                const text = results[0];
                out.textContent = text;
                document.getElementById('desc').value = text;
                const m = text.match(/(\d+(?:[.,]\d+)?)/);
                if (m) document.querySelector('[name="amount"]').value = parseFloat(m[1].replace(',', '.'));
            } catch (e) { out.textContent = 'مش متاح: ' + e.message; }
        });
        document.getElementById('loc-btn').addEventListener('click', async () => {
            out.textContent = 'بنحدد المكان…';
            try {
                const p = await getLocation();
                document.getElementById('location_lat').value = p.lat;
                document.getElementById('location_lng').value = p.lng;
                document.getElementById('location_name').value = `${p.lat.toFixed(4)}, ${p.lng.toFixed(4)}`;
                out.textContent = `📍 ${p.lat.toFixed(4)}, ${p.lng.toFixed(4)}`;
            } catch (e) { out.textContent = 'تعذر تحديد المكان'; }
        });
        document.getElementById('attachment-input').addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;
            out.textContent = '⏳ بنقرا الإيصال…';
            try {
                const r = await ocrReceipt(file);
                if (r.amount) {
                    document.querySelector('[name="amount"]').value = r.amount;
                    out.textContent = '✅ المبلغ المقروء: ' + r.amount;
                } else {
                    out.textContent = 'مش لاقي مبلغ، اكتبه يدوي.';
                }
            } catch (err) { out.textContent = 'مش قادر يقرا الصورة.'; }
        });
    </script>
    @endpush
</x-layouts.app>
