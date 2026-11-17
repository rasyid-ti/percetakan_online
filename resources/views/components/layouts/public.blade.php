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
            <a href="{{ route('home') }}" @class(['hover:text-brand-600', 'text-brand-600' => request()->routeIs('home')])>Beranda</a>
            <a href="{{ route('catalog') }}" @class(['hover:text-brand-600', 'text-brand-600' => request()->routeIs('catalog', 'product.show')])>Katalog</a>
            <a href="{{ route('home') }}#cara-pesan" class="hover:text-brand-600">Cara Pesan</a>
        </nav>

        <div class="flex items-center gap-3">
            @auth
                @if (auth()->user()->isCustomer())
                    <a href="{{ route('cart.index') }}" class="btn-outline">Keranjang ({{ \App\Support\Cart::count() }})</a>
                @endif
                <a href="{{ route(auth()->user()->role->dashboardRoute()) }}" class="btn-primary !py-2.5">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="px-4 py-2.5 text-sm font-bold">Masuk</a>
                <a href="{{ route('register') }}" class="btn-primary !py-2.5">Daftar Gratis</a>
            @endauth
        </div>
    </div>
</header>

@if (session('success'))
    <div class="mx-auto mt-6 max-w-7xl px-6 lg:px-12">
        <div class="rounded-xl bg-emerald-50 px-5 py-3 text-sm font-bold text-emerald-800 ring-1 ring-emerald-200">{{ session('success') }}</div>
    </div>
@endif

<main>
    {{ $slot }}
</main>

<footer class="mt-20 bg-[#08224A] text-ink-100">
    <div class="mx-auto flex max-w-7xl flex-wrap justify-between gap-8 px-6 py-12 lg:px-12">
        <div class="max-w-xs">
            <x-logo-mark :light="true" />
            <p class="mt-4 text-sm leading-relaxed">Cetak Mudah, Kapan Saja. Percetakan online untuk kartu nama, banner, stiker, brosur, dan undangan.</p>
        </div>
        <div class="flex gap-14 text-sm">
            <div class="flex flex-col gap-2.5">
                <span class="font-bold text-white">Produk</span>
                @foreach (\App\Models\Category::orderBy('sort_order')->take(4)->get() as $cat)
                    <a href="{{ route('catalog', ['kategori' => $cat->slug]) }}" class="hover:text-white">{{ $cat->name }}</a>
                @endforeach
            </div>
            <div class="flex flex-col gap-2.5">
                <span class="font-bold text-white">Akun</span>
                <a href="{{ route('login') }}" class="hover:text-white">Masuk</a>
                <a href="{{ route('register') }}" class="hover:text-white">Daftar</a>
            </div>
        </div>
    </div>
    <div class="border-t border-ink-800 px-6 py-4 text-center text-xs">&copy; {{ date('Y') }} TintaKita Online</div>
</footer>

</body>
</html>