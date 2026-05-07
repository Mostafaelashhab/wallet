<x-layouts.app :title="__('app.notifications')">
    <x-header-bar :title="__('app.notifications')" :back="route('dashboard')" />
    <div class="px-5">
        <div class="glass space-y-3 p-4 rounded-2xl">
            @forelse ($items as $i)
                <a href="{{ $i->url ?: '#' }}" class="block">
                    <p class="font-semibold text-sm text-white">{{ $i->title }}</p>
                    <p class="text-xs text-white/60">{{ $i->body }}</p>
                    <p class="text-[11px] text-white/40 mt-1">{{ $i->created_at->diffForHumans() }}</p>
                </a>
            @empty
                <div class="text-center py-6">
                    <div class="w-12 h-12 mx-auto rounded-full bg-white/10 grid place-items-center text-white/60 border border-white/10">
                        <x-icon name="bell" :size="22" />
                    </div>
                    <p class="text-white/55 text-sm mt-3">{{ __('app.notifications_empty') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.app>
