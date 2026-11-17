<x-layouts.customer title="Checkout">
    <h1 class="text-3xl font-extrabold tracking-tight">Checkout</h1>

    <form id="checkout-form" method="POST" action="{{ route('checkout.store') }}" enctype="multipart/form-data" class="mt-8 flex flex-wrap items-start gap-8">
        @csrf

        <div class="min-w-[300px] flex-[999_1_520px] space-y-8">

            {{-- 1. Desain --}}
            <section>
                <h2 class="text-lg font-extrabold">1. Unggah file desain</h2>
                <p class="mt-1 text-sm text-ink-600">PDF, PNG, JPG, atau WEBP. Maksimal 20 MB per file. Gunakan resolusi minimal 300 dpi.</p>

                <div class="mt-4 space-y-4">
                    @foreach ($lines as $line)
                        <div class="rounded-3xl border border-ink-100 bg-white p-5">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <div class="text-lg font-extrabold">{{ $line['product']->name }}</div>
                                    <div class="mt-1 text-sm text-ink-600">
                                        @foreach ($line['options'] as $group => $name)
                                            <span class="mr-3">{{ $group }}: <b class="text-ink-900">{{ $name }}</b></span>
                                        @endforeach
                                    </div>
                                    <div class="mt-1 text-sm text-ink-600">{{ number_format($line['qty'], 0, ',', '.') }} {{ $line['product']->unit }}</div>
                                    @if ($line['note'])
                                        <div class="mt-2 rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-900">Catatan: {{ $line['note'] }}</div>
                                    @endif
                                </div>
                                <div class="text-lg font-extrabold">Rp {{ number_format($line['subtotal'], 0, ',', '.') }}</div>
                            </div>

                            <label for="design-{{ $loop->index }}" class="mb-2 mt-4 block text-sm font-bold">File desain</label>
                            <input id="design-{{ $loop->index }}" type="file" name="designs[{{ $line['key'] }}]" accept=".pdf,.png,.jpg,.jpeg,.webp" required
                                   class="input file:mr-4 file:rounded-lg file:border-0 file:bg-brand-100 file:px-4 file:py-2 file:text-sm file:font-bold file:text-brand-600">
                            @error('designs.' . $line['key']) <p class="error-text">{{ $message }}</p> @enderror
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- 2. Pengambilan --}}
            <section>
                <h2 class="text-lg font-extrabold">2. Pengambilan pesanan</h2>
                @php $method = old('delivery_method', 'pickup'); @endphp

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <label class="cursor-pointer">
                        <input type="radio" name="delivery_method" value="pickup" class="peer sr-only" @checked($method === 'pickup')>
                        <span class="block rounded-2xl border-2 border-ink-200 bg-white p-5 peer-checked:border-brand-600 peer-checked:bg-brand-100 peer-focus-visible:ring-2 peer-focus-visible:ring-brand-500">
                            <span class="block font-extrabold">Ambil di toko</span>
                            <span class="mt-1 block text-sm text-ink-600">Gratis. Kami kabari saat pesanan siap.</span>
                        </span>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="delivery_method" value="delivery" class="peer sr-only" @checked($method === 'delivery')>
                        <span class="block rounded-2xl border-2 border-ink-200 bg-white p-5 peer-checked:border-brand-600 peer-checked:bg-brand-100 peer-focus-visible:ring-2 peer-focus-visible:ring-brand-500">
                            <span class="block font-extrabold">Dikirim ke alamat</span>
                            <span class="mt-1 block text-sm text-ink-600">Ongkos kirim Rp {{ number_format($shipping, 0, ',', '.') }}.</span>
                        </span>
                    </label>
                </div>
                @error('delivery_method') <p class="error-text">{{ $message }}</p> @enderror

                <div id="address-box" class="mt-4 hidden">
                    <label for="delivery_address" class="label">Alamat pengiriman</label>
                    <textarea id="delivery_address" name="delivery_address" rows="3" placeholder="Nama jalan, nomor, kecamatan, kota, kode pos" class="input resize-none">{{ old('delivery_address', auth()->user()->address) }}</textarea>
                    @error('delivery_address') <p class="error-text">{{ $message }}</p> @enderror
                </div>
            </section>

            {{-- 3. Catatan --}}
            <section>
                <h2 class="text-lg font-extrabold">3. Catatan pesanan <span class="text-sm font-semibold text-ink-500">(opsional)</span></h2>
                <label for="notes" class="sr-only">Catatan pesanan</label>
                <textarea id="notes" name="notes" rows="2" placeholder="Pesan tambahan untuk admin atau operator" class="input mt-3 resize-none">{{ old('notes') }}</textarea>
                @error('notes') <p class="error-text">{{ $message }}</p> @enderror
            </section>
        </div>

        {{-- Ringkasan --}}
        <aside class="min-w-[280px] flex-1 basis-[320px] space-y-4 rounded-3xl bg-ink-900 p-7 text-white">
            <h2 class="text-lg font-extrabold">Ringkasan pesanan</h2>
            <div class="flex justify-between text-sm text-ink-100"><span>Subtotal ({{ $lines->count() }} item)</span><span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span></div>
            <div class="flex justify-between text-sm text-ink-100"><span>Ongkos kirim</span><span id="ship-label"></span></div>
            <div class="h-px bg-ink-800"></div>
            <div class="flex items-baseline justify-between gap-3">
                <span class="font-bold">Total</span>
                <span id="total-label" class="text-3xl font-extrabold text-sun-400"></span>
            </div>
            <button type="submit" class="w-full rounded-2xl bg-magenta-600 px-6 py-4 text-base font-extrabold text-white hover:bg-white hover:text-ink-900">Buat Pesanan</button>
            <p class="text-center text-xs text-ink-100">Pembayaran dilakukan setelah pesanan dibuat.</p>
            <a href="{{ route('cart.index') }}" class="block text-center text-sm font-bold text-ink-100 hover:text-white">← Kembali ke keranjang</a>
        </aside>
    </form>

    <script>
        (function () {
            const form = document.getElementById('checkout-form');
            const box = document.getElementById('address-box');
            const subtotal = {{ $subtotal }};
            const shipping = {{ $shipping }};
            const rp = (n) => 'Rp ' + n.toLocaleString('id-ID');

            function update() {
                const delivery = form.querySelector('input[name=delivery_method]:checked').value === 'delivery';
                box.classList.toggle('hidden', !delivery);
                document.getElementById('ship-label').textContent = delivery ? rp(shipping) : 'Gratis (ambil di toko)';
                document.getElementById('total-label').textContent = rp(subtotal + (delivery ? shipping : 0));
            }

            form.querySelectorAll('input[name=delivery_method]').forEach((r) => r.addEventListener('change', update));
            update();
        })();
    </script>
</x-layouts.customer>