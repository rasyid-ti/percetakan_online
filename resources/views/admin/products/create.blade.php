<x-layouts.panel title="Tambah Produk">
    <a href="{{ route('admin.products.index') }}" class="text-sm font-bold text-brand-600 hover:text-ink-900">← Semua produk</a>
    <h1 class="mt-4 text-3xl font-extrabold tracking-tight">Tambah Produk</h1>

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="mt-8">
        @csrf
        @include('admin.products._form')
    </form>
</x-layouts.panel>