<x-layouts.panel title="Riwayat Tugas">
    <div class="flex flex-col gap-6">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight">Riwayat Tugas</h1>
            <div class="mt-1 text-sm text-ink-500">Pesanan yang sudah selesai atau dibatalkan</div>
        </div>

        @if ($orders->isEmpty())
            <div class="rounded-3xl border border-ink-100 bg-white p-12 text-center">
                <div class="text-lg font-extrabold">Belum ada riwayat</div>
                <p class="mt-2 text-sm text-ink-600">Tugas yang sudah selesai akan tercatat di sini.</p>
            </div>
        @else
            <div class="overflow-x-auto rounded-3xl border border-ink-100 bg-white p-6">
                <div class="min-w-[640px]">
                    <div class="grid grid-cols-[1.4fr_1.2fr_1.8fr_1.2fr_1.2fr] gap-3 border-b border-ink-100 pb-3 text-xs font-bold tracking-wide text-ink-500">
                        <span>KODE</span><span>PELANGGAN</span><span>PRODUK</span><span>SELESAI</span><span>STATUS</span>
                    </div>
                    @foreach ($orders as $order)
                        <div class="grid grid-cols-[1.4fr_1.2fr_1.8fr_1.2fr_1.2fr] items-center gap-3 border-b border-ink-50 py-4 text-sm">
                            <span class="font-bold">{{ $order->code }}</span>
                            <span>{{ $order->user->name }}</span>
                            <span class="text-ink-600">{{ $order->items->pluck('product_name')->take(2)->implode(', ') }}@if ($order->items->count() > 2) +{{ $order->items->count() - 2 }}@endif</span>
                            <span class="text-ink-600">{{ ($order->completed_at ?? $order->updated_at)->translatedFormat('d M Y, H:i') }}</span>
                            <span><x-status-badge :status="$order->status" /></span>
                        </div>
                    @endforeach
                </div>
            </div>
            <div>{{ $orders->links() }}</div>
        @endif
    </div>
</x-layouts.panel>