<x-guest-layout>
    {{-- Session Status --}}
    @if (session('status'))
        <div class="session-status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
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
                placeholder="you@example.com"
                required autofocus autocomplete="username"
            >
            @error('email')
                <p class="input-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <div class="input-wrap">
                <input
                    id="password"
                    type="password"
                    name="password"
                    class="form-input"
                    placeholder="••••••••"
                    style="padding-right:44px;"
                    required autocomplete="current-password"
                >
                <button type="button" class="toggle-pw" id="togglePw" title="Tampilkan password">
                    👁
                </button>
            </div>
            @error('password')
                <p class="input-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember + Forgot --}}
        <div class="row-flex">
            <label class="remember-label">
                <input type="checkbox" name="remember" id="remember_me">
                Ingat saya
            </label>
            @if (Route::has('password.request'))
                <a class="forgot-link" href="{{ route('password.request') }}">Lupa password?</a>
            @endif
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-login">Masuk →</button>
    </form>

    <script>
    document.getElementById('togglePw').addEventListener('click', function () {
        const pw = document.getElementById('password');
        if (pw.type === 'password') {
            pw.type = 'text';
            this.innerHTML = '🙈';
        } else {
            pw.type = 'password';
            this.innerHTML = '👁';
        }
    });
    </script>
</x-guest-layout>
