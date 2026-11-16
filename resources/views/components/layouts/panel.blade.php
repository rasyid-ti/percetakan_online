@props(['title' => 'Dashboard'])
@php
    $user = auth()->user();

    $iconDashboard = 'M4 4h7v7H4zM13 4h7v4h-7zM13 11h7v9h-7zM4 14h7v6H4z';
    $iconOrders = 'M6 3h12v18l-3-2-3 2-3-2-3 2zM9 8h6M9 12h6';
    $iconPayment = 'M3 7h18v10H3zM3 11h18M7 15h3';
    $iconBox = 'M21 8l-9-5-9 5 9 5 9-5zM3 8v8l9 5 9-5V8M12 13v8';
    $iconUsers = 'M16 20v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M10 10a4 4 0 1 0 0-8 4 4 0 0 0 0 8';

    $menus = [
        'admin' => [
            ['Dashboard', 'admin.dashboard', $iconDashboard],
            ['Pesanan', null, $iconOrders],
            ['Pembayaran', null, $iconPayment],
            ['Produk & Kategori', null, $iconBox],
            ['Pengguna', null, $iconUsers],
        ],
        'operator' => [
            ['Antrean Produksi', 'operator.dashboard', 'M4 4h6v16H4zM14 4h6v10h-6z'],
            ['Riwayat Tugas', null, 'M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18zM12 7v5l3 2'],
            ['Profil', null, $iconUsers],
        ],
        'owner' => [
            ['Ringkasan', 'owner.dashboard', $iconDashboard],
            ['Laporan Penjualan', null, 'M4 20V10M10 20V4M16 20v-7M22 20H2'],
            ['Produk', null, $iconBox],
            ['Tim & Pengguna', null, $iconUsers],
        ],
    ];

    $items = $menus[$user->role->value] ?? [];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-ink-50 font-sans text-ink-900 antialiased">
<div class="flex min-h-screen flex-wrap">

    <aside class="flex w-full flex-col gap-7 bg-ink-900 p-5 text-white lg:w-[264px] lg:shrink-0">
        <div class="px-2">
            <x-logo-mark :light="true" />
        </div>

        <nav class="flex flex-col gap-1.5">
            @foreach ($items as [$label, $route, $icon])
                @php $active = $route && request()->routeIs($route); @endphp
                <a href="{{ $route ? route($route) : '#' }}" @class([
                    'flex items-center gap-3.5 rounded-2xl px-3.5 py-3 text-[15px]',
                    'bg-white font-bold text-ink-900' => $active,
                    'font-semibold text-ink-100 hover:bg-ink-800' => ! $active,
                ])>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $active ? '#0B6BDB' : 'currentColor' }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="{{ $icon }}"/>
                    </svg>
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        <div class="mt-auto rounded-2xl bg-ink-800 p-3.5">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-sun-400 text-sm font-extrabold text-ink-900">{{ $user->initials() }}</span>
                <div class="min-w-0">
                    <div class="truncate text-sm font-bold">{{ $user->name }}</div>
                    <div class="text-xs text-ink-100">{{ $user->role->label() }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="w-full rounded-xl border border-ink-500 py-2 text-sm font-bold text-white hover:bg-ink-900">Keluar</button>
            </form>
        </div>
    </aside>

    <main class="min-w-0 flex-1 p-6 lg:p-10">
        {{ $slot }}
    </main>
</div>
</body>
</html>