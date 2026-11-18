<x-layouts.panel title="Antrean Produksi">
    <div class="flex flex-col gap-6">

        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight">Antrean Produksi</h1>
                <div class="mt-1 text-sm text-ink-500">Pesanan lunas yang ditugaskan kepada Anda</div>
            </div>
            <div class="flex flex-wrap gap-2.5">
                <span class="rounded-full border border-ink-100 bg-white px-4 py-2.5 text-sm font-bold">{{ $total }} tugas aktif</span>
                @if ($urgent > 0)
                    <span class="rounded-full bg-magenta-100 px-4 py-2.5 text-sm font-bold text-magenta-600">{{ $urgent }} tenggat hari ini / terlambat</span>
                @endif
            </div>
        </div>

        @if ($total === 0)
            <div class="rounded-3xl border border-ink-100 bg-white p-12 text-center">
                <div class="text-lg font-extrabold">Belum ada tugas</div>
                <p class="mt-2 text-sm text-ink-600">Pesanan lunas yang ditugaskan admin kepada Anda akan muncul di sini.</p>
            </div>
        @endif

        <div class="flex flex-wrap items-start gap-5">
            @foreach ($columns as $col)
                <section class="min-w-[250px] flex-1 basis-[250px] space-y-3.5 rounded-3xl bg-ink-100 p-4">
                    <div class="flex items-center gap-2.5 px-1.5 py-0.5">
                        <span class="h-3 w-3 rounded-full {{ $col['dot'] }}"></span>
                        <span class="font-extrabold">{{ $col['title'] }}</span>
                        <span class="ml-auto flex h-6 min-w-6 items-center justify-center rounded-full bg-white px-1.5 text-xs font-extrabold">{{ $col['orders']->count() }}</span>
                    </div>

                    @forelse ($col['orders'] as $order)
                        @php
                            $isToday = $order->deadline?->isToday();
                            $isLate = $order->deadline && ! $isToday && $order->deadline->isPast();
                        @endphp
                        <div class="space-y-3 rounded-2xl border border-ink-100 bg-white p-4">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-bold text-ink-500">{{ $order->code }}</span>
                                @if ($order->status !== \App\Enums\OrderStatus::Ready)
                                    @if ($isToday)
                                        <span class="rounded-full bg-magenta-600 px-2.5 py-0.5 text-[11px] font-extrabold text-white">HARI INI</span>
                                    @elseif ($isLate)
                                        <span class="rounded-full bg-red-700 px-2.5 py-0.5 text-[11px] font-extrabold text-white">TERLAMBAT</span>
                                    @endif
                                @endif
                            </div>

                            @foreach ($order->items as $item)
                                <div>
                                    <div class="font-extrabold leading-snug">{{ $item->product_name }} · {{ number_format($item->qty, 0, ',', '.') }}</div>
                                    <div class="mt-0.5 text-[13px] leading-relaxed text-ink-600">{{ collect($item->options)->implode(' · ') }}</div>
                                    @if ($item->design_notes)
                                        <div class="mt-1.5 rounded-lg bg-amber-50 px-3 py-2 text-xs leading-relaxed text-amber-900">Catatan: {{ $item->design_notes }}</div>
                                    @endif
                                    @if ($item->design_file)
                                        <a href="{{ route('operator.design', [$order->code, $item->id]) }}" target="_blank" class="mt-1.5 inline-block text-xs font-bold text-brand-600 hover:text-ink-900">Unduh desain</a>
                                    @endif
                                </div>
                            @endforeach

                            @if ($order->notes)
                                <div class="rounded-lg bg-amber-50 px-3 py-2 text-xs leading-relaxed text-amber-900">Catatan pesanan: {{ $order->notes }}</div>
                            @endif

                            <div class="flex flex-wrap items-center justify-between gap-1 text-xs font-semibold text-ink-600">
                                <span>{{ $order->user->name }} · {{ $order->delivery_method === 'delivery' ? 'Dikirim' : 'Ambil di toko' }}</span>
                                <span>Tenggat {{ $order->deadline?->translatedFormat('d M') ?? '-' }}</span>
                            </div>

                            <form method="POST" action="{{ route('operator.advance', $order->code) }}">
                                @csrf
                                <button type="submit" class="w-full rounded-xl bg-brand-600 px-4 py-3 text-sm font-bold text-white hover:bg-ink-900">{{ $col['action'] }}</button>
                            </form>
                        </div>
                    @empty
                        <div class="rounded-2xl border-2 border-dashed border-ink-200 px-4 py-8 text-center text-sm font-semibold text-ink-500">Kosong</div>
                    @endforelse
                </section>
            @endforeach
        </div>
    </div>
</x-layouts.panel>