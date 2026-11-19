<x-layouts.panel :title="$order->code">
    <a href="{{ route('admin.orders.index') }}" class="text-sm font-bold text-brand-600 hover:text-ink-900">← Semua pesanan</a>

    <div class="mt-4 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight">{{ $order->code }}</h1>
            <div class="mt-1 text-sm text-ink-500">
                Dibuat {{ $order->created_at->translatedFormat('d F Y, H:i') }}
                @if ($order->deadline) · Tenggat {{ $order->deadline->translatedFormat('d F Y') }} @endif
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <x-status-badge :status="$order->status" />
            <x-status-badge :status="$order->payment_status" />
        </div>
    </div>

    <div class="mt-8 flex flex-wrap items-start gap-8">

        {{-- Kiri --}}
        <div class="min-w-[300px] flex-[999_1_520px] space-y-8">

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
                                    <a href="{{ route('admin.orders.design', [$order->code, $item->id]) }}" target="_blank" class="mt-1 inline-block text-sm font-bold text-brand-600 hover:text-ink-900">Buka file desain</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
                @if ($order->notes)
                    <div class="rounded-2xl bg-white p-5 text-sm ring-1 ring-ink-100"><b>Catatan pesanan:</b> {{ $order->notes }}</div>
                @endif
            </section>

            <section class="space-y-4">
                <h2 class="text-lg font-extrabold">Pembayaran</h2>
                @forelse ($payments as $payment)
                    <div class="rounded-3xl border border-ink-100 bg-white p-5">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <div class="text-lg font-extrabold">Rp {{ number_format($payment->amount, 0, ',', '.') }} <span class="text-sm font-semibold text-ink-500">· {{ ucfirst($payment->method) }}</span></div>
                                <div class="mt-1 text-xs text-ink-500">Diunggah {{ $payment->created_at->translatedFormat('d F Y, H:i') }}</div>
                                @if ($payment->note)
                                    <div class="mt-2 text-sm text-ink-600">Catatan: {{ $payment->note }}</div>
                                @endif
                                @if ($payment->verifier)
                                    <div class="mt-1 text-xs text-ink-500">Diperiksa oleh {{ $payment->verifier->name }}</div>
                                @endif
                            </div>
                            <div class="text-right">
                                <x-status-badge :status="$payment->status" />
                                @if ($payment->proof)
                                    <a href="{{ route('admin.payments.proof', $payment->id) }}" target="_blank" class="mt-2 block text-sm font-bold text-brand-600 hover:text-ink-900">Buka bukti bayar</a>
                                @endif
                            </div>
                        </div>

                        @if ($payment->status === \App\Enums\PaymentStatus::WaitingVerification && $order->status === \App\Enums\OrderStatus::AwaitingPayment)
                            <div class="mt-5 flex flex-wrap items-start gap-4 border-t border-ink-100 pt-5">
                                <form method="POST" action="{{ route('admin.orders.approve', [$order->code, $payment->id]) }}">
                                    @csrf
                                    <button type="submit" class="rounded-xl bg-emerald-700 px-6 py-3 text-sm font-bold text-white hover:bg-emerald-800">Setujui Pembayaran</button>
                                </form>

                                <form method="POST" action="{{ route('admin.orders.reject', [$order->code, $payment->id]) }}" class="min-w-[260px] flex-1 space-y-2">
                                    @csrf
                                    <label for="reason-{{ $payment->id }}" class="sr-only">Alasan penolakan</label>
                                    <input id="reason-{{ $payment->id }}" name="reason" type="text" required placeholder="Alasan penolakan, contoh: nominal tidak sesuai" class="input">
                                    @error('reason') <p class="error-text">{{ $message }}</p> @enderror
                                    <button type="submit" class="rounded-xl border-2 border-rose-700 px-6 py-2.5 text-sm font-bold text-rose-700 hover:bg-rose-700 hover:text-white">Tolak Pembayaran</button>
                                </form>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="rounded-3xl border border-ink-100 bg-white p-6 text-sm text-ink-600">Pelanggan belum mengunggah bukti pembayaran.</div>
                @endforelse
            </section>

            <section>
                <h2 class="text-lg font-extrabold">Riwayat status</h2>
                <ol class="mt-4 space-y-4 rounded-3xl border border-ink-100 bg-white p-6">
                    @foreach ($order->statusLogs as $log)
                        <li class="flex gap-4">
                            <span class="mt-1.5 h-3 w-3 shrink-0 rounded-full bg-brand-600"></span>
                            <div>
                                <div class="text-sm font-bold">{{ $log->note }}</div>
                                <div class="text-xs text-ink-500">{{ $log->created_at->translatedFormat('d F Y, H:i') }} @if ($log->user) · {{ $log->user->name }} @endif</div>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </section>
        </div>

        {{-- Kanan --}}
        <aside class="min-w-[280px] flex-1 basis-[340px] space-y-5">

            <div class="rounded-3xl border border-ink-100 bg-white p-6">
                <h2 class="text-lg font-extrabold">Pelanggan</h2>
                <div class="mt-3 text-sm">
                    <div class="font-bold">{{ $order->user->name }}</div>
                    <div class="text-ink-600">{{ $order->user->email }}</div>
                    @if ($order->user->phone)<div class="text-ink-600">{{ $order->user->phone }}</div>@endif
                </div>
                <div class="mt-4 rounded-xl bg-ink-50 p-4 text-sm">
                    <div class="font-bold">{{ $order->delivery_method === 'delivery' ? 'Dikirim ke alamat' : 'Ambil di toko' }}</div>
                    @if ($order->delivery_address)<div class="mt-1 text-ink-600">{{ $order->delivery_address }}</div>@endif
                </div>
            </div>

            <div class="space-y-3 rounded-3xl bg-ink-900 p-6 text-white">
                <div class="flex justify-between text-sm text-ink-100"><span>Subtotal</span><span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
                <div class="flex justify-between text-sm text-ink-100"><span>Ongkos kirim</span><span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span></div>
                <div class="h-px bg-ink-800"></div>
                <div class="flex items-baseline justify-between">
                    <span class="font-bold">Total</span>
                    <span class="text-3xl font-extrabold text-sun-400">{{ $order->total_label }}</span>
                </div>
            </div>

            <div class="rounded-3xl border border-ink-100 bg-white p-6">
                <h2 class="text-lg font-extrabold">Operator produksi</h2>

                @if ($isOpen)
                    <form method="POST" action="{{ route('admin.orders.assign', $order->code) }}" class="mt-3 space-y-3">
                        @csrf
                        <label for="operator_id" class="sr-only">Pilih operator</label>
                        <select id="operator_id" name="operator_id" class="input">
                            <option value="">Belum ditugaskan</option>
                            @foreach ($operators as $operator)
                                <option value="{{ $operator->id }}" @selected($order->operator_id === $operator->id)>{{ $operator->name }}</option>
                            @endforeach
                        </select>
                        @error('operator_id') <p class="error-text">{{ $message }}</p> @enderror
                        <button type="submit" class="btn-primary w-full">Simpan Penugasan</button>
                    </form>
                @else
                    <p class="mt-3 text-sm text-ink-600">{{ $order->operator?->name ?? 'Tidak ada operator' }}</p>
                @endif
            </div>

            {{-- Siap diambil / dikirim --}}
            @if ($order->status === \App\Enums\OrderStatus::Ready)
                @php
                    $wa = preg_replace('/\D+/', '', (string) $order->user->phone);
                    if (str_starts_with($wa, '0')) { $wa = '62' . substr($wa, 1); }
                    $isPickup = $order->delivery_method !== 'delivery';
                    $waText = "Halo {$order->user->name}, pesanan {$order->code} sudah siap "
                        . ($isPickup ? 'diambil' : 'dikirim')
                        . ($order->storage_location ? ". Lokasi: {$order->storage_location}" : '')
                        . ($isPickup ? ". Kode ambil: {$order->pickup_code}" : '')
                        . '. Terima kasih, TintaKita.';
                @endphp

                <div class="space-y-4 rounded-3xl border-2 border-emerald-500 bg-white p-6">
                    <h2 class="text-lg font-extrabold">{{ $isPickup ? 'Siap diambil' : 'Siap dikirim' }}</h2>

                    @if ($isPickup)
                        <form method="POST" action="{{ route('admin.orders.location', $order->code) }}" class="space-y-2">
                            @csrf
                            <label for="storage_location" class="label">Lokasi simpan</label>
                            <input id="storage_location" name="storage_location" type="text" maxlength="60" value="{{ old('storage_location', $order->storage_location) }}" placeholder="Contoh: Rak B-3" class="input">
                            @error('storage_location') <p class="error-text">{{ $message }}</p> @enderror
                            <button type="submit" class="btn-primary w-full">Simpan Lokasi</button>
                        </form>
                    @endif

                    @if ($wa)
                        <a href="https://wa.me/{{ $wa }}?text={{ rawurlencode($waText) }}" target="_blank" rel="noopener"
                           class="flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-sm font-bold text-white hover:bg-emerald-700">
                            Kabari via WhatsApp
                        </a>
                    @else
                        <p class="text-xs text-ink-500">Pelanggan belum mengisi nomor telepon, jadi tombol WhatsApp tidak tersedia.</p>
                    @endif
                </div>
            @endif

            @if ($isOpen)
                <div class="space-y-3 rounded-3xl border border-ink-100 bg-white p-6">
                    <h2 class="text-lg font-extrabold">Tindakan</h2>

                    @if ($order->status === \App\Enums\OrderStatus::Ready)
                        @if ($order->delivery_method === 'delivery')
                            <form method="POST" action="{{ route('admin.orders.complete', $order->code) }}">
                                @csrf
                                <button type="submit" class="w-full rounded-xl bg-emerald-700 px-6 py-3 text-sm font-bold text-white hover:bg-emerald-800">Tandai Terkirim</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.orders.handover', $order->code) }}" class="space-y-2">
                                @csrf
                                <label for="pickup_code" class="label">Kode ambil dari pelanggan</label>
                                <input id="pickup_code" name="pickup_code" type="text" maxlength="6" autocomplete="off" placeholder="6 karakter" class="input text-center text-lg font-extrabold uppercase tracking-[0.3em]">
                                @error('pickup_code') <p class="error-text">{{ $message }}</p> @enderror
                                <button type="submit" class="w-full rounded-xl bg-emerald-700 px-6 py-3 text-sm font-bold text-white hover:bg-emerald-800">Serahkan ke Pelanggan</button>
                            </form>
                        @endif
                    @endif

                    <form method="POST" action="{{ route('admin.orders.cancel', $order->code) }}" onsubmit="return confirm('Yakin membatalkan pesanan ini?')">
                        @csrf
                        <button type="submit" class="w-full rounded-xl border-2 border-rose-700 px-6 py-3 text-sm font-bold text-rose-700 hover:bg-rose-700 hover:text-white">Batalkan Pesanan</button>
                    </form>
                </div>
            @endif
        </aside>
    </div>
</x-layouts.panel>