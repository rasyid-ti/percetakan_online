@auth
    @php
        $ready = \App\Models\Order::where('user_id', auth()->id())
            ->where('status', \App\Enums\OrderStatus::Ready->value)
            ->latest()
            ->get();
    @endphp

    @if ($ready->isNotEmpty())
        <div class="mb-6 space-y-3">
            @foreach ($ready as $item)
                <a href="{{ route('orders.show', $item->code) }}" class="flex flex-wrap items-center gap-4 rounded-2xl bg-emerald-600 p-4 text-white shadow-lg transition hover:bg-emerald-700">
                    <span class="relative flex h-10 w-10 shrink-0 items-center justify-center">
                        <span class="absolute inset-0 animate-ping rounded-full bg-white/40"></span>
                        <span class="relative flex h-10 w-10 items-center justify-center rounded-full bg-white text-emerald-600">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7"/></svg>
                        </span>
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="font-extrabold">Pesanan {{ $item->code }} {{ $item->delivery_method === 'delivery' ? 'siap dikirim' : 'siap diambil' }}!</div>
                        <div class="text-sm text-emerald-50">
                            @if ($item->delivery_method !== 'delivery')
                                Lokasi: {{ $item->storage_location ?: 'tanyakan ke kasir' }} ·
                            @endif
                            Ketuk untuk melihat detail
                        </div>
                    </div>
                    <span class="text-sm font-bold">Lihat →</span>
                </a>
            @endforeach
        </div>
    @endif
@endauth