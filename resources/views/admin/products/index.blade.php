<x-layouts.panel title="Produk & Kategori">
    <div class="flex flex-col gap-6">

        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight">Produk &amp; Kategori</h1>
                <div class="mt-1 text-sm text-ink-500">{{ $products->total() }} produk</div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.categories.index') }}" class="btn-outline !py-3">Kelola Kategori</a>
                <a href="{{ route('admin.products.create') }}" class="btn-primary">+ Tambah Produk</a>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.products.index') }}" class="flex gap-2">
            <label for="q" class="sr-only">Cari produk</label>
            <input id="q" name="q" type="text" value="{{ $q }}" placeholder="Cari nama produk" class="input !w-72">
            <button type="submit" class="btn-primary">Cari</button>
        </form>

        @if ($products->isEmpty())
            <div class="rounded-3xl border border-ink-100 bg-white p-12 text-center">
                <div class="text-lg font-extrabold">Produk tidak ditemukan</div>
            </div>
        @else
            <div class="overflow-x-auto rounded-3xl border border-ink-100 bg-white p-6">
                <div class="min-w-[860px]">
                    <div class="grid grid-cols-[1fr_2.2fr_1.4fr_1.3fr_1.1fr_1.6fr] gap-3 border-b border-ink-100 pb-3 text-xs font-bold tracking-wide text-ink-500">
                        <span>GAMBAR</span><span>PRODUK</span><span>KATEGORI</span><span>HARGA DASAR</span><span>STATUS</span><span>AKSI</span>
                    </div>

                    @foreach ($products as $product)
                        <div class="grid grid-cols-[1fr_2.2fr_1.4fr_1.3fr_1.1fr_1.6fr] items-center gap-3 border-b border-ink-50 py-4 text-sm">
                            <span>
                                @if ($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="" class="h-14 w-20 rounded-lg object-cover">
                                @else
                                    <span class="flex h-14 w-20 items-center justify-center rounded-lg bg-ink-100 text-center text-[11px] font-bold leading-tight text-ink-500">Tanpa gambar</span>
                                @endif
                            </span>
                            <span>
                                <span class="block font-extrabold">{{ $product->name }}</span>
                                <span class="text-xs text-ink-500">{{ $product->options_count }} opsi @if ($product->is_featured) · Unggulan @endif</span>
                            </span>
                            <span class="text-ink-600">{{ $product->category->name }}</span>
                            <span class="font-bold">{{ $product->price_label }} <span class="text-xs font-semibold text-ink-500">/ {{ $product->unit }}</span></span>
                            <span>
                                <form method="POST" action="{{ route('admin.products.toggle', $product) }}">
                                    @csrf
                                    <button type="submit" @class([
                                        'rounded-full px-3 py-1 text-xs font-bold',
                                        'bg-emerald-100 text-emerald-800' => $product->is_active,
                                        'bg-ink-100 text-ink-600' => ! $product->is_active,
                                    ])>{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</button>
                                </form>
                            </span>
                            <span class="flex items-center gap-4">
                                <a href="{{ route('admin.products.edit', $product) }}" class="font-bold text-brand-600 hover:text-ink-900">Ubah</a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Hapus produk {{ addslashes($product->name) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-bold text-rose-600 hover:text-ink-900">Hapus</button>
                                </form>
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div>{{ $products->links() }}</div>
        @endif
    </div>
</x-layouts.panel>