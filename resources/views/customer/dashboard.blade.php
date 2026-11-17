@php
    $categories = \App\Models\Category::withCount('products')->orderBy('sort_order')->get();
@endphp

<x-layouts.customer title="Beranda">
    <div class="rounded-3xl bg-ink-900 p-8 text-white lg:p-12">
        <p class="text-sm font-bold text-sun-400">HALO, {{ strtoupper(str(auth()->user()->name)->before(' ')) }}</p>
        <h1 class="mt-2 text-4xl font-extrabold leading-tight tracking-tight">Mau cetak apa hari ini?</h1>
        <p class="mt-3 max-w-xl text-ink-100">Pilih produk, atur ukuran dan finishing, lalu lihat harganya langsung.</p>
        <a href="{{ route('catalog') }}" class="mt-6 inline-flex rounded-xl bg-white px-6 py-3 text-sm font-extrabold text-ink-900 hover:bg-sun-400">Buka Katalog</a>
    </div>

    <h2 class="mt-10 text-xl font-extrabold">Kategori produk</h2>
    <div class="mt-4 grid gap-5 sm:grid-cols-2 lg:grid-cols-5">
        @foreach ($categories as $category)
            <a href="{{ route('catalog', ['kategori' => $category->slug]) }}" class="rounded-2xl border border-ink-100 bg-white p-6 transition hover:-translate-y-1 hover:border-brand-500">
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-100 text-brand-600">
                    <x-category-icon :name="$category->icon" :width="24" :height="24" />
                </span>
                <div class="mt-4 text-lg font-extrabold">{{ $category->name }}</div>
                <div class="mt-1 text-sm text-ink-600">{{ $category->description }}</div>
                <div class="mt-3 text-sm font-bold text-brand-600">{{ $category->products_count }} produk</div>
            </a>
        @endforeach
    </div>
</x-layouts.customer>