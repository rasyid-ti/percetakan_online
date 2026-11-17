@php
    $routeName = $mode === 'payments' ? 'admin.payments' : 'admin.orders.index';
    $heading = $mode === 'payments' ? 'Pembayaran' : 'Pesanan';
@endphp

<x-layouts.panel :title="$heading">
    <div class="flex flex-col gap-6">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight">{{ $heading }}</h1>
            <div class="mt-1 text-sm text-ink-500">{{ $orders->total() }} pesanan ditemukan</div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap gap-2">
                @foreach ($options as [$optValue, $optLabel])
                    <a href="{{ route($routeName, array_filter([$param => $optValue, 'q' => $q])) }}" @class([
                        'rounded-full border-2 px-4 py-2 text-sm font-bold',
                        'border-ink-900 bg-ink-900 text-white' => $value === $optValue,
                        'border-ink-200 bg-white hover:border-ink-900' => $value !== $optValue,
                    ])>{{ $optLabel }}</a>
                @endforeach
            </div>

            <form method="GET" action="{{ route($routeName) }}" class="flex gap-2">
                <input type="hidden" name="{{ $param }}" value="{{ $value }}">
                <label for="q" class="sr-only">Cari pesanan</label>
                <input id="q" name="q" type="text" value="{{ $q }}" placeholder="Cari kode atau pelanggan" class="input !w-64">
                <button type="submit" class="btn-primary">Cari</button>
            </form>
        </div>

        @if ($orders->isEmpty())
            <div class="rounded-3xl border border-ink-100 bg-white p-12 text-center">
                <div class="text-lg font-extrabold">Tidak ada pesanan</div>
                <p class="mt-2 text-sm text-ink-600">Coba ganti filter atau kata kunci pencarian.</p>
            </div>
        @else
            <div class="overflow-x-auto rounded-3xl border border-ink-100 bg-white p-6">
                <div class="min-w-[860px]">
                    <div class="grid grid-cols-[1.4fr_1.2fr_1.6fr_1fr_1.3fr_1.3fr_1fr] gap-3 border-b border-ink-100 pb-3 text-xs font-bold tracking-wide text-ink-500">
                        <span>KODE</span><span>PELANGGAN</span><span>PRODUK</span><span>TOTAL</span><span>STATUS</span><span>PEMBAYARAN</span><span>OPERATOR</span>
                    </div>
                    @foreach ($orders as $order)
                        <a href="{{ route('admin.orders.show', $order->code) }}" class="grid grid-cols-[1.4fr_1.2fr_1.6fr_1fr_1.3fr_1.3fr_1fr] items-center gap-3 border-b border-ink-50 py-4 text-sm hover:bg-ink-50">
                            <span>
                                <span class="block font-bold">{{ $order->code }}</span>
                                <span class="text-xs text-ink-500">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</span>
                            </span>
                            <span>{{ $order->user->name }}</span>
                            <span class="text-ink-600">{{ $order->items->pluck('product_name')->take(2)->implode(', ') }}@if ($order->items->count() > 2) +{{ $order->items->count() - 2 }}@endif</span>
                            <span class="font-bold">{{ $order->total_label }}</span>
                            <span><x-status-badge :status="$order->status" /></span>
                            <span><x-status-badge :status="$order->payment_status" /></span>
                            <span class="text-ink-600">{{ $order->operator?->name ?? '-' }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <div>{{ $orders->links() }}</div>
        @endif
    </div>
</x-layouts.panel>