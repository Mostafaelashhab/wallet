<x-layouts.app :title="__('app.login')" hero="tall">
    <div class="safe-top px-5 pt-3 text-white">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-full bg-white grid place-items-center">
                    <span class="text-brand-500 font-extrabold">S</span>
                </div>
                <span class="font-extrabold text-xl tracking-tight">Splitty</span>
            </div>
            <div class="text-xs space-x-2">
                <button onclick="toggleLocale('ar')" class="px-3 py-1.5 rounded-full bg-white/15 backdrop-blur">عربي</button>
                <button onclick="toggleLocale('en')" class="px-3 py-1.5 rounded-full bg-white/15 backdrop-blur">EN</button>
            </div>
        </div>

        <div class="mt-8 text-white">
            <h1 class="text-3xl font-extrabold leading-tight">قسّم الفواتير،<br>وانسى الحسابات.</h1>
            <p class="opacity-85 mt-2 text-sm leading-6">رحلات، عشا، إيجار، اشتراكات — Splitty بيحسبها كلها ويقول مين عليه كام بأقل عدد تحويلات.</p>
        </div>

        {{-- Floating preview cards (visual interest) --}}
        <div class="relative mt-6 h-16">
            <div class="absolute inset-x-2 top-0 bg-white/20 backdrop-blur rounded-2xl p-3 flex items-center gap-3 -rotate-2">
                <div class="w-9 h-9 rounded-xl bg-emerald-200 grid place-items-center">✅</div>
                <div class="flex-1 text-xs">
                    <p class="font-semibold">سددت لـ Wade</p>
                    <p class="opacity-70">2,501.32 EGP عبر InstaPay</p>
                </div>
            </div>
        </div>
    </div>

    <div class="px-5 mt-6">
        <div class="card p-6 rounded-3xl">
            <h2 class="text-lg font-extrabold mb-1">{{ __('app.login') }}</h2>
            <p class="text-sm text-stone-500 mb-4">سجّل دخول وكمّل الحسابات مع صحابك.</p>
            <form method="POST" action="{{ route('login') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="text-xs font-bold text-stone-600">{{ __('app.email') }}</label>
                    <input type="email" name="email" required value="{{ old('email', 'devuser1@esystematic.org') }}" class="input mt-1" autofocus>
                    @error('email') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-bold text-stone-600">{{ __('app.password') }}</label>
                    <input type="password" name="password" required class="input mt-1" placeholder="password">
                </div>
                <label class="flex items-center gap-2 text-sm text-stone-700">
                    <input type="checkbox" name="remember" value="1" class="w-4 h-4 accent-orange-500">
                    {{ __('app.remember_me') }}
                </label>
                <button class="btn-primary mt-2">{{ __('app.continue') }}</button>
            </form>
            <p class="text-center mt-4 text-sm text-stone-600">
                {{ __('app.no_account') }}
                <a href="{{ route('register') }}" class="text-brand-600 font-semibold">{{ __('app.register') }}</a>
            </p>
        </div>

        {{-- Feature highlight strip --}}
        <div class="grid grid-cols-3 gap-2 mt-5">
            <div class="card !p-3 text-center">
                <div class="text-2xl">🎙️</div>
                <p class="text-[11px] mt-1 font-semibold leading-tight">سجّل المصروف بصوتك</p>
            </div>
            <div class="card !p-3 text-center">
                <div class="text-2xl">📷</div>
                <p class="text-[11px] mt-1 font-semibold leading-tight">صوّر الإيصال يقرأه</p>
            </div>
            <div class="card !p-3 text-center">
                <div class="text-2xl">⚡</div>
                <p class="text-[11px] mt-1 font-semibold leading-tight">تبسيط الديون تلقائي</p>
            </div>
        </div>

        <p class="text-center text-stone-400 text-[11px] mt-5">جربه الآن: <code class="text-stone-600">devuser1@esystematic.org / password</code></p>
    </div>
</x-layouts.app>
