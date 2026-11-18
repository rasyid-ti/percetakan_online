<x-layouts.customer title="Keranjang">
    <h1 class="text-3xl font-extrabold tracking-tight">Keranjang</h1>

    @if ($lines->isEmpty())
        <div class="mt-8 rounded-3xl border border-ink-100 bg-white p-12 text-center">
            <div class="text-lg font-extrabold">Keranjang Anda masih kosong</div>
            <p class="mt-2 text-sm text-ink-600">Pilih produk dari katalog untuk mulai memesan.</p>
            <a href="{{ route('catalog') }}" class="btn-primary mt-6">Lihat Katalog</a>
        </div>
    @else
        <div class="mt-8 flex flex-wrap items-start gap-8">
            <div class="min-w-[300px] flex-[999_1_520px] space-y-4">
                @foreach ($lines as $line)
                    <div class="flex flex-wrap items-center gap-5 rounded-3xl border border-ink-100 bg-white p-5">
                        <div class="h-24 w-32 shrink-0 overflow-hidden rounded-2xl">
                            <x-product-art :compact="true" :src="$line['product']->image_url" />
                        </div>
                        <div class="min-w-[200px] flex-1">
                            <a href="{{ route('product.show', $line['product']->slug) }}" class="text-lg font-extrabold hover:text-brand-600">{{ $line['product']->name }}</a>
                            <div class="mt-1 text-sm leading-relaxed text-ink-600">
                                @foreach ($line['options'] as $group => $name)
                                    <span class="mr-3">{{ $group }}: <b class="text-ink-900">{{ $name }}</b></span>
                                @endforeach
                            </div>
                            @if ($line['note'])
                                <div class="mt-2 rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-900">Catatan: {{ $line['note'] }}</div>
                            @endif
                            <div class="mt-2 text-sm text-ink-600">
                                {{ number_format($line['qty'], 0, ',', '.') }} {{ $line['product']->unit }} × Rp {{ number_format($line['unit_price'], 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xl font-extrabold">Rp {{ number_format($line['subtotal'], 0, ',', '.') }}</div>
                            <form method="POST" action="{{ route('cart.destroy', $line['key']) }}" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-bold text-magenta-600 hover:text-ink-900">Hapus</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <aside class="min-w-[280px] flex-1 basis-[320px] space-y-4 rounded-3xl bg-ink-900 p-7 text-white">
                <h2 class="text-lg font-extrabold">Ringkasan</h2>
                <div class="flex justify-between text-sm text-ink-100"><span>{{ $lines->count() }} item</span><span>Rp {{ number_format($total, 0, ',', '.') }}</span></div>
                <div class="h-px bg-ink-800"></div>
                <div class="flex items-baseline justify-between">
                    <span class="font-bold">Total</span>
                    <span class="text-3xl font-extrabold text-sun-400">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
                <a href="{{ route('checkout.create') }}" class="block w-full rounded-2xl bg-magenta-600 px-6 py-4 text-center text-base font-extrabold text-white hover:bg-white hover:text-ink-900">Lanjut ke Checkout</a>
                <a href="{{ route('catalog') }}" class="block text-center text-sm font-bold text-ink-100 hover:text-white">+ Tambah produk lain</a>
            </aside>
        </div>
    @endif
</x-layouts.customer>