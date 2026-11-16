@props(['heading', 'text'])
@php
    $stats = [
        ['Produk Aktif', \App\Models\Product::active()->count(), 'border-brand-500'],
        ['Kategori', \App\Models\Category::count(), 'border-magenta-500'],
        ['Pelanggan', \App\Models\User::where('role', 'customer')->count(), 'border-sun-400'],
        ['Total Pesanan', \App\Models\Order::count(), 'border-ink-900'],
    ];
@endphp

<div class="flex flex-col gap-8">
    <div>
        <h1 class="text-3xl font-extrabold tracking-tight">{{ $heading }}</h1>
        <p class="mt-1 text-sm text-ink-500">{{ $text }}</p>
    </div>

    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as [$label, $value, $border])
            <div class="rounded-2xl border border-ink-100 border-t-[5px] {{ $border }} bg-white p-6">
                <div class="text-sm font-semibold text-ink-500">{{ $label }}</div>
                <div class="mt-1.5 text-4xl font-extrabold">{{ $value }}</div>
            </div>
        @endforeach
    </div>

    <div class="rounded-3xl bg-white p-8 ring-1 ring-ink-100">
        <h2 class="text-lg font-extrabold">Halaman ini akan segera diisi</h2>
        <p class="mt-2 max-w-xl text-sm leading-relaxed text-ink-600">
            Kerangka login, peran, dan layout sudah siap. Isi dashboard lengkap sesuai desain
            akan dibangun pada tahap berikutnya.
        </p>
    </div>
</div>