<x-layouts.app :title="__('app.profile')">
    <x-header-bar :title="__('app.profile')" :back="route('dashboard')" />

    <div class="px-5 space-y-3">
        {{-- Avatar card --}}
        <div class="glass flex items-center gap-3 p-4 rounded-2xl">
            <x-avatar :user="$user" :size="64" />
            <div class="flex-1 min-w-0">
                <p class="font-bold text-white truncate">{{ $user->name }}</p>
                <p class="text-xs text-white/55 truncate">{{ $user->email }}</p>
                <p class="text-[11px] text-white/40 mt-1">{{ __('app.profile_member_since', ['date' => $user->created_at->translatedFormat('M Y')]) }}</p>
            </div>
            <form id="avatar-form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <input type="hidden" name="name" value="{{ $user->name }}">
                <input type="hidden" name="email" value="{{ $user->email }}">
                <label class="w-10 h-10 rounded-full bg-white/10 border border-white/10 grid place-items-center cursor-pointer text-white/80">
                    <x-icon name="camera" :size="16" />
                    <input type="file" name="avatar" accept="image/*" class="hidden" onchange="document.getElementById('avatar-form').submit()">
                </label>
            </form>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" class="glass space-y-4 p-5 rounded-2xl">
            @csrf @method('PUT')
            <p class="text-[11px] font-bold text-white/55 uppercase tracking-widest">{{ __('app.profile_info') }}</p>
            <div>
                <label class="text-xs font-semibold text-white/70">{{ __('app.name') }}</label>
                <input name="name" value="{{ $user->name }}" class="input mt-1" required>
            </div>
            <div>
                <label class="text-xs font-semibold text-white/70">{{ __('app.email') }}</label>
                <input type="email" name="email" value="{{ $user->email }}" class="input mt-1" required>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-semibold text-white/70">{{ __('app.phone') }}</label>
                    <input name="phone" value="{{ $user->phone }}" class="input mt-1">
                </div>
                <div>
                    <label class="text-xs font-semibold text-white/70">{{ __('app.locale') }}</label>
                    <select name="locale" class="input mt-1">
                        <option value="ar" @selected($user->locale === 'ar')>{{ __('app.locale_ar') }}</option>
                        <option value="en" @selected($user->locale === 'en')>{{ __('app.locale_en') }}</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-semibold text-white/70">{{ __('app.currency') }}</label>
                    <select name="currency" class="input mt-1">
                        @foreach (['EGP','USD','EUR','GBP','SAR','AED'] as $c)
                            <option value="{{ $c }}" @selected($user->currency === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-white/70">{{ __('app.profile_color') }}</label>
                    <input type="color" name="color" value="{{ $user->color }}" class="input mt-1 h-12">
                </div>
            </div>
            <button class="btn-primary">{{ __('app.save') }}</button>
        </form>

        <form method="POST" action="{{ route('profile.update') }}" class="glass space-y-3 p-5 rounded-2xl">
            @csrf @method('PUT')
            <input type="hidden" name="name" value="{{ $user->name }}">
            <input type="hidden" name="email" value="{{ $user->email }}">
            <p class="text-[11px] font-bold text-white/55 uppercase tracking-widest">{{ __('app.profile_security') }}</p>
            <div>
                <label class="text-xs font-semibold text-white/70">{{ __('app.password_new') }}</label>
                <input type="password" name="password" class="input mt-1" placeholder="{{ __('app.password_min_hint') }}">
            </div>
            <div>
                <label class="text-xs font-semibold text-white/70">{{ __('app.password_confirm') }}</label>
                <input type="password" name="password_confirmation" class="input mt-1">
            </div>
            <button class="btn-ghost w-full">{{ __('app.update_password') }}</button>
        </form>

        <div class="glass p-4 rounded-2xl">
            <button type="button" onclick="enablePushFromBtn()" class="btn-ghost w-full flex items-center justify-center gap-2">
                <x-icon name="bell" :size="16" /> {{ __('app.notif_enable') }}
            </button>
            <p id="push-status" class="text-xs text-white/55 mt-2 text-center"></p>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full py-3 rounded-2xl glass-soft text-white font-semibold flex items-center justify-center gap-2">
                <x-icon name="logout" :size="18" /> {{ __('app.logout') }}
            </button>
        </form>
    </div>

    @push('scripts')
    <script>
        async function enablePushFromBtn() {
            const out = document.getElementById('push-status');
            if (!('Notification' in window)) { out.textContent = '{{ __('app.notif_unsupported') }}'; return; }
            try {
                const perm = await Notification.requestPermission();
                if (perm !== 'granted') { out.textContent = '{{ __('app.notif_perm_required') }}'; return; }
                if ('serviceWorker' in navigator) {
                    const reg = await navigator.serviceWorker.ready;
                    reg.showNotification('Splitty ✓', { body: '{{ __('app.notif_enabled') }}', icon: '/icons/icon-192.svg' });
                }
                out.textContent = '✓ {{ __('app.notif_enabled') }}';
            } catch (e) { out.textContent = e.message; }
        }
    </script>
    @endpush
</x-layouts.app>
