<x-layouts.panel :title="'Ubah ' . $user->name">
    <a href="{{ route('admin.users.index') }}" class="text-sm font-bold text-brand-600 hover:text-ink-900">← Semua pengguna</a>
    <h1 class="mt-4 text-3xl font-extrabold tracking-tight">Ubah Akun</h1>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="mt-8 max-w-2xl space-y-6 rounded-3xl border border-ink-100 bg-white p-7">
        @csrf
        @method('PUT')
        @include('admin.users._form')
        <button type="submit" class="btn-primary w-full py-3.5">Simpan Perubahan</button>
    </form>
</x-layouts.panel>