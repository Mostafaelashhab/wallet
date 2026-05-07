@props(['title' => null, 'back' => null])
<div class="safe-top px-5 pb-3 flex items-center justify-between text-white">
    <div class="flex items-center gap-2">
        @if ($back)
            <a href="{{ $back }}" class="w-9 h-9 rounded-full bg-white/15 grid place-items-center backdrop-blur" aria-label="back">
                <x-icon name="arrow-back" :size="18" :stroke="2.5" class="flip-rtl" />
            </a>
        @endif
        @if ($title)
            <h1 class="text-lg font-bold tracking-tight">{{ $title }}</h1>
        @else
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-white grid place-items-center">
                    <span class="text-brand-500 font-extrabold">S</span>
                </div>
                <span class="font-extrabold text-lg tracking-tight">Splitty</span>
            </div>
        @endif
    </div>
    <div class="flex items-center gap-2">
        {{ $slot ?? '' }}
    </div>
</div>
