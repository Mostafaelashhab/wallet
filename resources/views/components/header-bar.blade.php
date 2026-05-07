@props(['title' => null, 'back' => null])
<div class="safe-top px-5 pb-3 flex items-center justify-between text-white">
    <div class="flex items-center gap-2">
        @if ($back)
            <a href="{{ $back }}" class="w-9 h-9 rounded-full bg-white/15 grid place-items-center" aria-label="back">
                <svg width="20" height="20" class="flip-rtl" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 6l-6 6 6 6"/></svg>
            </a>
        @endif
        @if ($title)
            <h1 class="text-lg font-semibold tracking-tight">{{ $title }}</h1>
        @else
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-white grid place-items-center">
                    <span class="text-brand-500 font-bold text-sm">S</span>
                </div>
                <span class="font-bold text-lg tracking-tight">Splitty</span>
            </div>
        @endif
    </div>
    <div class="flex items-center gap-3">
        {{ $slot ?? '' }}
        @auth
            <a href="{{ route('notifications') }}" class="w-9 h-9 rounded-full bg-white/15 grid place-items-center relative">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 8a6 6 0 1 1 12 0c0 7 3 7 3 9H3c0-2 3-2 3-9z"/><path d="M10 21a2 2 0 0 0 4 0"/></svg>
            </a>
            <a href="{{ route('profile') }}" aria-label="profile">
                <x-avatar :user="auth()->user()" :size="36" />
            </a>
        @endauth
    </div>
</div>
