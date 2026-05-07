<x-layouts.app :title="__('app.register')">
    <x-header-bar :back="route('login')" />

    <div class="px-5 mt-2">
        <div class="text-white">
            <h2 class="text-2xl font-extrabold leading-tight tracking-tight">{{ __('app.register') }}</h2>
            <p class="text-white/55 mt-1 text-sm">{{ __('app.register_subtitle') }}</p>
        </div>

        <div class="glass mt-6 p-6 rounded-3xl">
            <form method="POST" action="{{ route('register') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="text-xs font-semibold text-white/70">{{ __('app.name') }}</label>
                    <input type="text" name="name" required value="{{ old('name') }}" class="input mt-1">
                    @error('name') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold text-white/70">{{ __('app.email') }}</label>
                    <input type="email" name="email" required value="{{ old('email') }}" class="input mt-1">
                    @error('email') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold text-white/70">{{ __('app.phone') }}</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" class="input mt-1" placeholder="+20 1XX XXX XXXX">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-semibold text-white/70">{{ __('app.password') }}</label>
                        <input type="password" name="password" required minlength="6" class="input mt-1">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-white/70">{{ __('app.password_confirm') }}</label>
                        <input type="password" name="password_confirmation" required minlength="6" class="input mt-1">
                    </div>
                </div>
                <button class="btn-primary mt-2">{{ __('app.continue') }}</button>
            </form>
            <p class="text-center mt-4 text-sm text-white/55">
                {{ __('app.have_account') }}
                <a href="{{ route('login') }}" class="text-indigo-300 font-semibold">{{ __('app.login') }}</a>
            </p>
        </div>
    </div>
</x-layouts.app>
