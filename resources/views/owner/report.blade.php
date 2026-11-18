<x-layouts.panel title="Laporan Penjualan">
    <div class="flex flex-col gap-6">

        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight">Laporan Penjualan</h1>
                <div class="mt-1 text-sm text-ink-500">{{ $from->translatedFormat('d F Y') }} – {{ $to->translatedFormat('d F Y') }}</div>
            </div>
            <a href="{{ route('owner.report.export', ['dari' => $from->toDateString(), 'sampai' => $to->toDateString()]) }}" class="inline-flex rounded-xl bg-magenta-600 px-5 py-3 text-sm font-bold text-white hover:bg-ink-900">Unduh CSV</a>
        </div>

        <form method="GET" action="{{ route('owner.report') }}" class="flex flex-wrap items-end gap-4 rounded-3xl border border-ink-100 bg-white p-5">
            <div>
                <label for="dari" class="label">Dari tanggal</label>
                <input id="dari" name="dari" type="date" value="{{ $from->toDateString() }}" class="input">
            </div>
            <div>
                <label for="sampai" class="label">Sampai tanggal</label>
                <input id="sampai" name="sampai" type="date" value="{{ $to->toDateString() }}" class="input">
            </div>
            <button type="submit" class="btn-primary !py-3.5">Tampilkan</button>
        </form>

        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl bg-ink-900 p-6 text-white">
                <div class="text-sm font-semibold text-ink-100">Total Omzet</div>
                <div class="mt-2 text-3xl font-extrabold text-sun-400">Rp {{ number_format($revenue, 0, ',', '.') }}</div>
            </div>
            <div class="rounded-2xl border border-ink-100 bg-white p-6">
                <div class="text-sm font-semibold text-ink-500">Pesanan Lunas</div>
                <div class="mt-2 text-3xl font-extrabold">{{ $orderCount }}</div>
            </div>
            <div class="rounded-2xl border border-ink-100 bg-white p-6">
                <div class="text-sm font-semibold text-ink-500">Rata-rata per Pesanan</div>
                <div class="mt-2 text-3xl font-extrabold">Rp {{ number_format($average, 0, ',', '.') }}</div>
            </div>
            <div class="rounded-2xl border border-ink-100 bg-white p-6">
                <div class="text-sm font-semibold text-ink-500">Dari Ongkos Kirim</div>
                <div class="mt-2 text-3xl font-extrabold">Rp {{ number_format($shipping, 0, ',', '.') }}</div>
            </div>
        </div>

        <section class="overflow-x-auto rounded-3xl border border-ink-100 bg-white p-6">
            <h2 class="mb-4 text-xl font-extrabold">Penjualan per Produk</h2>
            @if ($products->isEmpty())
                <p class="py-6 text-center text-sm text-ink-500">Tidak ada penjualan pada rentang ini.</p>
            @else
                <div class="min-w-[560px]">
                    <div class="grid grid-cols-[2.4fr_1fr_1fr_1.4fr] gap-3 border-b border-ink-100 pb-3 text-xs font-bold tracking-wide text-ink-500">
                        <span>PRODUK</span><span>PESANAN</span><span>JUMLAH</span><span>PENDAPATAN</span>
                    </div>
                    @foreach ($products as $row)
                        <div class="grid grid-cols-[2.4fr_1fr_1fr_1.4fr] gap-3 border-b border-ink-50 py-3.5 text-sm">
                            <span class="font-bold">{{ $row['name'] }}</span>
                            <span>{{ $row['orders'] }}</span>
                            <span>{{ number_format($row['qty'], 0, ',', '.') }}</span>
                            <span class="font-bold">Rp {{ number_format($row['revenue'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
                <p class="mt-3 text-xs text-ink-500">Pendapatan per produk belum termasuk ongkos kirim.</p>
            @endif
        </section>

        <section class="overflow-x-auto rounded-3xl border border-ink-100 bg-white p-6">
            <h2 class="mb-4 text-xl font-extrabold">Daftar Transaksi</h2>
            @if ($payments->isEmpty())
                <p class="py-6 text-center text-sm text-ink-500">Tidak ada transaksi pada rentang ini.</p>
            @else
                <div class="min-w-[640px]">
                    <div class="grid grid-cols-[1.6fr_1.4fr_1.5fr_1fr_1.2fr] gap-3 border-b border-ink-100 pb-3 text-xs font-bold tracking-wide text-ink-500">
                        <span>KODE</span><span>TANGGAL BAYAR</span><span>PELANGGAN</span><span>METODE</span><span>TOTAL</span>
                    </div>
                    @foreach ($payments as $payment)
                        <div class="grid grid-cols-[1.6fr_1.4fr_1.5fr_1fr_1.2fr] gap-3 border-b border-ink-50 py-3.5 text-sm">
                            <span class="font-bold">{{ $payment->order->code }}</span>
                            <span class="text-ink-600">{{ $payment->paid_at->translatedFormat('d M Y, H:i') }}</span>
                            <span>{{ $payment->order->user->name }}</span>
                            <span class="text-ink-600">{{ ucfirst($payment->method) }}</span>
                            <span class="font-bold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-layouts.panel>