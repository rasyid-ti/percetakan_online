<div class="option-row flex flex-wrap items-center gap-3 rounded-2xl bg-ink-50 p-3">
    <input type="hidden" name="options[{{ $i }}][id]" value="{{ $row['id'] ?? '' }}">

    <div class="min-w-[130px] flex-1">
        <label for="opt-group-{{ $i }}" class="sr-only">Kelompok</label>
        <input id="opt-group-{{ $i }}" list="group-list" name="options[{{ $i }}][group]" type="text" value="{{ $row['group'] ?? '' }}" placeholder="Kelompok (Ukuran)" class="input">
    </div>
    <div class="min-w-[150px] flex-[1.4]">
        <label for="opt-name-{{ $i }}" class="sr-only">Nama opsi</label>
        <input id="opt-name-{{ $i }}" name="options[{{ $i }}][name]" type="text" value="{{ $row['name'] ?? '' }}" placeholder="Nama opsi (A4)" class="input">
    </div>
    <div class="min-w-[110px] flex-1">
        <label for="opt-price-{{ $i }}" class="sr-only">Tambahan harga</label>
        <input id="opt-price-{{ $i }}" name="options[{{ $i }}][extra_price]" type="number" min="0" value="{{ $row['extra_price'] ?? 0 }}" placeholder="+ Rp" class="input">
    </div>
    <button type="button" class="remove-option rounded-lg px-3 py-2 text-sm font-bold text-rose-600 hover:bg-rose-50" aria-label="Hapus opsi">Hapus</button>
</div>