<x-guest-layout>
    <x-slot name="title">Lupa Password?</x-slot>
    <x-slot name="subtitle">Atur ulang password Anda</x-slot>

    @if (session('status'))
        <div class="session-status">{{ session('status') }}</div>
    @endif

    <p style="color: #94a3b8; font-size: 13px; line-height: 1.6; margin-bottom: 22px; text-align: center;">
        Jangan khawatir! Cukup masukkan alamat email Anda dan kami akan mengirimkan link untuk menyetel ulang password.
    </p>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        {{-- Email --}}
        <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                class="form-input"
                value="{{ old('email') }}"
                placeholder="nama@email.com"
                required autofocus
            >
            @error('email')
                <p class="input-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn-login" style="margin-bottom: 20px;">
            Kirim Link Reset Password <i data-lucide="arrow-right" style="width:14px;height:14px;margin-left:4px;vertical-align:middle;margin-top:-2px;"></i>
        </button>

        <div style="text-align: center;">
            <a href="{{ route('login') }}" class="forgot-link">Kembali ke halaman masuk</a>
        </div>
    </form>
</x-guest-layout>
