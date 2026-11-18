<x-layouts.panel title="Ringkasan Bisnis">
    <div class="flex flex-col gap-6">

        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight">Ringkasan Bisnis</h1>
                <div class="mt-1 text-sm text-ink-500">{{ $periodLabel }}</div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex rounded-xl border border-ink-200 bg-white p-1">
                    @foreach (['7' => '7 hari', '30' => '30 hari', 'tahun' => 'Tahun ini'] as $key => $label)
                        <a href="{{ route('owner.dashboard', ['periode' => $key]) }}" @class([
                            'rounded-lg px-4 py-2 text-sm font-bold',
                            'bg-ink-900 text-white' => $period === (string) $key,
                            'text-ink-600 hover:text-ink-900' => $period !== (string) $key,
                        ])>{{ $label }}</a>
                    @endforeach
                </div>
                <a href="{{ route('owner.report') }}" class="inline-flex rounded-xl bg-magenta-600 px-5 py-3 text-sm font-bold text-white hover:bg-ink-900">Laporan Penjualan</a>
            </div>
        </div>

        @if ($waitingVerification > 0)
            <div class="rounded-2xl bg-amber-50 px-5 py-3 text-sm font-bold text-amber-900 ring-1 ring-amber-200">
                {{ $waitingVerification }} pembayaran menunggu verifikasi admin.
            </div>
        @endif

        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl bg-ink-900 p-6 text-white">
                <div class="text-sm font-semibold text-ink-100">Omzet</div>
                <div class="mt-2 text-3xl font-extrabold text-sun-400">Rp {{ number_format($revenue, 0, ',', '.') }}</div>
                <div class="mt-1 text-sm text-ink-100">Dari pembayaran lunas</div>
            </div>
            <div class="rounded-2xl border border-ink-100 bg-white p-6">
                <div class="text-sm font-semibold text-ink-500">Total Pesanan</div>
                <div class="mt-2 text-3xl font-extrabold">{{ $totalOrders }}</div>
                <div class="mt-1 text-sm text-ink-600">{{ $completedOrders }} sudah selesai</div>
            </div>
            <div class="rounded-2xl border border-ink-100 bg-white p-6">
                <div class="text-sm font-semibold text-ink-500">Rata-rata per Pesanan</div>
                <div class="mt-2 text-3xl font-extrabold">Rp {{ number_format($average, 0, ',', '.') }}</div>
                <div class="mt-1 text-sm text-ink-600">Omzet ÷ pesanan lunas</div>
            </div>
            <div class="rounded-2xl border border-ink-100 bg-white p-6">
                <div class="text-sm font-semibold text-ink-500">Pelanggan Aktif</div>
                <div class="mt-2 text-3xl font-extrabold">{{ $activeCustomers }}</div>
                <div class="mt-1 text-sm text-ink-600">Memesan pada periode ini</div>
            </div>
        </div>

        <div class="flex flex-wrap items-start gap-6">

            <section class="min-w-[300px] flex-[999_1_520px] rounded-3xl border border-ink-100 bg-white p-6">
                <div class="mb-2 flex items-center justify-between gap-3">
                    <h2 class="text-xl font-extrabold">{{ $period === 'tahun' ? 'Omzet Bulanan' : 'Omzet Harian' }}</h2>
                    <span class="text-sm text-ink-500">Rupiah</span>
                </div>

                <svg viewBox="0 0 640 270" width="100%" role="img" aria-label="Grafik omzet. Total periode Rp {{ number_format($revenue, 0, ',', '.') }}" class="block">
                    @foreach ($chart['grid'] as $g)
                        <line x1="56" x2="632" y1="{{ $g['y'] }}" y2="{{ $g['y'] }}" stroke="#E3EAF5" stroke-width="1"/>
                        <text x="0" y="{{ $g['y'] + 4 }}" font-size="11" fill="#5A6F92">{{ $g['label'] }}</text>
                    @endforeach

                    @foreach ($chart['bars'] as $b)
                        <rect x="{{ $b['x'] }}" y="{{ $b['y'] }}" width="{{ $b['w'] }}" height="{{ $b['h'] }}" rx="6" fill="{{ $b['color'] }}"/>
                        @if ($b['value'] !== '')
                            <text x="{{ $b['cx'] }}" y="{{ $b['y'] - 6 }}" text-anchor="middle" font-size="11" font-weight="700" fill="#0B2A5B">{{ $b['value'] }}</text>
                        @endif
                        @if ($b['label'] !== '')
                            <text x="{{ $b['cx'] }}" y="244" text-anchor="middle" font-size="12" fill="#5A6F92">{{ $b['label'] }}</text>
                        @endif
                    @endforeach
                </svg>

                @if ($revenue === 0)
                    <p class="mt-2 text-center text-sm text-ink-500">Belum ada pembayaran lunas pada periode ini.</p>
                @endif
            </section>

            <section class="min-w-[280px] flex-1 basis-[300px] space-y-5 rounded-3xl border border-ink-100 bg-white p-6">
                <h2 class="text-xl font-extrabold">Produk Terlaris</h2>

                @forelse ($topProducts as $product)
                    <div class="space-y-2">
                        <div class="flex justify-between gap-2 text-sm">
                            <span class="font-bold">{{ $product->name }}</span>
                            <span class="shrink-0 text-ink-600">{{ $product->orders_count }} pesanan</span>
                        </div>
                        <div class="h-2.5 overflow-hidden rounded-full bg-ink-100">
                            <div class="h-full rounded-full bg-brand-500" style="width: {{ round($product->revenue / $topMax * 100) }}%"></div>
                        </div>
                        <div class="text-xs font-semibold text-ink-500">Rp {{ number_format($product->revenue, 0, ',', '.') }}</div>
                    </div>
                @empty
                    <p class="py-6 text-center text-sm text-ink-500">Belum ada data penjualan.</p>
                @endforelse
            </section>
        </div>

        <section class="rounded-3xl border border-ink-100 bg-white p-6">
            <h2 class="mb-4 text-xl font-extrabold">Beban Kerja Produksi</h2>
            <div class="flex flex-wrap gap-4">
                @forelse ($operators as $operator)
                    <div class="flex min-w-[240px] flex-1 items-center gap-4 rounded-2xl bg-ink-50 p-5">
                        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-ink-900 text-sm font-extrabold text-white">{{ $operator->initials() }}</span>
                        <div>
                            <div class="font-extrabold">{{ $operator->name }}</div>
                            <div class="text-sm text-ink-600">{{ $operator->active_count }} aktif · {{ $operator->done_count }} selesai pada periode ini</div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-ink-500">Belum ada operator aktif.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-layouts.panel>