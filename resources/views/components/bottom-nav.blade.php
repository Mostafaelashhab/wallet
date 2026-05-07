@php
    $r = request()->route()?->getName() ?? '';
@endphp
<nav class="bottom-nav">
    <div class="bottom-nav-inner">
        <a href="{{ route('dashboard') }}" class="{{ str_starts_with($r, 'dashboard') ? 'active' : '' }}" aria-label="Home">
            <x-icon name="home" :size="22" />
        </a>
        <a href="{{ route('accounts.index') }}" class="{{ str_starts_with($r, 'accounts') ? 'active' : '' }}" aria-label="Wallets">
            <x-icon name="wallet" :size="22" />
        </a>
        <a href="{{ route('transactions.create') }}?type=expense" class="add-fab" aria-label="Add">
            <x-icon name="plus" :size="26" :stroke="2.6" />
        </a>
        <a href="{{ route('reports.index') }}" class="{{ str_starts_with($r, 'reports') ? 'active' : '' }}" aria-label="Reports">
            <x-icon name="chart" :size="22" />
        </a>
        <a href="{{ route('profile') }}" class="{{ str_starts_with($r, 'profile') ? 'active' : '' }}" aria-label="Profile">
            <x-icon name="user" :size="22" />
        </a>
    </div>
</nav>
