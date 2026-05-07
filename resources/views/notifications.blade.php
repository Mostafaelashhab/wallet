<x-layouts.app title="الإشعارات">
    <x-header-bar title="الإشعارات" :back="route('dashboard')" />
    <div class="px-5">
        <div class="card space-y-3">
            @forelse ($items as $i)
                <a href="{{ $i->url ?: '#' }}" class="block">
                    <p class="font-semibold text-sm">{{ $i->title }}</p>
                    <p class="text-xs text-stone-600">{{ $i->body }}</p>
                    <p class="text-xs text-stone-400 mt-1">{{ $i->created_at->diffForHumans() }}</p>
                </a>
            @empty
                <p class="text-stone-500 text-sm text-center">مفيش إشعارات.</p>
            @endforelse
        </div>
    </div>
</x-layouts.app>
