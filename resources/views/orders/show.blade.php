@php
    $payment = $order->latestPayment;
@endphp

<x-layouts.customer :title="$order->code">
    <a href="{{ route('orders.index') }}" class="text-sm font-bold text-brand-600 hover:text-ink-900">← Semua pesanan</a>

    <div class="mt-4 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight">{{ $order->code }}</h1>
            <div class="mt-1 text-sm text-ink-500">Dibuat {{ $order->created_at->translatedFormat('d F Y, H:i') }}
                @if ($order->deadline) · Estimasi selesai {{ $order->deadline->translatedFormat('d F Y') }} @endif
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <x-status-badge :status="$order->status" />
            <x-status-badge :status="$order->payment_status" />
        </div>
    </div>

    {{-- QRIS: tampil paling atas saat belum dibayar --}}
    <div class="mt-8 empty:hidden">
        <x-qris-panel :order="$order" />
    </div>

    <div class="mt-8 flex flex-wrap items-start gap-8">

        {{-- Kiri: pelacak, item, dan linimasa --}}
        <div class="min-w-[300px] flex-[999_1_520px] space-y-8">

            <section class="rounded-3xl border border-ink-100 bg-white p-6">
                <h2 class="mb-5 text-lg font-extrabold">Status pesanan</h2>
                <x-order-tracker :order="$order" />
            </section>

            <section class="space-y-4">
                <h2 class="text-lg font-extrabold">Item pesanan</h2>
                @foreach ($order->items as $item)
                    <div class="rounded-3xl border border-ink-100 bg-white p-5">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <div class="text-lg font-extrabold">{{ $item->product_name }}</div>
                                <div class="mt-1 text-sm text-ink-600">
                                    @foreach ($item->options ?? [] as $group => $name)
                                        <span class="mr-3">{{ $group }}: <b class="text-ink-900">{{ $name }}</b></span>
                                    @endforeach
                                </div>
                                <div class="mt-1 text-sm text-ink-600">{{ number_format($item->qty, 0, ',', '.') }} × Rp {{ number_format($item->unit_price, 0, ',', '.') }}</div>
                                @if ($item->design_notes)
                                    <div class="mt-2 rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-900">Catatan: {{ $item->design_notes }}</div>
                                @endif
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-extrabold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                                @if ($item->design_file)
                                    <a href="{{ route('orders.design', [$order->code, $item->id]) }}" target="_blank" class="mt-1 inline-block text-sm font-bold text-brand-600 hover:text-ink-900">Lihat file desain</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </section>

            <section>
                <h2 class="text-lg font-extrabold">Riwayat status</h2>
                <ol class="mt-4 space-y-4 rounded-3xl border border-ink-100 bg-white p-6">
                    @foreach ($order->statusLogs as $log)
                        <li class="flex gap-4">
                            <span class="mt-1.5 h-3 w-3 shrink-0 rounded-full bg-brand-600"></span>
                            <div>
                                <div class="text-sm font-bold">{{ $log->note }}</div>
                                <div class="text-xs text-ink-500">{{ $log->created_at->translatedFormat('d F Y, H:i') }}</div>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </section>
        </div>

        {{-- Kanan: ringkasan dan pembayaran --}}
        <aside class="min-w-[280px] flex-1 basis-[340px] space-y-5">

            <div class="space-y-3 rounded-3xl bg-ink-900 p-7 text-white">
                <div class="flex justify-between text-sm text-ink-100"><span>Subtotal</span><span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
                <div class="flex justify-between text-sm text-ink-100"><span>Ongkos kirim</span><span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span></div>
                <div class="h-px bg-ink-800"></div>
                <div class="flex items-baseline justify-between">
                    <span class="font-bold">Total</span>
                    <span class="text-3xl font-extrabold text-sun-400">{{ $order->total_label }}</span>
                </div>
                <div class="rounded-xl bg-ink-800 p-4 text-sm text-ink-100">
                    <div class="font-bold text-white">{{ $order->delivery_method === 'delivery' ? 'Dikirim ke alamat' : 'Ambil di toko' }}</div>
                    @if ($order->delivery_address)<div class="mt-1">{{ $order->delivery_address }}</div>@endif
                </div>
            </div>

            {{-- Pembayaran --}}
            <div class="rounded-3xl border border-ink-100 bg-white p-6">
                <h2 class="text-lg font-extrabold">Pembayaran</h2>

                @if ($order->status === \App\Enums\OrderStatus::Cancelled)
                    <p class="mt-3 text-sm text-ink-600">Pesanan ini sudah dibatalkan.</p>

                @elseif ($order->payment_status === \App\Enums\PaymentStatus::Paid)
                    <p class="mt-3 rounded-xl bg-emerald-50 p-4 text-sm font-bold text-emerald-800">Pembayaran lunas. Pesanan Anda masuk antrean produksi.</p>

                @elseif ($order->payment_status === \App\Enums\PaymentStatus::WaitingVerification)
                    <p class="mt-3 rounded-xl bg-amber-50 p-4 text-sm font-bold text-amber-900">Bukti pembayaran sudah dikirim. Admin sedang memeriksanya.</p>
                    @if ($payment && $payment->proof)
                        <a href="{{ route('orders.proof', $order->code) }}" target="_blank" class="mt-3 inline-block text-sm font-bold text-brand-600 hover:text-ink-900">Lihat bukti yang diunggah</a>
                    @endif

                @elseif ($canPay)
                    @if ($order->payment_status === \App\Enums\PaymentStatus::Rejected)
                        <p class="mt-3 rounded-xl bg-rose-50 p-4 text-sm font-bold text-rose-800">
                            Pembayaran sebelumnya ditolak.
                            @if ($payment && $payment->note)<span class="block font-medium">Alasan: {{ $payment->note }}</span>@endif
                            Silakan unggah bukti yang benar.
                        </p>
                    @endif

                    <div class="mt-4 space-y-3 text-sm">
                        <div class="rounded-xl bg-ink-50 p-4">
                            <div class="font-extrabold">Atau transfer bank</div>
                            <div class="mt-1 text-ink-600">{{ config('tintakita.bank.name') }}</div>
                            <div class="text-lg font-extrabold">{{ config('tintakita.bank.number') }}</div>
                            <div class="text-ink-600">a.n. {{ config('tintakita.bank.holder') }}</div>
                        </div>
                        <div class="font-bold">Jumlah yang harus dibayar: <span class="text-brand-600">{{ $order->total_label }}</span></div>
                    </div>

                    <form method="POST" action="{{ route('orders.pay', $order->code) }}" enctype="multipart/form-data" class="mt-5 space-y-4">
                        @csrf
                        <div>
                            <label for="method" class="label">Metode pembayaran</label>
                            <select id="method" name="method" class="input">
                                <option value="qris" @selected(old('method') === 'qris')>QRIS</option>
                                <option value="transfer" @selected(old('method') === 'transfer')>Transfer bank</option>
                            </select>
                            @error('method') <p class="error-text">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="proof" class="label">Bukti pembayaran</label>
                            <input id="proof" type="file" name="proof" accept=".jpg,.jpeg,.png,.webp,.pdf" required
                                   class="input file:mr-4 file:rounded-lg file:border-0 file:bg-brand-100 file:px-4 file:py-2 file:text-sm file:font-bold file:text-brand-600">
                            @error('proof') <p class="error-text">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="note" class="label">Catatan <span class="font-medium text-ink-500">(opsional)</span></label>
                            <input id="note" name="note" type="text" value="{{ old('note') }}" placeholder="Contoh: transfer atas nama Andi" class="input">
                            @error('note') <p class="error-text">{{ $message }}</p> @enderror
                        </div>
                        <button type="submit" class="btn-primary w-full py-3.5">Kirim Bukti Pembayaran</button>
                    </form>

                    <form method="POST" action="{{ route('orders.cancel', $order->code) }}" class="mt-4" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')">
                        @csrf
                        <button type="submit" class="w-full text-center text-sm font-bold text-rose-600 hover:text-ink-900">Batalkan pesanan</button>
                    </form>
                @endif
            </div>
        </aside>
    </div>
</x-layouts.customer>