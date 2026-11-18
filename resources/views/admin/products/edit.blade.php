<x-layouts.panel :title="'Ubah ' . $product->name">
    <a href="{{ route('admin.products.index') }}" class="text-sm font-bold text-brand-600 hover:text-ink-900">← Semua produk</a>

    <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-3xl font-extrabold tracking-tight">Ubah Produk</h1>
        <a href="{{ route('product.show', $product->slug) }}" target="_blank" class="text-sm font-bold text-brand-600 hover:text-ink-900">Lihat di katalog →</a>
    </div>

    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="mt-8">
        @csrf
        @method('PUT')
        @include('admin.products._form')
    </form>
</x-layouts.panel>