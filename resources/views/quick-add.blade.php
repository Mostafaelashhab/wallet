<x-layouts.app :title="__('app.quick_add')">
    <x-header-bar :title="__('app.quick_add')" :back="route('dashboard')" />

    <div class="px-5">
        <p class="text-white/55 text-sm mb-4">{{ __('app.quick_add_subtitle') }}</p>

        <a href="{{ route('transactions.create') }}?type=expense" class="glass mb-3 flex items-center gap-4 tap-anim p-4 rounded-2xl block">
            <div class="w-14 h-14 rounded-2xl grid place-items-center bg-rose-500/20 border border-rose-400/20 text-rose-300 shrink-0">
                <x-icon name="arrow-up" :size="26" />
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-white">{{ __('app.tx_log_expense') }}</p>
                <p class="text-xs text-white/55">{{ __('app.tx_subtitle_expense') }}</p>
            </div>
            <x-icon name="chevron-right" :size="20" class="text-white/40 flip-rtl" />
        </a>

        <a href="{{ route('transactions.create') }}?type=income" class="glass mb-3 flex items-center gap-4 tap-anim p-4 rounded-2xl block">
            <div class="w-14 h-14 rounded-2xl grid place-items-center bg-emerald-500/20 border border-emerald-400/20 text-emerald-300 shrink-0">
                <x-icon name="arrow-down" :size="26" />
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-white">{{ __('app.tx_log_income') }}</p>
                <p class="text-xs text-white/55">{{ __('app.tx_subtitle_income') }}</p>
            </div>
            <x-icon name="chevron-right" :size="20" class="text-white/40 flip-rtl" />
        </a>

        <a href="{{ route('transactions.create') }}?type=transfer" class="glass flex items-center gap-4 tap-anim p-4 rounded-2xl block">
            <div class="w-14 h-14 rounded-2xl grid place-items-center bg-sky-500/20 border border-sky-400/20 text-sky-300 shrink-0">
                <x-icon name="transfer" :size="26" />
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-white">{{ __('app.tx_log_transfer') }}</p>
                <p class="text-xs text-white/55">{{ __('app.tx_subtitle_transfer') }}</p>
            </div>
            <x-icon name="chevron-right" :size="20" class="text-white/40 flip-rtl" />
        </a>
    </div>
</x-layouts.app>
