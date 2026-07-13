<x-guest-layout>
    <style>
        .quick-login-title {
            text-align: center;
            margin: 22px 0 12px;
            font-size: 11.5px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1px;
        }
        .quick-login-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .btn-demo-login {
            padding: 10px 14px;
            background: rgba(255, 255, 255, 0.03);
            border: 1.5px dashed rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            color: #cbd5e1;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: background 0.18s, border-color 0.18s, color 0.18s, transform 0.1s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .btn-demo-login:hover {
            background: rgba(99, 102, 241, 0.08);
            border-color: rgba(99, 102, 241, 0.4);
            color: #e2e8f0;
        }
        .btn-demo-login:active {
            transform: scale(0.98);
        }
        .quick-login-divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 24px 0 16px;
        }
        .quick-login-divider::before,
        .quick-login-divider::after {
            content: '';
            flex: 1;
            border-bottom: 1.5px solid rgba(255, 255, 255, 0.08);
        }
        .quick-login-divider::before {
            margin-right: 12px;
        }
        .quick-login-divider::after {
            margin-left: 12px;
        }
        .divider-text {
            font-size: 11.5px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1.2px;
            white-space: nowrap;
        }
    </style>

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
                <button type="button" class="toggle-pw" id="togglePw" title="Tampilkan password" style="display:inline-flex;align-items:center;justify-content:center;">
                    <i data-lucide="eye" style="width:18px;height:18px;"></i>
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

        @if(config('app.demo'))
            {{-- Quick Login Divider --}}
            <div class="quick-login-divider">
                <span class="divider-text">Atau Quick Login</span>
            </div>

            <div class="quick-login-grid">
                <button type="button" class="btn-demo-login" onclick="quickLogin('guest@gmail.com', '12345678')">
                    <i data-lucide="user" style="width:14px;height:14px;margin-right:4px;vertical-align:middle;margin-top:-2px;"></i> Guest
                </button>
                <button type="button" class="btn-demo-login" onclick="quickLogin('driver@gmail.com', '12345678')">
                    <i data-lucide="truck" style="width:14px;height:14px;margin-right:4px;vertical-align:middle;margin-top:-2px;"></i> Driver
                </button>
            </div>
        @endif
    </form>

    <script>
    document.getElementById('togglePw').addEventListener('click', function () {
        const pw = document.getElementById('password');
        if (pw.type === 'password') {
            pw.type = 'text';
            this.innerHTML = '<i data-lucide="eye-off" style="width:18px;height:18px;"></i>';
        } else {
            pw.type = 'password';
            this.innerHTML = '<i data-lucide="eye" style="width:18px;height:18px;"></i>';
        }
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });

    function quickLogin(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
        
        const form = document.querySelector('form');
        const submitBtn = form.querySelector('.btn-login');
        submitBtn.innerHTML = 'Memproses masuk...';
        submitBtn.style.opacity = '0.7';
        
        setTimeout(() => {
            form.submit();
        }, 150);
    }
    </script>
</x-guest-layout>
