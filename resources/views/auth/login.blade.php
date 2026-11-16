<x-layouts.guest title="Masuk">
    <h1 class="text-3xl font-extrabold tracking-tight">Selamat datang kembali</h1>
    <p class="mt-2 text-ink-600">Masuk untuk memesan dan memantau cetakan Anda.</p>

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <label for="email" class="label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="nama@email.com" class="input">
            @error('email') <p class="error-text">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="label">Kata sandi</label>
            <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="Masukkan kata sandi" class="input">
            @error('password') <p class="error-text">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-2 text-sm font-medium text-ink-600">
            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-ink-200 accent-brand-600">
            Ingat saya
        </label>

        <button type="submit" class="btn-primary w-full py-3.5">Masuk</button>
    </form>

    <p class="mt-6 text-center text-sm text-ink-600">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-bold text-brand-600 hover:text-ink-900">Daftar gratis</a>
    </p>

    @if (app()->environment('local'))
        <div class="mt-8 rounded-2xl border border-ink-200 bg-white p-5 text-sm">
            <div class="font-extrabold">Akun uji (hanya tampil di lokal)</div>
            <ul class="mt-2 space-y-1 text-ink-600">
                <li>owner@tintakita.test</li>
                <li>admin@tintakita.test</li>
                <li>operator@tintakita.test</li>
                <li>customer@tintakita.test</li>
            </ul>
            <div class="mt-2 text-ink-500">Kata sandi semua akun: <span class="font-bold text-ink-900">password</span></div>
        </div>
    @endif
</x-layouts.guest>