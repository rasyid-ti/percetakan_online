@props(['status'])
@php
    $map = [
        'amber' => 'bg-amber-100 text-amber-800',
        'orange' => 'bg-orange-100 text-orange-800',
        'sky' => 'bg-sky-100 text-sky-800',
        'indigo' => 'bg-indigo-100 text-indigo-800',
        'violet' => 'bg-violet-100 text-violet-800',
        'emerald' => 'bg-emerald-100 text-emerald-800',
        'green' => 'bg-green-100 text-green-800',
        'rose' => 'bg-rose-100 text-rose-800',
        'red' => 'bg-red-100 text-red-800',
    ];
@endphp
<span {{ $attributes->class(['inline-block rounded-full px-3 py-1 text-xs font-bold', $map[$status->color()] ?? 'bg-ink-100 text-ink-900']) }}>{{ $status->label() }}</span>