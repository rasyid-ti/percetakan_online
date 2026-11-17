<x-layouts.customer title="Pesanan Saya">
    <h1 class="text-3xl font-extrabold tracking-tight">Pesanan Saya</h1>

    @if ($orders->isEmpty())
        <div class="mt-8 rounded-3xl border border-ink-100 bg-white p-12 text-center">
            <div class="text-lg font-extrabold">Belum ada pesanan</div>
            <p class="mt-2 text-sm text-ink-600">Pesanan yang Anda buat akan muncul di sini.</p>
            <a href="{{ route('catalog') }}" class="btn-primary mt-6">Lihat Katalog</a>
        </div>
    @else
        <div class="mt-8 space-y-4">
            @foreach ($orders as $order)
                <a href="{{ route('orders.show', $order->code) }}" class="flex flex-wrap items-center justify-between gap-4 rounded-3xl border border-ink-100 bg-white p-6 transition hover:border-brand-500">
                    <div class="min-w-[220px]">
                        <div class="text-lg font-extrabold">{{ $order->code }}</div>
                        <div class="mt-1 text-sm text-ink-500">{{ $order->created_at->translatedFormat('d F Y, H:i') }}</div>
                        <div class="mt-2 text-sm text-ink-600">
                            {{ $order->items->pluck('product_name')->take(2)->implode(', ') }}
                            @if ($order->items->count() > 2) dan {{ $order->items->count() - 2 }} lainnya @endif
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <x-status-badge :status="$order->status" />
                        <x-status-badge :status="$order->payment_status" />
                    </div>
                    <div class="text-xl font-extrabold">{{ $order->total_label }}</div>
                </a>
            @endforeach
        </div>
    @endif
</x-layouts.customer>