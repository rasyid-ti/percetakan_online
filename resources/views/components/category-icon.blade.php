@props(['name' => 'card'])
@php
    $paths = [
        'card' => 'M5 6h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2zM7 10h5M7 14h8',
        'banner' => 'M5 3v18M5 4h14v10H5M9 8h6',
        'sticker' => 'M21 12a9 9 0 1 0-9 9M21 12h-6a3 3 0 0 0-3 3v6',
        'flyer' => 'M7 3h8l4 4v14H7zM15 3v4h4M10 12h6M10 16h6',
        'invitation' => 'M3 7l9 6 9-6M3 7v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2z',
    ];
    $d = $paths[$name] ?? $paths['card'];
@endphp
<svg {{ $attributes->merge(['width' => 28, 'height' => 28]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="{{ $d }}"/>
</svg>