<div class="space-y-5">
    <div>
        <label for="name" class="label">Nama kategori</label>
        <input id="name" name="name" type="text" value="{{ old('name', $category->name) }}" class="input" placeholder="Contoh: Kartu Nama">
        @error('name') <p class="error-text">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="icon" class="label">Ikon</label>
        <select id="icon" name="icon" class="input">
            @foreach ($icons as $value => $label)
                <option value="{{ $value }}" @selected(old('icon', $category->icon ?? 'card') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('icon') <p class="error-text">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="description" class="label">Deskripsi singkat</label>
        <input id="description" name="description" type="text" value="{{ old('description', $category->description) }}" class="input" placeholder="Tampil di beranda dan katalog">
        @error('description') <p class="error-text">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="sort_order" class="label">Urutan tampil</label>
        <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $category->sort_order ?? 0) }}" class="input">
        @error('sort_order') <p class="error-text">{{ $message }}</p> @enderror
    </div>
</div>