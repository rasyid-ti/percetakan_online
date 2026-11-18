<x-layouts.panel :title="'Ubah ' . $category->name">
    <a href="{{ route('admin.categories.index') }}" class="text-sm font-bold text-brand-600 hover:text-ink-900">← Semua kategori</a>
    <h1 class="mt-4 text-3xl font-extrabold tracking-tight">Ubah Kategori</h1>

    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="mt-8 max-w-xl space-y-5 rounded-3xl border border-ink-100 bg-white p-7">
        @csrf
        @method('PUT')
        @include('admin.categories._form')
        <button type="submit" class="btn-primary w-full py-3.5">Simpan Perubahan</button>
    </form>
</x-layouts.panel>