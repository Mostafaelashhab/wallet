@props(['name', 'size' => 24, 'stroke' => 2])
@php
    $sw = $stroke;
    $svgs = [
        // Navigation
        'home'         => '<path d="M3 12 12 4l9 8"/><path d="M5 10v10h14V10"/>',
        'activity'     => '<path d="M3 12h4l2-6 4 12 2-6h6"/>',
        'plus'         => '<path d="M12 5v14M5 12h14"/>',
        'wallet'       => '<rect x="3" y="6" width="18" height="14" rx="3"/><path d="M3 10h18"/><circle cx="17" cy="14" r="1.4" fill="currentColor"/>',
        'chart'        => '<path d="M4 20V8M10 20v-6M16 20V4M22 20H2"/>',
        'user'         => '<circle cx="12" cy="9" r="3.5"/><path d="M5 20c0-3.5 3-6 7-6s7 2.5 7 6"/>',
        'users'        => '<circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M3 20c0-3 2.5-5 6-5s6 2 6 5"/><path d="M14 20c0-2 1.5-3.5 4-3.5s4 1.5 4 3.5"/>',
        'bell'         => '<path d="M6 8a6 6 0 1 1 12 0c0 7 3 7 3 9H3c0-2 3-2 3-9z"/><path d="M10 21a2 2 0 0 0 4 0"/>',
        'cog'          => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/>',
        'chevron-right'=> '<path d="M9 6l6 6-6 6"/>',
        'chevron-left' => '<path d="M15 6l-6 6 6 6"/>',
        'arrow-back'   => '<path d="M15 6l-6 6 6 6"/>',
        'search'       => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
        'pencil'       => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 1 1 3 3L7 19l-4 1 1-4z"/>',
        'trash'        => '<path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6 18 20a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>',
        'check'        => '<path d="M5 12l5 5 9-11"/>',
        'x'            => '<path d="M6 6l12 12M6 18 18 6"/>',
        'logout'       => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m17 16 5-4-5-4"/><path d="M22 12H10"/>',

        // Tx types
        'arrow-down'   => '<path d="M12 5v14"/><path d="m18 13-6 6-6-6"/>',
        'arrow-up'     => '<path d="M12 19V5"/><path d="m6 11 6-6 6 6"/>',
        'arrow-swap'   => '<path d="m17 3 4 4-4 4"/><path d="M21 7H7"/><path d="m7 21-4-4 4-4"/><path d="M3 17h14"/>',
        'income'       => '<circle cx="12" cy="12" r="9"/><path d="M12 7v10"/><path d="m8 13 4 4 4-4"/>',
        'expense'      => '<circle cx="12" cy="12" r="9"/><path d="M12 17V7"/><path d="m8 11 4-4 4 4"/>',
        'transfer'     => '<path d="m17 3 4 4-4 4"/><path d="M21 7H7"/><path d="m7 21-4-4 4-4"/><path d="M3 17h14"/>',

        // Account types
        'cash'         => '<rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2.5"/><path d="M6 10v.01M18 14v.01"/>',
        'bank'         => '<path d="M3 21h18"/><path d="M3 10h18"/><path d="m12 3 9 4H3z"/><path d="M5 10v9M9 10v9M15 10v9M19 10v9"/>',
        'mobile'       => '<rect x="6" y="2" width="12" height="20" rx="3"/><path d="M11 18h2"/>',
        'card'         => '<rect x="2" y="5" width="20" height="14" rx="3"/><path d="M2 10h20"/><path d="M6 15h4"/>',
        'piggy'        => '<path d="M21 13a4 4 0 0 0-4-4h-1V7a2 2 0 0 0-3-1.7c-1 .3-2.6 1.5-3 3.7-2 .3-3.7 1.7-4 4H4a2 2 0 0 0 0 4h1c.3 1 .8 2 1.5 2.6V21h3v-1h4v1h3v-1.4c1.5-1 2.5-2.7 2.5-4.6 1 0 2-.5 2-2z"/><circle cx="17" cy="13" r="0.6" fill="currentColor"/>',

        // Categories
        'food'         => '<path d="M5 2v9c0 1 .5 1.5 1.5 1.5h1V22"/><path d="M11 2v22"/><path d="M16 2c-2 2-2 7 0 9v11"/>',
        'groceries'    => '<circle cx="9" cy="20" r="1.6"/><circle cx="18" cy="20" r="1.6"/><path d="M2 3h3l3 13h11l3-9H6"/>',
        'transport'    => '<rect x="4" y="6" width="16" height="11" rx="2"/><path d="M6 17v2M18 17v2"/><circle cx="8" cy="14" r="1.2" fill="currentColor"/><circle cx="16" cy="14" r="1.2" fill="currentColor"/><path d="M4 11h16"/>',
        'bills'        => '<rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 8h6M9 12h6M9 16h4"/>',
        'rent'         => '<path d="m3 11 9-7 9 7"/><path d="M5 10v10h14V10"/><path d="M10 20v-6h4v6"/>',
        'entertainment'=> '<rect x="3" y="6" width="18" height="14" rx="2"/><path d="M3 10h18"/><path d="m7 6 2-2M11 6l2-2M15 6l2-2"/>',
        'travel'       => '<path d="M2 12h20"/><path d="M11 6l-3 3-1-3 1-1z"/><path d="m13 4 8 8h-3l-3 5-2-1 1-4-3-3z"/>',
        'shopping'     => '<path d="M6 8h12l-1 12H7z"/><path d="M9 8a3 3 0 0 1 6 0"/>',
        'health'       => '<path d="M3 12h3l3-7 3 14 3-7h6"/>',
        'gifts'        => '<rect x="3" y="9" width="18" height="11" rx="1.5"/><path d="M12 9v11"/><path d="M3 13h18"/><path d="M7 9c0-2 2-3 3-1.5L12 9 14 7.5C15 6 17 7 17 9"/>',
        'other'        => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M3 12h18"/><path d="M9 7V4h6v3"/>',

        // Group icons (replacements for emoji set)
        'party'        => '<path d="M5 19 12 5l7 14z"/><path d="m9 14 6-2"/>',
        'cake'         => '<path d="M3 18v-6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v6"/><path d="M3 14c1.5 1.5 3 1.5 4.5 0s3 0 4.5 0 3-1.5 4.5 0 3 1.5 4.5 0"/><path d="M3 18h18v3H3z"/><path d="M7 8V5M12 8V5M17 8V5"/>',
        'plane'        => '<path d="M2 12h20"/><path d="M11 6l-3 3-1-3 1-1z"/><path d="m13 4 8 8h-3l-3 5-2-1 1-4-3-3z"/>',
        'pizza'        => '<path d="M12 2 2 22h20z"/><circle cx="9" cy="14" r="1" fill="currentColor"/><circle cx="14" cy="11" r="1" fill="currentColor"/><circle cx="13" cy="17" r="1" fill="currentColor"/>',
        'ball'         => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3v18"/><path d="M5.6 5.6 18.4 18.4"/><path d="m18.4 5.6-12.8 12.8"/>',
        'ring'         => '<circle cx="12" cy="14" r="6"/><path d="m9 8 1.5-3h3L15 8"/>',
        'graduation'   => '<path d="m22 10-10-5L2 10l10 5z"/><path d="M6 12v5c0 1.5 3 3 6 3s6-1.5 6-3v-5"/>',
        'gamepad'      => '<rect x="2" y="7" width="20" height="11" rx="3"/><circle cx="8" cy="13" r="1.4" fill="currentColor"/><path d="M14 11h4M16 9v4"/>',
        'target'       => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.4" fill="currentColor"/>',
        'beach'        => '<circle cx="14" cy="6" r="2"/><path d="M2 22c2-2 5-2 7 0M14 22c2-2 5-2 7 0M2 22h20"/><path d="M14 8c-2 4-6 8-10 12"/>',
        'crown'        => '<path d="m3 18 3-10 4 5 2-7 2 7 4-5 3 10z"/><path d="M3 21h18"/>',

        // Misc
        'mic'          => '<rect x="9" y="3" width="6" height="12" rx="3"/><path d="M5 11a7 7 0 0 0 14 0"/><path d="M12 18v3"/>',
        'camera'       => '<path d="M5 7h3l2-3h4l2 3h3a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2z"/><circle cx="12" cy="13" r="4"/>',
        'pin'          => '<path d="M12 22c5-7 8-10 8-13a8 8 0 1 0-16 0c0 3 3 6 8 13z"/><circle cx="12" cy="9" r="2.5"/>',
        'qr'           => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><path d="M14 14h2v2h-2zM18 14h3M14 18h3M18 21h3M21 14v3"/>',
        'sparkles'     => '<path d="M12 3v4M12 17v4M3 12h4M17 12h4M5.6 5.6l2.8 2.8M15.6 15.6l2.8 2.8M5.6 18.4l2.8-2.8M15.6 8.4l2.8-2.8"/>',
        'split'        => '<path d="M16 3h5v5"/><path d="M8 21H3v-5"/><path d="M21 3 3 21"/>',
        'group'        => '<circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M3 20c0-3 2.5-5 6-5s6 2 6 5"/><path d="M14 20c0-2 1.5-3.5 4-3.5s4 1.5 4 3.5"/>',
        'flame'        => '<path d="M12 22c4 0 7-3 7-7 0-3-2-4-3-6-1 1-2 2-3 2 0-3-3-7-3-7-2 4-7 7-7 11 0 4 3 7 9 7z"/>',
        'star'         => '<path d="m12 3 3 6 7 1-5 5 1 7-6-3-6 3 1-7-5-5 7-1z"/>',
    ];

    $body = $svgs[$name] ?? $svgs['other'];
    $classes = $attributes->get('class', '');
@endphp
<svg xmlns="http://www.w3.org/2000/svg" width="{{ $size }}" height="{{ $size }}"
     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $sw }}"
     stroke-linecap="round" stroke-linejoin="round" class="{{ $classes }}" aria-hidden="true">{!! $body !!}</svg>
