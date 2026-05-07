<x-layouts.app :title="__('app.login')">
    <div class="safe-top px-5 pt-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-white">
                <div class="w-9 h-9 rounded-full bg-white grid place-items-center">
                    <span class="text-ink-950 font-extrabold">S</span>
                </div>
                <span class="font-extrabold text-xl tracking-tight">Splitty</span>
            </div>
            <div class="text-xs flex items-center gap-2">
                <a href="/locale/ar?redirect=/login" class="px-3 py-1.5 rounded-full bg-white/10 border border-white/10 text-white">عربي</a>
                <a href="/locale/en?redirect=/login" class="px-3 py-1.5 rounded-full bg-white/10 border border-white/10 text-white">EN</a>
            </div>
        </div>

        <div class="mt-12 text-white">
            <h1 class="text-3xl font-extrabold leading-tight tracking-tight">{{ __('app.app_tagline') }}</h1>
            <p class="opacity-60 mt-2 text-sm leading-6">{{ app()->getLocale() === 'ar' ? 'تتبع كل مصروفاتك ودخلك في مكان واحد. بسيط، سريع، وكل البيانات معاك.' : 'Track every expense and income in one place. Simple, fast, all your data with you.' }}</p>
        </div>
    </div>

    <div class="px-5 mt-8">
        <div class="glass p-6 rounded-3xl">
            <h2 class="text-lg font-bold mb-1 text-white">{{ __('app.login') }}</h2>
            <p class="text-sm text-white/55 mb-4">{{ __('app.login_subtitle') }}</p>
            <form method="POST" action="{{ route('login') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="text-xs font-semibold text-white/70">{{ __('app.email') }}</label>
                    <input type="email" name="email" required value="{{ old('email') }}" class="input mt-1" autofocus>
                    @error('email') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold text-white/70">{{ __('app.password') }}</label>
                    <input type="password" name="password" required class="input mt-1">
                </div>
                <label class="flex items-center gap-2 text-sm text-white/70">
                    <input type="checkbox" name="remember" value="1" class="w-4 h-4 accent-indigo-500">
                    {{ __('app.remember_me') }}
                </label>
                <button class="btn-primary mt-2">{{ __('app.continue') }}</button>
            </form>
            <p class="text-center mt-4 text-sm text-white/55">
                {{ __('app.no_account') }}
                <a href="{{ route('register') }}" class="text-indigo-300 font-semibold">{{ __('app.register') }}</a>
            </p>
        </div>

        <div class="grid grid-cols-3 gap-2 mt-5">
            <div class="glass-soft !p-3 text-center rounded-2xl">
                <div class="w-9 h-9 mx-auto rounded-2xl grid place-items-center bg-indigo-500/20 text-indigo-200 border border-indigo-400/20"><x-icon name="mic" :size="18" /></div>
                <p class="text-[11px] mt-2 font-semibold text-white/80 leading-tight">{{ app()->getLocale() === 'ar' ? 'سجّل بصوتك' : 'Voice entry' }}</p>
            </div>
            <div class="glass-soft !p-3 text-center rounded-2xl">
                <div class="w-9 h-9 mx-auto rounded-2xl grid place-items-center bg-sky-500/20 text-sky-200 border border-sky-400/20"><x-icon name="camera" :size="18" /></div>
                <p class="text-[11px] mt-2 font-semibold text-white/80 leading-tight">{{ app()->getLocale() === 'ar' ? 'إيصال OCR' : 'Receipt OCR' }}</p>
            </div>
            <div class="glass-soft !p-3 text-center rounded-2xl">
                <div class="w-9 h-9 mx-auto rounded-2xl grid place-items-center bg-emerald-500/20 text-emerald-200 border border-emerald-400/20"><x-icon name="chart" :size="18" /></div>
                <p class="text-[11px] mt-2 font-semibold text-white/80 leading-tight">{{ app()->getLocale() === 'ar' ? 'تقارير ذكية' : 'Smart reports' }}</p>
            </div>
        </div>
    </div>
</x-layouts.app>
