@props(['light' => false])

<span {{ $attributes->class(['inline-flex items-center gap-3']) }}>
    <svg width="36" height="30" viewBox="0 0 40 34" aria-hidden="true">
        <rect x="0" y="4" width="26" height="7" rx="3.5" fill="#1A8CFF"/>
        <rect x="4" y="14" width="30" height="7" rx="3.5" fill="#EC0E80"/>
        <rect x="0" y="24" width="22" height="7" rx="3.5" fill="#FFD500"/>
    </svg>
    <span @class(['text-2xl font-extrabold', 'text-white' => $light, 'text-ink-900' => ! $light])>
        tinta<span @class(['text-[#5BB0FF]' => $light, 'text-brand-600' => ! $light])>kita</span>
    </span>
</span>