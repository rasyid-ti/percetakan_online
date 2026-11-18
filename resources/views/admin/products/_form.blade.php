@php
    $rows = old('options', $product->exists
        ? $product->options->map(fn ($o) => [
            'id' => $o->id,
            'group' => $o->group,
            'name' => $o->name,
            'extra_price' => $o->extra_price,
        ])->all()
        : []);

    $groups = collect(['Ukuran', 'Bahan', 'Finishing'])
        ->merge(\App\Models\ProductOption::query()->distinct()->pluck('group'))
        ->unique()->values();

    $optionErrors = collect($errors->get('options.*'))->flatten()->unique();
@endphp

<div class="flex flex-wrap items-start gap-8">

    {{-- Kiri: data produk --}}
    <div class="min-w-[300px] flex-[999_1_520px] space-y-8">

        <section class="space-y-5 rounded-3xl border border-ink-100 bg-white p-7">
            <h2 class="text-lg font-extrabold">Informasi produk</h2>

            <div>
                <label for="category_id" class="label">Kategori</label>
                <select id="category_id" name="category_id" class="input">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((int) old('category_id', $product->category_id) === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="error-text">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="name" class="label">Nama produk</label>
                <input id="name" name="name" type="text" value="{{ old('name', $product->name) }}" class="input" placeholder="Contoh: Kartu Nama Standar">
                @error('name') <p class="error-text">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="label">Deskripsi</label>
                <textarea id="description" name="description" rows="3" class="input resize-none" placeholder="Jelaskan produk secara singkat">{{ old('description', $product->description) }}</textarea>
                @error('description') <p class="error-text">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="base_price" class="label">Harga dasar per satuan (Rp)</label>
                    <input id="base_price" name="base_price" type="number" min="0" value="{{ old('base_price', $product->base_price) }}" class="input">
                    @error('base_price') <p class="error-text">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="unit" class="label">Satuan</label>
                    <select id="unit" name="unit" class="input">
                        @foreach (['pcs', 'lembar', 'meter', 'box', 'set'] as $unit)
                            <option value="{{ $unit }}" @selected(old('unit', $product->unit ?? 'pcs') === $unit)>{{ $unit }}</option>
                        @endforeach
                    </select>
                    @error('unit') <p class="error-text">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="min_qty" class="label">Minimal pesan</label>
                    <input id="min_qty" name="min_qty" type="number" min="1" value="{{ old('min_qty', $product->min_qty ?? 1) }}" class="input">
                    @error('min_qty') <p class="error-text">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="estimated_days" class="label">Estimasi produksi (hari)</label>
                    <input id="estimated_days" name="estimated_days" type="number" min="1" value="{{ old('estimated_days', $product->estimated_days ?? 2) }}" class="input">
                    @error('estimated_days') <p class="error-text">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <section class="space-y-4 rounded-3xl border border-ink-100 bg-white p-7">
            <div>
                <h2 class="text-lg font-extrabold">Opsi produk</h2>
                <p class="mt-1 text-sm text-ink-600">Contoh: kelompok <b>Ukuran</b>, nama <b>A4</b>, tambahan harga <b>0</b>. Pelanggan memilih satu opsi dari tiap kelompok, dan tambahannya dijumlah per satuan.</p>
            </div>

            @foreach ($optionErrors as $message)
                <p class="error-text">{{ $message }}</p>
            @endforeach

            <datalist id="group-list">
                @foreach ($groups as $group)
                    <option value="{{ $group }}">
                @endforeach
            </datalist>

            <div id="option-rows" class="space-y-3">
                @foreach ($rows as $i => $row)
                    @include('admin.products._option-row', ['i' => $i, 'row' => $row])
                @endforeach
            </div>

            <template id="option-template">
                @include('admin.products._option-row', ['i' => '__I__', 'row' => []])
            </template>

            <button type="button" id="add-option" class="btn-outline">+ Tambah opsi</button>
        </section>
    </div>

    {{-- Kanan: gambar dan status --}}
    <aside class="min-w-[280px] flex-1 basis-[320px] space-y-6">

        <section class="space-y-4 rounded-3xl border border-ink-100 bg-white p-7">
            <h2 class="text-lg font-extrabold">Gambar produk</h2>

            @if ($product->exists && $product->image_url)
                <img src="{{ $product->image_url }}" alt="Gambar {{ $product->name }}" class="h-44 w-full rounded-2xl object-cover">
                <label class="flex items-center gap-2 text-sm font-semibold text-ink-600">
                    <input type="checkbox" name="remove_image" value="1" class="h-4 w-4 accent-brand-600">
                    Hapus gambar ini
                </label>
            @else
                <div class="flex h-44 items-center justify-center rounded-2xl bg-ink-100 text-sm font-bold text-ink-500">Belum ada gambar</div>
            @endif

            <div>
                <label for="image" class="label">{{ $product->exists && $product->image_url ? 'Ganti gambar' : 'Unggah gambar' }}</label>
                <input id="image" type="file" name="image" accept=".jpg,.jpeg,.png,.webp"
                       class="input file:mr-4 file:rounded-lg file:border-0 file:bg-brand-100 file:px-4 file:py-2 file:text-sm file:font-bold file:text-brand-600">
                <p class="mt-1.5 text-xs text-ink-500">Disarankan rasio 4:3 (misal 1200 × 900 px). JPG, PNG, atau WEBP, maksimal 3 MB.</p>
                @error('image') <p class="error-text">{{ $message }}</p> @enderror
            </div>
        </section>

        <section class="space-y-3 rounded-3xl border border-ink-100 bg-white p-7">
            <h2 class="text-lg font-extrabold">Status</h2>
            <label class="flex items-center gap-3 text-sm font-semibold">
                <input type="checkbox" name="is_active" value="1" class="h-4 w-4 accent-brand-600" @checked(old('is_active', $product->exists ? $product->is_active : true))>
                Aktif (tampil di katalog)
            </label>
            <label class="flex items-center gap-3 text-sm font-semibold">
                <input type="checkbox" name="is_featured" value="1" class="h-4 w-4 accent-brand-600" @checked(old('is_featured', $product->exists ? $product->is_featured : false))>
                Produk unggulan (tampil di beranda)
            </label>
        </section>

        <button type="submit" class="btn-primary w-full py-4 text-base">Simpan Produk</button>
    </aside>
</div>

<script>
    (function () {
        const rows = document.getElementById('option-rows');
        const template = document.getElementById('option-template').innerHTML;
        let next = {{ count($rows) }};

        document.getElementById('add-option').addEventListener('click', () => {
            rows.insertAdjacentHTML('beforeend', template.replaceAll('__I__', next++));
        });

        rows.addEventListener('click', (e) => {
            const button = e.target.closest('.remove-option');
            if (button) button.closest('.option-row').remove();
        });
    })();
</script>