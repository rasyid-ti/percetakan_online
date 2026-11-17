<x-layouts.public title="Katalog">
    <div class="mx-auto max-w-7xl px-6 py-12 lg:px-12">
        <div class="text-sm font-bold tracking-widest text-magenta-600">KATALOG</div>
        <h1 class="mt-1.5 text-4xl font-extrabold tracking-tight">Semua produk cetak</h1>

        <div class="mt-8 flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap gap-2.5">
                <a href="{{ route('catalog', array_filter(['q' => $search])) }}" @class([
                    'rounded-full px-5 py-2.5 text-sm font-bold border-2',
                    'border-ink-900 bg-ink-900 text-white' => ! $activeCategory,
                    'border-ink-200 bg-white hover:border-ink-900' => $activeCategory,
                ])>Semua</a>
                @foreach ($categories as $category)
                    <a href="{{ route('catalog', array_filter(['kategori' => $category->slug, 'q' => $search])) }}" @class([
                        'rounded-full px-5 py-2.5 text-sm font-bold border-2',
                        'border-ink-900 bg-ink-900 text-white' => $activeCategory === $category->slug,
                        'border-ink-200 bg-white hover:border-ink-900' => $activeCategory !== $category->slug,
                    ])>{{ $category->name }}</a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('catalog') }}" class="flex gap-2">
                @if ($activeCategory)
                    <input type="hidden" name="kategori" value="{{ $activeCategory }}">
                @endif
                <label for="q" class="sr-only">Cari produk</label>
                <input id="q" name="q" type="text" value="{{ $search }}" placeholder="Cari produk" class="input !w-64">
                <button type="submit" class="btn-primary">Cari</button>
            </form>
        </div>

        @if ($products->isEmpty())
            <div class="mt-10 rounded-3xl border border-ink-100 bg-white p-12 text-center">
                <div class="text-lg font-extrabold">Produk tidak ditemukan</div>
                <p class="mt-2 text-sm text-ink-600">Coba kata kunci lain atau pilih kategori yang berbeda.</p>
                <a href="{{ route('catalog') }}" class="btn-primary mt-6">Lihat semua produk</a>
            </div>
        @else
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($products as $product)
                    <a href="{{ route('product.show', $product->slug) }}" class="flex flex-col overflow-hidden rounded-3xl border border-ink-100 bg-white transition hover:-translate-y-1 hover:border-brand-500">
                        <x-product-art />
                        <div class="flex flex-1 flex-col gap-2.5 p-5">
                            <span class="w-fit rounded-full bg-magenta-100 px-2.5 py-1 text-xs font-bold text-magenta-600">{{ $product->category->name }}</span>
                            <span class="text-lg font-extrabold">{{ $product->name }}</span>
                            <span class="text-sm text-ink-600">Min. {{ $product->min_qty }} {{ $product->unit }} · Estimasi {{ $product->estimated_days }} hari</span>
                            <div class="mt-auto flex items-center justify-between pt-2">
                                <span class="text-lg font-extrabold">{{ $product->price_label }}<span class="text-xs font-semibold text-ink-500"> / {{ $product->unit }}</span></span>
                                <span class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white">Pesan</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.public>