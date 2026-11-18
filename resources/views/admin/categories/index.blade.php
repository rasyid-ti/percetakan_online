<x-layouts.panel title="Kategori">
    <a href="{{ route('admin.products.index') }}" class="text-sm font-bold text-brand-600 hover:text-ink-900">← Produk</a>
    <h1 class="mt-4 text-3xl font-extrabold tracking-tight">Kategori</h1>

    <div class="mt-8 flex flex-wrap items-start gap-8">

        <div class="min-w-[300px] flex-[999_1_480px] space-y-4">
            @foreach ($categories as $item)
                <div class="flex flex-wrap items-center gap-4 rounded-3xl border border-ink-100 bg-white p-5">
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-100 text-brand-600">
                        <x-category-icon :name="$item->icon" :width="24" :height="24" />
                    </span>
                    <div class="min-w-[160px] flex-1">
                        <div class="text-lg font-extrabold">{{ $item->name }}</div>
                        <div class="text-sm text-ink-600">{{ $item->products_count }} produk · urutan {{ $item->sort_order }}</div>
                    </div>
                    <div class="flex items-center gap-4 text-sm">
                        <a href="{{ route('admin.categories.edit', $item) }}" class="font-bold text-brand-600 hover:text-ink-900">Ubah</a>
                        <form method="POST" action="{{ route('admin.categories.destroy', $item) }}" onsubmit="return confirm('Hapus kategori {{ addslashes($item->name) }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="font-bold text-rose-600 hover:text-ink-900">Hapus</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <aside class="min-w-[280px] flex-1 basis-[320px] rounded-3xl border border-ink-100 bg-white p-7">
            <h2 class="mb-5 text-lg font-extrabold">Tambah kategori</h2>
            <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-5">
                @csrf
                @include('admin.categories._form')
                <button type="submit" class="btn-primary w-full py-3.5">Simpan Kategori</button>
            </form>
        </aside>
    </div>
</x-layouts.panel>