@php
    $categories = \App\Models\Category::withCount('products')->orderBy('sort_order')->get();
@endphp

<x-layouts.customer title="Beranda">
    <div class="rounded-3xl bg-ink-900 p-8 text-white lg:p-12">
        <p class="text-sm font-bold text-sun-400">HALO, {{ strtoupper(str(auth()->user()->name)->before(' ')) }}</p>
        <h1 class="mt-2 text-4xl font-extrabold leading-tight tracking-tight">Mau cetak apa hari ini?</h1>
        <p class="mt-3 max-w-xl text-ink-100">Katalog lengkap, konfigurator harga, dan pelacakan pesanan akan hadir di tahap berikutnya.</p>
    </div>

    <h2 class="mt-10 text-xl font-extrabold">Kategori produk</h2>
    <div class="mt-4 grid gap-5 sm:grid-cols-2 lg:grid-cols-5">
        @foreach ($categories as $category)
            <div class="rounded-2xl border border-ink-100 bg-white p-6">
                <div class="text-lg font-extrabold">{{ $category->name }}</div>
                <div class="mt-1 text-sm text-ink-600">{{ $category->description }}</div>
                <div class="mt-3 text-sm font-bold text-brand-600">{{ $category->products_count }} produk</div>
            </div>
        @endforeach
    </div>
</x-layouts.customer>