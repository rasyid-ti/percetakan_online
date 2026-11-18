<div class="space-y-5">
    <div>
        <label for="name" class="label">Nama lengkap</label>
        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" class="input">
        @error('name') <p class="error-text">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="email" class="label">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" class="input">
        @error('email') <p class="error-text">{{ $message }}</p> @enderror
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="phone" class="label">Telepon <span class="font-medium text-ink-500">(opsional)</span></label>
            <input id="phone" name="phone" type="tel" value="{{ old('phone', $user->phone) }}" class="input">
            @error('phone') <p class="error-text">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="role" class="label">Peran</label>
            @if ($isSelf)
                <input type="text" value="{{ $user->role->label() }}" disabled class="input bg-ink-50">
                <p class="mt-1.5 text-xs text-ink-500">Peran akun Anda sendiri tidak dapat diubah.</p>
            @else
                <select id="role" name="role" class="input">
                    @foreach ($assignable as $r)
                        <option value="{{ $r->value }}" @selected(old('role', $user->role?->value ?? 'customer') === $r->value)>{{ $r->label() }}</option>
                    @endforeach
                </select>
                @error('role') <p class="error-text">{{ $message }}</p> @enderror
            @endif
        </div>
    </div>

    <div>
        <label for="address" class="label">Alamat <span class="font-medium text-ink-500">(opsional)</span></label>
        <textarea id="address" name="address" rows="2" class="input resize-none">{{ old('address', $user->address) }}</textarea>
        @error('address') <p class="error-text">{{ $message }}</p> @enderror
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="password" class="label">Kata sandi @if ($user->exists)<span class="font-medium text-ink-500">(kosongkan jika tidak diganti)</span>@endif</label>
            <input id="password" name="password" type="password" autocomplete="new-password" class="input" placeholder="Minimal 8 karakter">
            @error('password') <p class="error-text">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password_confirmation" class="label">Ulangi kata sandi</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="input">
        </div>
    </div>

    @unless ($isSelf)
        <label class="flex items-center gap-3 text-sm font-semibold">
            <input type="checkbox" name="is_active" value="1" class="h-4 w-4 accent-brand-600" @checked(old('is_active', $user->exists ? $user->is_active : true))>
            Akun aktif (bisa masuk ke sistem)
        </label>
    @endunless
</div>