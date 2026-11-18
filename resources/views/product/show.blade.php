<x-layouts.public :title="$product->name">
    <div class="mx-auto max-w-7xl px-6 py-10 lg:px-12">

        <div class="text-sm text-ink-500">
            <a href="{{ route('catalog') }}" class="hover:text-ink-900">Katalog</a> /
            <a href="{{ route('catalog', ['kategori' => $product->category->slug]) }}" class="hover:text-ink-900">{{ $product->category->name }}</a> /
            <span class="font-bold text-ink-900">{{ $product->name }}</span>
        </div>

        <div class="mt-6 grid gap-10 lg:grid-cols-2">

            {{-- Kiri --}}
            <div class="space-y-5">
                <div class="relative overflow-hidden rounded-[28px]">
                    <x-product-art :tall="true" :src="$product->image_url" />
                    <span class="absolute left-5 top-5 rounded-full bg-white px-3.5 py-1.5 text-xs font-bold">Pratinjau ilustrasi</span>
                </div>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="rounded-2xl bg-white p-5 ring-1 ring-ink-100">
                        <div class="font-semibold text-ink-500">Minimal pesan</div>
                        <div class="mt-1 text-lg font-extrabold">{{ $product->min_qty }} {{ $product->unit }}</div>
                    </div>
                    <div class="rounded-2xl bg-white p-5 ring-1 ring-ink-100">
                        <div class="font-semibold text-ink-500">Estimasi produksi</div>
                        <div class="mt-1 text-lg font-extrabold">{{ $product->estimated_days }} hari kerja</div>
                    </div>
                </div>
            </div>

            {{-- Kanan --}}
            <form id="config-form" method="POST" action="{{ route('cart.store', $product->slug) }}" class="space-y-6">
                @csrf

                <div>
                    <span class="rounded-full bg-magenta-100 px-3 py-1 text-xs font-bold text-magenta-600">{{ $product->category->name }}</span>
                    <h1 class="mt-3 text-4xl font-extrabold tracking-tight">{{ $product->name }}</h1>
                    <p class="mt-2 leading-relaxed text-ink-600">{{ $product->description }}</p>
                </div>

                <div class="space-y-7 rounded-3xl border border-ink-100 bg-white p-7">
                    @foreach ($groups as $group => $options)
                        @php $selectedId = (int) old("options.{$group}", $options->first()->id); @endphp
                        <fieldset>
                            <legend class="mb-3 text-[15px] font-extrabold">{{ $group }}</legend>
                            <div class="flex flex-wrap gap-2.5">
                                @foreach ($options as $option)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="options[{{ $group }}]" value="{{ $option->id }}" data-extra="{{ $option->extra_price }}" class="peer sr-only" @checked($selectedId === $option->id)>
                                        <span class="block rounded-xl border-2 border-ink-200 bg-white px-4 py-3 text-sm font-bold peer-checked:border-brand-600 peer-checked:bg-brand-100 peer-focus-visible:ring-2 peer-focus-visible:ring-brand-500">
                                            {{ $option->name }}
                                            <span class="mt-0.5 block text-xs font-semibold text-ink-500">
                                                {{ $option->extra_price > 0 ? '+Rp ' . number_format($option->extra_price, 0, ',', '.') . ' / ' . $product->unit : 'Termasuk' }}
                                            </span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>
                    @endforeach
                    @error('options') <p class="error-text">{{ $message }}</p> @enderror

                    <div>
                        <label for="qty" class="mb-3 block text-[15px] font-extrabold">
                            Jumlah <span class="font-semibold text-ink-500">(kelipatan {{ $product->min_qty }} {{ $product->unit }})</span>
                        </label>
                        <div class="inline-flex items-center overflow-hidden rounded-xl border-2 border-ink-200">
                            <button type="button" id="qty-minus" aria-label="Kurangi jumlah" class="h-12 w-12 bg-ink-50 text-2xl font-bold hover:bg-ink-100">−</button>
                            <input id="qty" name="qty" type="number" min="{{ $product->min_qty }}" step="{{ $product->min_qty }}" value="{{ old('qty', $product->min_qty) }}" class="h-12 w-28 border-0 text-center text-lg font-extrabold focus:outline-none">
                            <button type="button" id="qty-plus" aria-label="Tambah jumlah" class="h-12 w-12 bg-ink-50 text-2xl font-bold hover:bg-ink-100">+</button>
                        </div>
                        @error('qty') <p class="error-text">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="note" class="mb-3 block text-[15px] font-extrabold">Catatan untuk operator <span class="font-semibold text-ink-500">(opsional)</span></label>
                        <textarea id="note" name="note" rows="2" placeholder="Contoh: warna navy harus sama dengan logo" class="input resize-none">{{ old('note') }}</textarea>
                        @error('note') <p class="error-text">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Ringkasan harga --}}
                <div class="space-y-4 rounded-3xl bg-ink-900 p-7 text-white">
                    <div class="flex justify-between text-sm text-ink-100"><span>Harga satuan</span><span id="unit-label"></span></div>
                    <div class="flex justify-between text-sm text-ink-100"><span>Jumlah</span><span id="qty-label"></span></div>
                    <div class="h-px bg-ink-800"></div>
                    <div class="flex items-baseline justify-between gap-3">
                        <span class="font-bold">Total</span>
                        <span id="total-label" class="text-4xl font-extrabold text-sun-400"></span>
                    </div>

                    @auth
                        @if (auth()->user()->isCustomer())
                            <button type="submit" class="w-full rounded-2xl bg-magenta-600 px-6 py-4 text-base font-extrabold text-white hover:bg-white hover:text-ink-900">Masukkan ke Keranjang</button>
                        @else
                            <p class="rounded-xl bg-ink-800 p-4 text-sm text-ink-100">Anda masuk sebagai {{ auth()->user()->role->label() }}. Pemesanan hanya untuk akun pelanggan.</p>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="block w-full rounded-2xl bg-magenta-600 px-6 py-4 text-center text-base font-extrabold text-white hover:bg-white hover:text-ink-900">Masuk untuk Memesan</a>
                        <p class="text-center text-xs text-ink-100">Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-white underline">Daftar gratis</a></p>
                    @endauth
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const form = document.getElementById('config-form');
            const qty = document.getElementById('qty');
            const base = {{ $product->base_price }};
            const min = {{ $product->min_qty }};
            const unitName = @json($product->unit);
            const rp = (n) => 'Rp ' + n.toLocaleString('id-ID');

            function update() {
                let unit = base;
                form.querySelectorAll('input[type=radio]:checked').forEach((r) => {
                    unit += Number(r.dataset.extra);
                });
                const q = Math.max(parseInt(qty.value, 10) || 0, 0);
                document.getElementById('unit-label').textContent = rp(unit) + ' / ' + unitName;
                document.getElementById('qty-label').textContent = q.toLocaleString('id-ID') + ' ' + unitName;
                document.getElementById('total-label').textContent = rp(unit * q);
            }

            document.getElementById('qty-minus').addEventListener('click', () => {
                qty.value = Math.max(min, (parseInt(qty.value, 10) || min) - min);
                update();
            });
            document.getElementById('qty-plus').addEventListener('click', () => {
                qty.value = (parseInt(qty.value, 10) || 0) + min;
                update();
            });
            form.addEventListener('input', update);
            update();
        })();
    </script>
</x-layouts.public>