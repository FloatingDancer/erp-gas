<x-guest-layout>
    <x-slot name="title">Reset Password</x-slot>
    <x-slot name="subtitle">Buat password baru Anda</x-slot>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- Email Address --}}
        <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                class="form-input"
                value="{{ old('email', $request->email) }}"
                required autocomplete="username"
            >
            @error('email')
                <p class="input-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="form-group">
            <label class="form-label" for="password">Password Baru</label>
            <input
                id="password"
                type="password"
                name="password"
                class="form-input"
                placeholder="••••••••"
                required autocomplete="new-password"
            >
            @error('password')
                <p class="input-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="form-group">
            <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                class="form-input"
                placeholder="••••••••"
                required autocomplete="new-password"
            >
            @error('password_confirmation')
                <p class="input-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn-login" style="margin-bottom: 20px;">
            Simpan Password Baru <i data-lucide="arrow-right" style="width:14px;height:14px;margin-left:4px;vertical-align:middle;margin-top:-2px;"></i>
        </button>

        <div style="text-align: center;">
            <a href="{{ route('login') }}" class="forgot-link">Kembali ke halaman masuk</a>
        </div>
    </form>
</x-guest-layout>
