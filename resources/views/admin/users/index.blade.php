@php
    $badge = [
        'owner' => 'bg-violet-100 text-violet-800',
        'admin' => 'bg-indigo-100 text-indigo-800',
        'operator' => 'bg-sky-100 text-sky-800',
        'customer' => 'bg-ink-100 text-ink-900',
    ];
@endphp

<x-layouts.panel title="Pengguna">
    <div class="flex flex-col gap-6">

        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight">Pengguna</h1>
                <div class="mt-1 text-sm text-ink-500">{{ $users->total() }} akun</div>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn-primary">+ Tambah Akun</a>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.users.index', array_filter(['q' => $q])) }}" @class([
                    'rounded-full border-2 px-4 py-2 text-sm font-bold',
                    'border-ink-900 bg-ink-900 text-white' => $role === '',
                    'border-ink-200 bg-white hover:border-ink-900' => $role !== '',
                ])>Semua</a>
                @foreach ($roles as $r)
                    <a href="{{ route('admin.users.index', array_filter(['role' => $r->value, 'q' => $q])) }}" @class([
                        'rounded-full border-2 px-4 py-2 text-sm font-bold',
                        'border-ink-900 bg-ink-900 text-white' => $role === $r->value,
                        'border-ink-200 bg-white hover:border-ink-900' => $role !== $r->value,
                    ])>{{ $r->label() }}</a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-2">
                <input type="hidden" name="role" value="{{ $role }}">
                <label for="q" class="sr-only">Cari pengguna</label>
                <input id="q" name="q" type="text" value="{{ $q }}" placeholder="Cari nama atau email" class="input !w-64">
                <button type="submit" class="btn-primary">Cari</button>
            </form>
        </div>

        <div class="overflow-x-auto rounded-3xl border border-ink-100 bg-white p-6">
            <div class="min-w-[760px]">
                <div class="grid grid-cols-[2.4fr_1.4fr_1.2fr_1fr_0.8fr] gap-3 border-b border-ink-100 pb-3 text-xs font-bold tracking-wide text-ink-500">
                    <span>PENGGUNA</span><span>TELEPON</span><span>PERAN</span><span>STATUS</span><span>AKSI</span>
                </div>

                @foreach ($users as $item)
                    <div class="grid grid-cols-[2.4fr_1.4fr_1.2fr_1fr_0.8fr] items-center gap-3 border-b border-ink-50 py-4 text-sm">
                        <span class="flex items-center gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-ink-900 text-xs font-extrabold text-white">{{ $item->initials() }}</span>
                            <span class="min-w-0">
                                <span class="block truncate font-extrabold">{{ $item->name }}</span>
                                <span class="block truncate text-xs text-ink-500">{{ $item->email }}</span>
                            </span>
                        </span>
                        <span class="text-ink-600">{{ $item->phone ?? '-' }}</span>
                        <span><span class="rounded-full px-3 py-1 text-xs font-bold {{ $badge[$item->role->value] }}">{{ $item->role->label() }}</span></span>
                        <span>
                            <span @class([
                                'rounded-full px-3 py-1 text-xs font-bold',
                                'bg-emerald-100 text-emerald-800' => $item->is_active,
                                'bg-ink-100 text-ink-600' => ! $item->is_active,
                            ])>{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </span>
                        <span>
                            @if ($item->role === \App\Enums\Role::Owner)
                                <span class="text-xs font-semibold text-ink-500">Terkunci</span>
                            @else
                                <a href="{{ route('admin.users.edit', $item) }}" class="font-bold text-brand-600 hover:text-ink-900">Ubah</a>
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <div>{{ $users->links() }}</div>
    </div>
</x-layouts.panel>