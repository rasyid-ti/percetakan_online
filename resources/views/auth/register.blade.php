<x-layouts.guest title="Daftar">
    <h1 class="text-3xl font-extrabold tracking-tight">Buat akun gratis</h1>
    <p class="mt-2 text-ink-600">Pesanan pertama Anda bisa langsung diproses.</p>

    <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <label for="name" class="label">Nama lengkap</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Nama Anda" class="input">
            @error('name') <p class="error-text">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" placeholder="nama@email.com" class="input">
            @error('email') <p class="error-text">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="phone" class="label">Nomor WhatsApp <span class="font-medium text-ink-500">(opsional)</span></label>
            <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel" placeholder="08xxxxxxxxxx" class="input">
            @error('phone') <p class="error-text">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="label">Kata sandi</label>
            <input id="password" name="password" type="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" class="input">
            @error('password') <p class="error-text">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="label">Ulangi kata sandi</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" placeholder="Ketik ulang kata sandi" class="input">
        </div>

        <button type="submit" class="btn-primary w-full py-3.5">Daftar Sekarang</button>
    </form>

    <p class="mt-6 text-center text-sm text-ink-600">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-bold text-brand-600 hover:text-ink-900">Masuk</a>
    </p>
</x-layouts.guest>