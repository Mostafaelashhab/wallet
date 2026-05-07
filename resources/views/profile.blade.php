<x-layouts.app :title="__('app.profile')">
    <x-header-bar :title="__('app.profile')" :back="route('dashboard')" />

    <div class="px-5 space-y-4">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="card space-y-4">
            @csrf @method('PUT')
            <div class="flex items-center gap-3">
                <x-avatar :user="$user" :size="64" />
                <div class="flex-1">
                    <p class="font-semibold">{{ $user->name }}</p>
                    <p class="text-xs text-stone-500">{{ $user->email }}</p>
                </div>
                <label class="btn-ghost text-xs cursor-pointer">
                    📷
                    <input type="file" name="avatar" accept="image/*" class="hidden">
                </label>
            </div>

            <div>
                <label class="text-sm font-semibold">{{ __('app.name') }}</label>
                <input name="name" value="{{ $user->name }}" class="input mt-1" required>
            </div>
            <div>
                <label class="text-sm font-semibold">{{ __('app.email') }}</label>
                <input type="email" name="email" value="{{ $user->email }}" class="input mt-1" required>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-sm font-semibold">{{ __('app.phone') }}</label>
                    <input name="phone" value="{{ $user->phone }}" class="input mt-1">
                </div>
                <div>
                    <label class="text-sm font-semibold">{{ __('app.locale') }}</label>
                    <select name="locale" class="input mt-1">
                        <option value="ar" @selected($user->locale === 'ar')>عربي</option>
                        <option value="en" @selected($user->locale === 'en')>English</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-sm font-semibold">{{ __('app.currency') }}</label>
                    <select name="currency" class="input mt-1">
                        @foreach (['EGP','USD','EUR','GBP','SAR','AED'] as $c)
                            <option value="{{ $c }}" @selected($user->currency === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-semibold">لوني</label>
                    <input type="color" name="color" value="{{ $user->color }}" class="input mt-1 h-12">
                </div>
            </div>
            <div>
                <label class="text-sm font-semibold">{{ __('app.instapay') }}</label>
                <input name="instapay_handle" value="{{ $user->instapay_handle }}" class="input mt-1" placeholder="user@instapay">
            </div>
            <div>
                <label class="text-sm font-semibold">{{ __('app.vodafone_cash') }}</label>
                <input name="vodafone_cash" value="{{ $user->vodafone_cash }}" class="input mt-1" placeholder="010X XXX XXXX">
            </div>
            <div>
                <label class="text-sm font-semibold">كلمة سر جديدة (اختياري)</label>
                <input type="password" name="password" class="input mt-1">
                <input type="password" name="password_confirmation" placeholder="تأكيد" class="input mt-2">
            </div>
            <button class="btn-primary">{{ __('app.save') }}</button>
        </form>

        <div class="card">
            <button onclick="enablePushFromBtn()" class="btn-ghost w-full">{{ __('app.enable_push') }}</button>
            <p id="push-status" class="text-xs text-stone-500 mt-2"></p>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full py-3 rounded-2xl bg-stone-900 text-white font-semibold">{{ __('app.logout') }}</button>
        </form>
    </div>

    @push('scripts')
    <script>
        async function enablePushFromBtn() {
            const out = document.getElementById('push-status');
            try {
                // For local dev we use a placeholder VAPID key — server will accept any subscription
                await enablePush('BCmjnqyjBOwwj2c0uAQHBNUjfMGfV7cOHkyZv5qN8GqQ8oR9rKvQK7G4u7q8tUqL5pZ3dYy7xAMXX2ZrJ8mQbDk');
                out.textContent = '✅ مفعّل';
            } catch (e) { out.textContent = '⚠️ ' + e.message; }
        }
    </script>
    @endpush
</x-layouts.app>
