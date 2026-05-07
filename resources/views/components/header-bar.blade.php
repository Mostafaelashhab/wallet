@props(['title' => null, 'back' => null])
<div class="app-header safe-top px-5 pb-3 flex items-center justify-between">
    <div class="flex items-center gap-2 min-w-0">
        @if ($back)
            <a href="{{ $back }}" class="w-9 h-9 rounded-full glass-soft grid place-items-center text-white shrink-0" aria-label="back">
                <x-icon name="arrow-back" :size="18" :stroke="2.2" class="flip-rtl" />
            </a>
        @endif
        @if ($title)
            <h1 class="text-base font-semibold tracking-tight text-white truncate">{{ $title }}</h1>
        @else
            <div class="flex items-center gap-2 text-white">
                <div class="w-8 h-8 rounded-full bg-white grid place-items-center">
                    <span class="text-ink-950 font-bold">S</span>
                </div>
                <span class="font-bold text-base tracking-tight">Splitty</span>
            </div>
        @endif
    </div>
    <div class="flex items-center gap-2 shrink-0">
        {{ $slot ?? '' }}
    </div>
</div>
