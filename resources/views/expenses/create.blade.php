<x-layouts.app :title="__('app.add_expense')">
    <x-header-bar :title="__('app.add_expense')" :back="route('dashboard')" />

    <div class="px-5">
        <form id="expense-form" method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data" class="card space-y-4">
            @csrf

            <div class="flex items-center gap-2">
                <button type="button" id="voice-btn" class="btn-ghost text-sm flex items-center gap-2">🎙️ {{ __('app.voice_add') }}</button>
                <label for="receipt-input" class="btn-ghost text-sm flex items-center gap-2 cursor-pointer">📷 {{ __('app.receipt') }}</label>
                <input id="receipt-input" type="file" name="receipt" accept="image/*" capture="environment" class="hidden">
                <button type="button" id="loc-btn" class="btn-ghost text-sm flex items-center gap-2">📍</button>
            </div>
            <p id="voice-out" class="text-xs text-stone-500"></p>
            <p id="loc-out" class="text-xs text-stone-500"></p>

            <div>
                <label class="text-sm font-semibold">{{ __('app.description') }}</label>
                <input type="text" name="description" id="desc" required class="input mt-1" placeholder="مثلاً: غدا مع الشلة">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-sm font-semibold">{{ __('app.amount') }}</label>
                    <input type="number" name="amount" id="amount" min="0.01" step="0.01" required class="input mt-1">
                </div>
                <div>
                    <label class="text-sm font-semibold">{{ __('app.currency') }}</label>
                    <select name="currency" class="input mt-1">
                        @foreach (['EGP','USD','EUR','GBP','SAR','AED'] as $c)
                            <option value="{{ $c }}" @selected($c === 'EGP')>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-sm font-semibold">{{ __('app.date') }}</label>
                    <input type="datetime-local" name="occurred_at" required class="input mt-1" value="{{ now()->format('Y-m-d\TH:i') }}">
                </div>
                <div>
                    <label class="text-sm font-semibold">{{ __('app.category') }}</label>
                    <select name="category_id" class="input mt-1">
                        <option value="">— أوتوماتيك —</option>
                        @foreach ($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->icon }} {{ $c->name() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="text-sm font-semibold">المجموعة</label>
                <select name="group_id" id="group-select" class="input mt-1">
                    <option value="">— مفيش مجموعة (فردي) —</option>
                    @foreach ($groups as $g)
                        <option value="{{ $g->id }}" @selected($selectedGroup?->id === $g->id)>{{ $g->icon }} {{ $g->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm font-semibold">{{ __('app.paid_by') }}</label>
                <select name="payer_id" class="input mt-1">
                    <option value="{{ auth()->id() }}">{{ auth()->user()->name }} (أنا)</option>
                    @foreach ($friends as $f)
                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm font-semibold">طريقة التقسيم</label>
                <div class="grid grid-cols-4 gap-1 mt-2 bg-stone-100 rounded-2xl p-1 text-xs font-semibold">
                    @foreach ([['equal', __('app.split_equally')], ['exact', __('app.split_exact')], ['percent', __('app.split_percent')], ['shares', __('app.split_shares')]] as $opt)
                        <label class="text-center py-2 rounded-xl cursor-pointer">
                            <input type="radio" name="split_type" value="{{ $opt[0] }}" class="peer sr-only" {{ $opt[0] === 'equal' ? 'checked' : '' }}>
                            <span class="block px-1 peer-checked:bg-white peer-checked:shadow-sm rounded-xl py-1">{{ $opt[1] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <p class="text-sm font-semibold">{{ __('app.split_with') }}</p>
                <div id="people-list" class="mt-2 space-y-2">
                    @php $candidates = collect([auth()->user()])->concat($friends); @endphp
                    @foreach ($candidates as $u)
                        <label class="flex items-center gap-3 bg-stone-50 rounded-2xl p-3">
                            <input type="checkbox" data-uid="{{ $u->id }}" class="person-cb w-5 h-5 accent-orange-500" checked>
                            <x-avatar :user="$u" :size="32" />
                            <span class="flex-1 text-sm font-medium">{{ $u->name }}</span>
                            <input type="number" step="0.01" min="0" name="shares[{{ $u->id }}]" value="1" class="share-in input !py-2 !px-3 w-24 text-sm text-end">
                        </label>
                    @endforeach
                </div>
                @error('shares') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <input type="hidden" name="location_lat" id="location_lat">
            <input type="hidden" name="location_lng" id="location_lng">
            <input type="hidden" name="location_name" id="location_name">

            <button class="btn-primary">{{ __('app.save') }}</button>
        </form>
    </div>

    @push('scripts')
    <script>
        // Voice
        document.getElementById('voice-btn').addEventListener('click', async () => {
            const out = document.getElementById('voice-out');
            out.textContent = 'يسمعك الآن…';
            try {
                const locale = '{{ app()->getLocale() }}' === 'ar' ? 'ar-EG' : 'en-US';
                const results = await startVoice(locale);
                const text = results[0];
                out.textContent = text;
                document.getElementById('desc').value = text;
                const m = text.match(/(\d+(?:[.,]\d+)?)/);
                if (m) document.getElementById('amount').value = parseFloat(m[1].replace(',', '.'));
            } catch (e) { out.textContent = 'مش متاح: ' + e.message; }
        });

        // Geolocation
        document.getElementById('loc-btn').addEventListener('click', async () => {
            const out = document.getElementById('loc-out');
            out.textContent = 'بنحدد المكان…';
            try {
                const p = await getLocation();
                document.getElementById('location_lat').value = p.lat;
                document.getElementById('location_lng').value = p.lng;
                document.getElementById('location_name').value = `${p.lat.toFixed(4)}, ${p.lng.toFixed(4)}`;
                out.textContent = `📍 ${p.lat.toFixed(4)}, ${p.lng.toFixed(4)}`;
            } catch (e) { out.textContent = 'تعذر تحديد المكان'; }
        });

        // OCR receipt
        document.getElementById('receipt-input').addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;
            const out = document.getElementById('voice-out');
            out.textContent = '⏳ بنقرا الإيصال…';
            try {
                const r = await ocrReceipt(file);
                if (r.amount) {
                    document.getElementById('amount').value = r.amount;
                    out.textContent = '✅ المبلغ: ' + r.amount;
                } else {
                    out.textContent = 'مش لاقي مبلغ، اكتبه يدوي.';
                }
            } catch (err) { out.textContent = 'مش قادر يقرا الصورة.'; }
        });

        // Toggle person inclusion
        document.querySelectorAll('.person-cb').forEach(cb => {
            const wrap = cb.closest('label');
            const input = wrap.querySelector('.share-in');
            cb.addEventListener('change', () => {
                input.disabled = !cb.checked;
                if (!cb.checked) input.value = '';
                else input.value = input.dataset.def || 1;
            });
        });
    </script>
    @endpush
</x-layouts.app>
