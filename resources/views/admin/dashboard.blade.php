<x-layouts.panel title="Dashboard Admin">
    <div class="flex flex-col gap-7">

        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight">Dashboard Admin</h1>
                <div class="mt-1 text-sm text-ink-500">{{ now()->translatedFormat('l, d F Y') }}</div>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="btn-primary">Semua Pesanan</a>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($stats as $stat)
                <div class="rounded-2xl border border-ink-100 border-t-[5px] {{ $stat['border'] }} bg-white p-6">
                    <div class="text-sm font-semibold text-ink-500">{{ $stat['label'] }}</div>
                    <div class="mt-1.5 text-4xl font-extrabold">{{ $stat['value'] }}</div>
                    <div class="mt-0.5 text-sm text-ink-600">{{ $stat['hint'] }}</div>
                </div>
            @endforeach
        </div>

        <div class="flex flex-wrap items-start gap-6">

            {{-- Pesanan terbaru --}}
            <section class="min-w-[300px] flex-[999_1_560px] rounded-3xl border border-ink-100 bg-white p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-xl font-extrabold">Pesanan Terbaru</h2>
                    <a href="{{ route('admin.orders.index') }}" class="text-sm font-bold text-brand-600 hover:text-ink-900">Lihat semua →</a>
                </div>

                @if ($recent->isEmpty())
                    <p class="py-8 text-center text-sm text-ink-600">Belum ada pesanan.</p>
                @else
                    <div class="overflow-x-auto">
                        <div class="min-w-[640px]">
                            <div class="grid grid-cols-[1.5fr_1.2fr_1fr_1.4fr] gap-3 border-b border-ink-100 pb-3 text-xs font-bold tracking-wide text-ink-500">
                                <span>KODE</span><span>PELANGGAN</span><span>TOTAL</span><span>STATUS</span>
                            </div>
                            @foreach ($recent as $order)
                                <a href="{{ route('admin.orders.show', $order->code) }}" class="grid grid-cols-[1.5fr_1.2fr_1fr_1.4fr] items-center gap-3 border-b border-ink-50 py-4 text-sm hover:bg-ink-50">
                                    <span class="font-bold">{{ $order->code }}</span>
                                    <span>{{ $order->user->name }}</span>
                                    <span class="font-bold">{{ $order->total_label }}</span>
                                    <span><x-status-badge :status="$order->status" /></span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>

            {{-- Verifikasi pembayaran --}}
            <section class="min-w-[280px] flex-1 basis-[320px] space-y-4 rounded-3xl border border-ink-100 bg-white p-6">
                <h2 class="text-xl font-extrabold">Perlu Diverifikasi</h2>

                @forelse ($waiting as $payment)
                    <div class="rounded-2xl border border-ink-100 bg-ink-50 p-4">
                        <div class="text-sm font-extrabold">{{ $payment->order->code }}</div>
                        <div class="mt-0.5 text-sm text-ink-600">{{ $payment->order->user->name }} · {{ ucfirst($payment->method) }}</div>
                        <div class="mt-1 text-lg font-extrabold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                        <a href="{{ route('admin.orders.show', $payment->order->code) }}" class="btn-primary mt-3 w-full !py-2.5">Periksa</a>
                    </div>
                @empty
                    <p class="py-6 text-center text-sm text-ink-600">Tidak ada pembayaran yang menunggu.</p>
                @endforelse
            </section>
        </div>
    </div>
</x-layouts.panel>