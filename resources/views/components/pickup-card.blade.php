@props(['order'])

@if ($order->status === \App\Enums\OrderStatus::Ready)
    @if ($order->delivery_method === 'delivery')
        <div class="flex items-center gap-4 rounded-3xl border-2 border-emerald-500 bg-emerald-50 p-6">
            <span class="tk-pop flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7"/></svg>
            </span>
            <div>
                <div class="text-lg font-extrabold text-emerald-900">Pesanan siap dikirim</div>
                <div class="text-sm text-emerald-800">Pesanan akan segera dikirim ke alamat Anda.</div>
            </div>
        </div>
    @else
        <div class="rounded-3xl border-2 border-emerald-500 bg-emerald-50 p-6">
            <div class="flex items-center gap-3">
                <span class="tk-pop flex h-10 w-10 items-center justify-center rounded-full bg-emerald-500 text-white">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7"/></svg>
                </span>
                <h2 class="text-xl font-extrabold text-emerald-900">Pesanan siap diambil!</h2>
            </div>

            <div class="mt-5 flex flex-wrap items-center gap-6">
                <div class="rounded-2xl bg-white p-4 text-center shadow-sm">
                    <div class="text-xs font-semibold text-ink-500">Kode ambil</div>
                    <div class="mt-1 text-4xl font-extrabold tracking-[0.25em] text-ink-900">{{ $order->pickup_code }}</div>
                </div>

                <div id="pickup-qr-{{ $order->id }}" class="rounded-2xl bg-white p-3 shadow-sm"></div>

                <div class="min-w-[200px] flex-1 space-y-3 text-sm">
                    <div>
                        <div class="text-xs font-semibold text-emerald-800">Lokasi pengambilan</div>
                        <div class="text-lg font-extrabold text-ink-900">
                            {{ $order->storage_location ?: 'Tanyakan ke kasir' }}
                        </div>
                    </div>
                    <p class="text-emerald-900">Tunjukkan <b>kode ini</b> atau QR di samping kepada petugas toko saat mengambil pesanan.</p>
                </div>
            </div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
        <script>
            (function () {
                var el = document.getElementById('pickup-qr-{{ $order->id }}');
                if (el && typeof QRCode !== 'undefined') {
                    new QRCode(el, { text: '{{ $order->code }}|{{ $order->pickup_code }}', width: 120, height: 120 });
                } else if (el) {
                    el.remove();
                }
            })();
        </script>
    @endif
@endif