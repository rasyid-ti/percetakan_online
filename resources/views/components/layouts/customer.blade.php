@props(['title' => 'TintaKita Online'])
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

<header class="border-b border-ink-100 bg-white">
    <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-6 py-3 lg:px-12">
        <a href="{{ route('home') }}" aria-label="TintaKita Online">
            <img src="{{ asset('images/logo.png') }}" alt="TintaKita Online" class="h-12 w-auto">
        </a>

        <nav class="flex flex-wrap gap-8 text-sm font-semibold">
            <a href="{{ route('customer.dashboard') }}" @class(['hover:text-brand-600', 'text-brand-600' => request()->routeIs('customer.dashboard')])>Beranda</a>
            <a href="{{ route('catalog') }}" class="hover:text-brand-600">Katalog</a>
            <a href="{{ route('orders.index') }}" @class(['hover:text-brand-600', 'text-brand-600' => request()->routeIs('orders.*', 'checkout.*')])>Pesanan Saya</a>
            <a href="{{ route('cart.index') }}" @class(['hover:text-brand-600', 'text-brand-600' => request()->routeIs('cart.*')])>Keranjang ({{ \App\Support\Cart::count() }})</a>
        </nav>

        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-ink-900 text-sm font-extrabold text-white">{{ auth()->user()->initials() }}</span>
            <span class="hidden text-sm font-bold sm:block">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-outline">Keluar</button>
            </form>
        </div>
    </div>
</header>

<main class="mx-auto max-w-7xl px-6 py-10 lg:px-12">
    @if (session('success'))
        <div class="mb-6 rounded-xl bg-emerald-50 px-5 py-3 text-sm font-bold text-emerald-800 ring-1 ring-emerald-200">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="mb-6 rounded-xl bg-rose-50 px-5 py-3 text-sm font-bold text-rose-800 ring-1 ring-rose-200">{{ session('error') }}</div>
    @endif
    
    {{ $slot }}
</main>

</body>
</html>