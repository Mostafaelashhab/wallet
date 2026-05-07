@php
    $r = request()->route()?->getName() ?? '';
@endphp
<nav class="bottom-nav">
    <div class="bottom-nav-inner">
        <a href="{{ route('dashboard') }}" class="{{ str_starts_with($r, 'dashboard') ? 'active' : '' }}" aria-label="Home">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12 12 4l9 8"/><path d="M5 10v10h14V10"/></svg>
        </a>
        <a href="{{ route('activity') }}" class="{{ str_starts_with($r, 'activity') ? 'active' : '' }}" aria-label="Activity">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12h4l2-6 4 12 2-6h6"/></svg>
        </a>
        <a href="{{ route('expenses.create') }}" class="add-fab" aria-label="Add">
            <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        </a>
        <a href="{{ route('groups.index') }}" class="{{ str_starts_with($r, 'groups') ? 'active' : '' }}" aria-label="Groups">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M3 20c0-3 2.5-5 6-5s6 2 6 5"/><path d="M14 20c0-2 1.5-3.5 4-3.5s4 1.5 4 3.5"/></svg>
        </a>
        <a href="{{ route('profile') }}" class="{{ str_starts_with($r, 'profile') ? 'active' : '' }}" aria-label="Profile">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="9" r="3.5"/><path d="M5 20c0-3.5 3-6 7-6s7 2.5 7 6"/></svg>
        </a>
    </div>
</nav>
