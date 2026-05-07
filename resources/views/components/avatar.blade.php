@props(['user', 'size' => 36])
<img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}"
     style="width:{{ $size }}px;height:{{ $size }}px;border-radius:9999px;object-fit:cover;border:2px solid #fff;"
     class="shrink-0">
