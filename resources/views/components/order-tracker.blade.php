@props(['order'])

@php
    $status = $order->status->value;
    $pay = match ($order->payment_status) {
        \App\Enums\PaymentStatus::Paid => 'paid',
        \App\Enums\PaymentStatus::WaitingVerification => 'waiting_verification',
        \App\Enums\PaymentStatus::Rejected => 'rejected',
        default => 'unpaid',
    };
    $cancelled = $order->status === \App\Enums\OrderStatus::Cancelled;

    $map = ['awaiting_payment' => 1, 'processing' => 2, 'printing' => 3, 'finishing' => 4, 'ready' => 5, 'completed' => 6];
    $current = $map[$status] ?? 1;

    $steps = [
        ['Pesanan dibuat', 'Pesanan Anda sudah kami terima.'],
        ['Pembayaran', ''],
        ['Cek file & antrean', 'Tim kami memeriksa file desain Anda.'],
        ['Sedang dicetak', 'Desain Anda sedang naik mesin cetak.'],
        ['Finishing', 'Pemotongan, laminasi, dan perapian akhir.'],
        ['Siap diambil / dikirim', 'Pesanan selesai diproduksi dan siap.'],
        ['Selesai', 'Pesanan sudah Anda terima. Terima kasih!'],
    ];

    $payNote = match ($pay) {
        'unpaid' => 'Menunggu pembayaran Anda.',
        'waiting_verification' => 'Admin sedang memeriksa bukti pembayaran Anda.',
        'rejected' => 'Bukti pembayaran ditolak. Silakan unggah ulang.',
        default => 'Pembayaran sudah terverifikasi.',
    };

    $autoRefresh = $pay === 'waiting_verification' || in_array($status, ['processing', 'printing', 'finishing'], true);
@endphp

@if ($cancelled)
    <div class="flex items-center gap-4 rounded-2xl bg-red-50 p-5 text-red-800 ring-1 ring-red-200">
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-500 text-white">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
        </span>
        <div>
            <div class="font-extrabold">Pesanan dibatalkan</div>
            <div class="text-sm">Pesanan ini tidak diproses lebih lanjut.</div>
        </div>
    </div>
@else
    <ol class="m-0 list-none p-0" aria-label="Status pesanan">
        @foreach ($steps as $i => [$title, $desc])
            @php
                $state = $i < $current ? 'done' : ($i === $current ? 'active' : 'todo');

                if ($current === 6 && $i === 6) {
                    $state = 'done';
                }
                if ($i === 1 && $current === 1) {
                    $state = match ($pay) {
                        'rejected' => 'error',
                        'unpaid' => 'wait',
                        default => 'active',
                    };
                }
                if ($i === 5 && $current === 5) {
                    $state = 'ready';
                }
                if ($i === 1) {
                    $desc = $payNote;
                }
            @endphp

            <li class="relative pb-8 pl-14 last:pb-0">
                @unless ($loop->last)
                    <span class="absolute left-[19px] top-10 bottom-0 w-0.5 {{ $state === 'done' ? 'bg-emerald-400' : 'bg-ink-100' }}"></span>
                @endunless

                <span class="absolute left-0 top-0">
                    @if ($state === 'done')
                        <span class="tk-pop flex h-10 w-10 items-center justify-center rounded-full bg-emerald-500 text-white">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7"/></svg>
                        </span>
                    @elseif ($state === 'active')
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-100">
                            <span class="tk-spin h-5 w-5 rounded-full border-[3px] border-brand-600/25 border-t-brand-600"></span>
                        </span>
                    @elseif ($state === 'ready')
                        <span class="relative flex h-10 w-10 items-center justify-center">
                            <span class="absolute inset-0 animate-ping rounded-full bg-emerald-400/50"></span>
                            <span class="relative flex h-10 w-10 items-center justify-center rounded-full bg-emerald-500 text-white">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7"/></svg>
                            </span>
                        </span>
                    @elseif ($state === 'wait')
                        <span class="flex h-10 w-10 animate-pulse items-center justify-center rounded-full bg-amber-100 text-amber-700">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                        </span>
                    @elseif ($state === 'error')
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-red-500 text-white">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
                        </span>
                    @else
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-ink-100 text-sm font-extrabold text-ink-500">{{ $i + 1 }}</span>
                    @endif
                </span>

                <div class="pt-1.5">
                    <div @class([
                        'text-base',
                        'font-extrabold text-ink-900' => in_array($state, ['active', 'ready', 'wait', 'error'], true),
                        'font-bold text-ink-900' => $state === 'done',
                        'font-semibold text-ink-500' => $state === 'todo',
                    ])>{{ $title }}</div>
                    @if ($state !== 'todo')
                        <div @class([
                            'mt-0.5 text-sm',
                            'text-ink-600' => ! in_array($state, ['error', 'ready'], true),
                            'font-semibold text-red-700' => $state === 'error',
                            'font-semibold text-emerald-700' => $state === 'ready',
                        ])>{{ $desc }}</div>
                    @endif
                </div>
            </li>
        @endforeach
    </ol>

    @if ($autoRefresh)
        <script>
            setTimeout(function () { if (!document.hidden) location.reload(); }, 30000);
        </script>
    @endif
@endif