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
<div class="flex min-h-screen flex-wrap">

    {{-- Panel kiri (desktop) --}}
    <aside class="relative hidden overflow-hidden bg-ink-900 text-white lg:flex lg:w-[46%] lg:flex-col lg:justify-between lg:p-14">
        <x-logo-mark :light="true" />

        <div class="relative z-10 max-w-md">
            <h2 class="text-5xl font-extrabold leading-[1.05] tracking-tight">
                Cetak Mudah,<br><span class="text-sun-400">Kapan Saja.</span>
            </h2>
            <p class="mt-5 text-lg leading-relaxed text-ink-100">
                Pilih produk, unggah desain, bayar, lalu pantau proses cetaknya dari satu tempat.
            </p>
        </div>

        <p class="relative z-10 text-sm text-ink-200">&copy; {{ date('Y') }} TintaKita Online</p>

        <div class="absolute -right-16 top-[38%] h-8 w-72 rounded-full bg-brand-500"></div>
        <div class="absolute -right-24 top-[46%] h-8 w-80 rounded-full bg-magenta-500"></div>
        <div class="absolute -right-12 top-[54%] h-8 w-60 rounded-full bg-sun-400"></div>
    </aside>

    {{-- Form --}}
    <main class="flex flex-1 items-center justify-center px-6 py-12">
        <div class="w-full max-w-md">
            <a href="{{ route('home') }}" class="mb-8 block lg:hidden">
                <img src="{{ asset('images/logo.png') }}" alt="TintaKita Online" class="h-14 w-auto">
            </a>
            {{ $slot }}
        </div>
    </main>
</div>
</body>
</html>