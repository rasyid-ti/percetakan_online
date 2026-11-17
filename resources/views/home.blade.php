@php
    $steps = [
        ['1', 'Pilih produk', 'Tentukan ukuran, bahan, dan finishing. Harga dihitung langsung.'],
        ['2', 'Unggah desain', 'Kirim file desain Anda dan tambahkan catatan untuk operator.'],
        ['3', 'Bayar & verifikasi', 'Transfer atau QRIS, unggah bukti, admin memverifikasi.'],
        ['4', 'Ambil atau kirim', 'Pantau status cetak sampai pesanan siap diambil atau dikirim.'],
    ];
@endphp

<x-layouts.public title="Beranda">

    {{-- HERO --}}
    <section class="mx-auto flex max-w-7xl flex-wrap items-center gap-14 px-6 py-16 lg:px-12 lg:py-20">
        <div class="flex min-w-[300px] flex-1 basis-[460px] flex-col gap-7">
            <div class="flex w-fit items-center gap-2.5 rounded-full border border-ink-100 bg-white px-4 py-2 text-sm font-semibold">
                <span class="h-2.5 w-2.5 rounded-full bg-magenta-500"></span>
                Percetakan online untuk bisnis &amp; personal
            </div>
            <h1 class="text-5xl font-extrabold leading-[1.05] tracking-tight lg:text-7xl">
                Cetak Mudah,<br><span class="text-brand-600">Kapan Saja.</span>
            </h1>
            <p class="max-w-lg text-lg leading-relaxed text-ink-600">
                Pilih produk, unggah desain, bayar, lalu pantau proses cetaknya langsung dari satu tempat. Tanpa antre, tanpa ribet.
            </p>
            <div class="flex flex-wrap gap-3.5">
                <a href="{{ route('catalog') }}" class="btn-primary !rounded-2xl !px-8 !py-4 !text-base">Mulai Pesan</a>
                <a href="#cara-pesan" class="inline-flex items-center rounded-2xl border-2 border-ink-900 px-7 py-4 text-base font-bold hover:bg-ink-900 hover:text-white">Lihat Cara Pesan</a>
            </div>
        </div>

        <div class="relative min-h-[460px] min-w-[300px] flex-1 basis-[420px] overflow-hidden rounded-[36px] bg-ink-900">
            <div class="absolute -left-10 top-16 h-8 w-64 rounded-full bg-brand-500"></div>
            <div class="absolute -left-16 top-28 h-8 w-72 rounded-full bg-magenta-500"></div>
            <div class="absolute -left-8 top-40 h-8 w-52 rounded-full bg-sun-400"></div>

            <div class="absolute right-9 top-12 flex h-44 w-72 rotate-6 flex-col justify-between rounded-2xl bg-white p-5 shadow-2xl">
                <div class="flex gap-1.5">
                    <span class="h-2 w-7 rounded-full bg-brand-500"></span>
                    <span class="h-2 w-5 rounded-full bg-magenta-500"></span>
                    <span class="h-2 w-3 rounded-full bg-sun-400"></span>
                </div>
                <div>
                    <div class="text-lg font-extrabold">Andi Pratama</div>
                    <div class="text-xs text-ink-600">Founder · Kopi Senja</div>
                </div>
            </div>

            <div class="absolute inset-x-9 bottom-9 rounded-3xl bg-white p-6 shadow-2xl">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <div class="text-xs font-semibold text-ink-500">Pesanan Anda</div>
                        <div class="font-extrabold">TKT-20261116-0001</div>
                    </div>
                    <span class="rounded-full bg-indigo-100 px-3.5 py-1.5 text-xs font-bold text-indigo-800">Sedang Dicetak</span>
                </div>
                <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-ink-100"><div class="h-full w-[62%] rounded-full bg-brand-600"></div></div>
                <div class="mt-3 flex justify-between text-xs font-semibold text-ink-500">
                    <span>Cek file</span><span class="text-ink-900">Cetak</span><span>Finishing</span><span>Siap</span>
                </div>
            </div>
        </div>
    </section>

    {{-- KATEGORI --}}
    <section class="mx-auto max-w-7xl px-6 pb-16 lg:px-12">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <div class="text-sm font-bold tracking-widest text-magenta-600">KATEGORI</div>
                <h2 class="mt-1.5 text-3xl font-extrabold tracking-tight lg:text-4xl">Apa yang ingin Anda cetak?</h2>
            </div>
            <a href="{{ route('catalog') }}" class="text-sm font-bold text-brand-600 hover:text-ink-900">Semua produk →</a>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-5">
            @foreach ($categories as $category)
                <a href="{{ route('catalog', ['kategori' => $category->slug]) }}" class="flex flex-col gap-3.5 rounded-3xl border border-ink-100 bg-white p-6 transition hover:-translate-y-1 hover:border-brand-500">
                    <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-100 text-brand-600">
                        <x-category-icon :name="$category->icon" />
                    </span>
                    <span class="text-lg font-extrabold">{{ $category->name }}</span>
                    <span class="text-sm leading-relaxed text-ink-600">{{ $category->description }}</span>
                    <span class="text-sm font-bold text-brand-600">{{ $category->products_count }} produk</span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- PRODUK UNGGULAN --}}
    <section class="border-y border-ink-100 bg-white">
        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-12">
            <div class="mb-8">
                <div class="text-sm font-bold tracking-widest text-magenta-600">PRODUK UNGGULAN</div>
                <h2 class="mt-1.5 text-3xl font-extrabold tracking-tight lg:text-4xl">Paling sering dipesan</h2>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($featured as $product)
                    <a href="{{ route('product.show', $product->slug) }}" class="flex flex-col overflow-hidden rounded-3xl border border-ink-100 bg-ink-50 transition hover:-translate-y-1 hover:border-brand-500">
                        <x-product-art />
                        <div class="flex flex-1 flex-col gap-2.5 p-5">
                            <span class="w-fit rounded-full bg-magenta-100 px-2.5 py-1 text-xs font-bold text-magenta-600">{{ $product->category->name }}</span>
                            <span class="text-lg font-extrabold">{{ $product->name }}</span>
                            <span class="text-sm text-ink-600">Min. {{ $product->min_qty }} {{ $product->unit }} · Estimasi {{ $product->estimated_days }} hari</span>
                            <div class="mt-auto flex items-center justify-between pt-2">
                                <span class="text-lg font-extrabold">{{ $product->price_label }}<span class="text-xs font-semibold text-ink-500"> / {{ $product->unit }}</span></span>
                                <span class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white">Pesan</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CARA PESAN --}}
    <section id="cara-pesan" class="bg-ink-900 text-white">
        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-12">
            <div class="mb-10">
                <div class="text-sm font-bold tracking-widest text-sun-400">CARA PESAN</div>
                <h2 class="mt-1.5 text-3xl font-extrabold tracking-tight lg:text-4xl">Empat langkah, selesai.</h2>
            </div>
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($steps as [$no, $stepTitle, $desc])
                    <div class="flex flex-col gap-3.5">
                        <span class="flex h-12 w-12 items-center justify-center rounded-full bg-sun-400 text-xl font-extrabold text-ink-900">{{ $no }}</span>
                        <span class="text-xl font-extrabold">{{ $stepTitle }}</span>
                        <span class="text-[15px] leading-relaxed text-ink-100">{{ $desc }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="mx-auto max-w-7xl px-6 pt-16 lg:px-12">
        <div class="flex flex-wrap items-center justify-between gap-6 rounded-[32px] border border-ink-100 bg-white p-8 lg:p-12">
            <div class="max-w-xl">
                <h2 class="text-3xl font-extrabold tracking-tight">Siap cetak sekarang?</h2>
                <p class="mt-2.5 text-lg text-ink-600">Buat akun gratis dan pesanan pertama Anda bisa langsung diproses.</p>
            </div>
            @guest
                <a href="{{ route('register') }}" class="inline-flex rounded-2xl bg-magenta-600 px-8 py-4 text-base font-bold text-white hover:bg-ink-900">Buat Akun Sekarang</a>
            @else
                <a href="{{ route('catalog') }}" class="inline-flex rounded-2xl bg-magenta-600 px-8 py-4 text-base font-bold text-white hover:bg-ink-900">Lihat Katalog</a>
            @endguest
        </div>
    </section>
</x-layouts.public>