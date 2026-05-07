<x-layouts.app title="مصروف مع الشلة">
    <x-header-bar title="مصروف مع الشلة" :back="route('quick-add')" />

    <div class="px-5">
        <form id="expense-form" method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data" class="card space-y-4">
            @csrf
            <input type="hidden" name="currency" value="EGP">

            {{-- Amount hero --}}
            <div class="text-center pb-3 border-b border-stone-100">
                <p class="text-xs text-stone-500">المبلغ</p>
                <div class="flex items-center justify-center gap-2 mt-1">
                    <input type="number" name="amount" id="amount" min="0.01" step="0.01" required inputmode="decimal"
                           class="text-4xl font-extrabold text-center w-44 bg-transparent outline-none placeholder:text-stone-300"
                           placeholder="0.00">
                    <span class="text-sm font-semibold text-stone-400">جنيه</span>
                </div>
            </div>

            {{-- Quick action chips --}}
            <div class="flex items-center gap-2 justify-center flex-wrap">
                <button type="button" id="voice-btn" class="chip text-xs flex items-center gap-1.5">
                    <x-icon name="mic" :size="14" /> صوت
                </button>
                <label for="receipt-input" class="chip text-xs flex items-center gap-1.5 cursor-pointer">
                    <x-icon name="camera" :size="14" /> إيصال
                </label>
                <input id="receipt-input" type="file" name="receipt" accept="image/*" capture="environment" class="hidden">
                <button type="button" id="loc-btn" class="chip text-xs flex items-center gap-1.5">
                    <x-icon name="pin" :size="14" /> مكان
                </button>
            </div>
            <p id="action-out" class="text-xs text-stone-500 text-center -mt-2"></p>

            <div>
                <label class="text-xs font-bold text-stone-600">إيه المصروف؟</label>
                <input type="text" name="description" id="desc" required class="input mt-1" placeholder="مثلاً: غدا مع الشلة">
                @error('description') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-stone-600">التاريخ</label>
                    <input type="datetime-local" name="occurred_at" required class="input mt-1" value="{{ now()->format('Y-m-d\TH:i') }}">
                </div>
                <div>
                    <label class="text-xs font-bold text-stone-600">الفئة</label>
                    <select name="category_id" class="input mt-1">
                        <option value="">— تلقائي —</option>
                        @foreach ($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-stone-600">المجموعة</label>
                <select name="group_id" id="group-select" class="input mt-1">
                    <option value="">— من غير مجموعة (فردي) —</option>
                    @foreach ($groups as $g)
                        <option value="{{ $g->id }}" @selected($selectedGroup?->id === $g->id)>{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs font-bold text-stone-600">مين دفع؟</label>
                <select name="payer_id" class="input mt-1">
                    <option value="{{ auth()->id() }}">{{ auth()->user()->name }} (أنا)</option>
                    @foreach ($friends as $f)
                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs font-bold text-stone-600">طريقة التقسيم</label>
                <div class="grid grid-cols-4 gap-1 mt-1 bg-stone-100 rounded-2xl p-1 text-xs font-semibold">
                    @foreach ([['equal', 'بالتساوي'], ['exact', 'مبالغ محددة'], ['percent', 'نسب %'], ['shares', 'حصص']] as $i => $opt)
                        <label class="text-center cursor-pointer rounded-xl py-2 has-[:checked]:bg-white has-[:checked]:shadow-sm has-[:checked]:text-stone-900 text-stone-500">
                            <input type="radio" name="split_type" value="{{ $opt[0] }}" class="sr-only" {{ $i === 0 ? 'checked' : '' }}>
                            <span class="block px-1">{{ $opt[1] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <p class="text-xs font-bold text-stone-600 mb-2">قسّم على</p>
                <div id="people-list" class="space-y-2">
                    @php $candidates = collect([auth()->user()])->concat($friends); @endphp
                    @foreach ($candidates as $u)
                        <label class="flex items-center gap-3 bg-stone-50 rounded-2xl p-3 has-[:checked]:bg-brand-50 has-[:checked]:ring-2 has-[:checked]:ring-brand-300">
                            <input type="checkbox" data-uid="{{ $u->id }}" class="person-cb w-5 h-5 accent-orange-500" checked>
                            <x-avatar :user="$u" :size="34" />
                            <span class="flex-1 text-sm font-semibold truncate">{{ $u->name }} @if ($u->id === auth()->id()) <span class="text-stone-400 text-xs">(أنا)</span> @endif</span>
                            <input type="number" step="0.01" min="0" name="shares[{{ $u->id }}]" value="1" data-def="1"
                                   class="share-in input !py-2 !px-3 w-24 text-sm text-end">
                        </label>
                    @endforeach
                </div>
                @error('shares') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <input type="hidden" name="location_lat" id="location_lat">
            <input type="hidden" name="location_lng" id="location_lng">
            <input type="hidden" name="location_name" id="location_name">

            <button class="btn-primary">حفظ</button>
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
                if (m) document.getElementById('amount').value = parseFloat(m[1].replace(',', '.'));
            } catch (e) { out.textContent = 'مش متاح: ' + e.message; }
        });

        document.getElementById('loc-btn').addEventListener('click', async () => {
            out.textContent = 'بنحدد المكان…';
            try {
                const p = await getLocation();
                document.getElementById('location_lat').value = p.lat;
                document.getElementById('location_lng').value = p.lng;
                document.getElementById('location_name').value = `${p.lat.toFixed(4)}, ${p.lng.toFixed(4)}`;
                out.textContent = `تم التحديد: ${p.lat.toFixed(4)}, ${p.lng.toFixed(4)}`;
            } catch (e) { out.textContent = 'تعذر تحديد المكان'; }
        });

        document.getElementById('receipt-input').addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;
            out.textContent = 'بنقرا الإيصال…';
            try {
                const r = await ocrReceipt(file);
                if (r.amount) {
                    document.getElementById('amount').value = r.amount;
                    out.textContent = 'المبلغ المقروء: ' + r.amount;
                } else {
                    out.textContent = 'مش لاقي مبلغ، اكتبه يدوي.';
                }
            } catch (err) { out.textContent = 'مش قادر يقرا الصورة.'; }
        });

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
