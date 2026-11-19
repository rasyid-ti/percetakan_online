@props(['order'])

@php
    $show = ! in_array($order->payment_status, [\App\Enums\PaymentStatus::Paid, \App\Enums\PaymentStatus::WaitingVerification], true)
        && $order->status !== \App\Enums\OrderStatus::Cancelled;
    $file = config('tintakita.qris.image', 'images/qris.png');
    $exists = file_exists(public_path($file));
    $merchant = config('tintakita.qris.merchant', config('app.name'));
@endphp

@if ($show)
    <div class="rounded-3xl border-2 border-brand-500 bg-white p-6">
        <div class="mb-5 flex flex-wrap items-center gap-3">
            <span class="rounded-full bg-magenta-100 px-3 py-1 text-xs font-bold text-magenta-600">Cara tercepat</span>
            <h3 class="text-xl font-extrabold">Bayar dengan QRIS</h3>
        </div>

        <div class="flex flex-wrap items-center gap-6">
            <div class="flex aspect-square w-56 max-w-full items-center justify-center overflow-hidden rounded-2xl border border-ink-100 bg-white p-3">
                @if ($exists)
                    <img src="{{ asset($file) }}" alt="Kode QRIS {{ $merchant }}" class="h-full w-full object-contain">
                @else
                    <div class="text-center text-xs font-semibold text-ink-500">Gambar QRIS belum dipasang.<br>Simpan di <span class="font-extrabold">public/{{ $file }}</span></div>
                @endif
            </div>

            <div class="min-w-[220px] flex-1 space-y-4">
                <div>
                    <div class="text-xs font-semibold text-ink-500">Atas nama</div>
                    <div class="font-extrabold">{{ $merchant }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold text-ink-500">Total yang harus dibayar</div>
                    <div class="text-3xl font-extrabold text-brand-600">{{ $order->total_label }}</div>
                </div>
                <ol class="list-decimal space-y-1 pl-5 text-sm text-ink-600">
                    <li>Buka aplikasi bank atau e-wallet Anda.</li>
                    <li>Pilih <b>Scan / QRIS</b>, lalu pindai kode di samping.</li>
                    <li>Masukkan nominal <b>persis sama</b> dengan total.</li>
                    <li>Simpan bukti, lalu unggah di bawah.</li>
                </ol>
            </div>
        </div>
    </div>
@endif