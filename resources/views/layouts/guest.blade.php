<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistem Informasi ERP Naga Sakti Jaya - Kelola pelanggan, produk, pengiriman, transaksi, dan pelaporan secara efisien dan real-time.">
    <meta name="keywords" content="Naga Sakti Jaya, ERP Naga Sakti Jaya, erp-nagasaktijaya, Manajemen Gas, Vercel ERP">
    <title>TK. NAGA SAKTI JAYA — Sistem ERP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            position: relative;
            overflow: hidden;
        }

        /* Background decorative blobs */
        body::before {
            content: '';
            position: fixed;
            width: 500px; height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(99,102,241,0.12) 0%, transparent 70%);
            top: -100px; left: -100px;
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59,130,246,0.10) 0%, transparent 70%);
            bottom: -80px; right: -80px;
            pointer-events: none;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 20px;
            position: relative;
            z-index: 10;
        }

        /* Brand header */
        .brand {
            text-align: center;
            margin-bottom: 28px;
        }
        .brand-icon {
            width: 56px; height: 56px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin-bottom: 14px;
            box-shadow: 0 8px 24px rgba(99,102,241,0.35);
        }
        .brand-name {
            font-size: 22px;
            font-weight: 700;
            color: #f1f5f9;
            letter-spacing: 0.3px;
        }
        .brand-sub {
            font-size: 13px;
            color: #64748b;
            margin-top: 3px;
        }

        /* Card */
        .auth-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.09);
            border-radius: 20px;
            padding: 36px 36px 32px;
            backdrop-filter: blur(16px);
            box-shadow: 0 24px 60px rgba(0,0,0,0.4);
        }

        .auth-card h2 {
            font-size: 20px;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: 6px;
        }
        .auth-card p.subtitle {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 28px;
        }

        /* Form elements */
        .form-group { margin-bottom: 18px; }

        label.form-label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: #94a3b8;
            margin-bottom: 7px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-input {
            width: 100%;
            padding: 11px 14px;
            background: rgba(255,255,255,0.06);
            border: 1.5px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            font-size: 14px;
            color: #e2e8f0;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: border-color 0.18s, box-shadow 0.18s, background 0.18s;
        }
        .form-input::placeholder { color: #475569; }
        .form-input:focus {
            border-color: #6366f1;
            background: rgba(99,102,241,0.08);
            box-shadow: 0 0 0 3px rgba(99,102,241,0.18);
        }
        .form-input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 1000px #1e293b inset;
            -webkit-text-fill-color: #e2e8f0;
        }

        /* Password wrapper */
        .input-wrap { position: relative; }
        .toggle-pw {
            position: absolute;
            right: 12px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            cursor: pointer; font-size: 17px;
            color: #475569;
            line-height: 1;
            transition: color 0.15s;
        }
        .toggle-pw:hover { color: #94a3b8; }

        /* Error text */
        .input-error { font-size: 12px; color: #f87171; margin-top: 5px; }

        /* Remember + forgot row */
        .row-flex {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
        }
        .remember-label {
            display: flex; align-items: center; gap: 7px;
            font-size: 13px; color: #94a3b8; cursor: pointer;
        }
        .remember-label input[type="checkbox"] {
            width: 15px; height: 15px;
            accent-color: #6366f1;
            cursor: pointer;
        }
        .forgot-link {
            font-size: 13px;
            color: #6366f1;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.15s;
        }
        .forgot-link:hover { color: #818cf8; }

        /* Submit button */
        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            letter-spacing: 0.3px;
            transition: opacity 0.15s, transform 0.15s;
            box-shadow: 0 4px 16px rgba(99,102,241,0.35);
        }
        .btn-login:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-login:active { transform: translateY(0); }

        /* Session status */
        .session-status {
            background: rgba(16,185,129,0.1);
            border: 1px solid rgba(16,185,129,0.3);
            color: #34d399;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 18px;
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        {{-- Brand --}}
        <div class="brand">
            <div class="brand-name">TK. NAGA SAKTI JAYA</div>
            <div class="brand-sub">Management System</div>
        </div>

        {{-- Card --}}
        <div class="auth-card">
            <h2>{{ $title ?? 'Selamat Datang 👋' }}</h2>
            <p class="subtitle">{{ $subtitle ?? 'Masuk ke akun Anda untuk melanjutkan' }}</p>

            {{ $slot }}
        </div>
    </div>
</body>
</html>
