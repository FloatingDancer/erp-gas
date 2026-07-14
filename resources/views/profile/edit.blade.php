@extends('layouts.app')
@section('content')

@php
    $user = auth()->user();
    $initials = strtoupper(substr($user->name, 0, 1));
    if (str_contains($user->name, ' ')) {
        $parts = explode(' ', $user->name);
        $initials = strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
    }
    $isGuest = $user->email === 'guest@gmail.com' || $user->email === 'driverguest@gmail.com';
@endphp

<style>
.settings-page { display:flex; justify-content:center; padding-top:10px; }
.settings-container { width:100%; max-width:640px; }

/* Section card */
.settings-card {
    background:white;
    border-radius:16px;
    box-shadow:0 1px 4px rgba(0,0,0,0.07);
    margin-bottom:20px;
    overflow:hidden;
}
.settings-card-header {
    display:flex; align-items:center; gap:12px;
    padding:18px 24px;
    border-bottom:1px solid #f1f5f9;
}
.settings-card-icon {
    width:36px; height:36px;
    border-radius:9px;
    display:flex; align-items:center; justify-content:center;
    font-size:17px; flex-shrink:0;
}
.icon-blue  { background:#dbeafe; }
.icon-amber { background:#fef9c3; }
.icon-red   { background:#fee2e2; }
.settings-card-title { font-size:14.5px; font-weight:700; color:#0f172a; margin:0; }
.settings-card-sub   { font-size:12px; color:#94a3b8; margin:2px 0 0; }
.settings-card-body  { padding:24px; }

/* Form */
.form-label { display:block; font-size:12px; font-weight:600; color:#64748b; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.4px; }
.form-input {
    width:100%; padding:10px 14px;
    border:1.5px solid #e2e8f0; border-radius:10px;
    font-size:14px; color:#1e293b; font-family:inherit;
    outline:none; transition:border-color 0.15s, box-shadow 0.15s;
}
.form-input:focus { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,0.12); }
.form-group { margin-bottom:18px; }
.input-error { font-size:12px; color:#ef4444; margin-top:4px; }

/* Buttons */
.btn-primary-custom {
    display:inline-flex; align-items:center; gap:6px;
    background:linear-gradient(135deg,#3b82f6,#6366f1);
    color:white; border:none; padding:10px 20px;
    border-radius:10px; font-size:13.5px; font-weight:600;
    cursor:pointer; font-family:inherit;
    box-shadow:0 4px 14px rgba(99,102,241,0.25);
    transition:opacity 0.15s, transform 0.15s;
}
.btn-primary-custom:hover { opacity:0.9; transform:translateY(-1px); }

.btn-danger {
    display:inline-flex; align-items:center; gap:6px;
    background:#fef2f2; color:#dc2626; border:1.5px solid #fecaca;
    padding:10px 20px; border-radius:10px;
    font-size:13.5px; font-weight:600;
    cursor:pointer; font-family:inherit;
    transition:background 0.15s;
}
.btn-danger:hover { background:#fee2e2; }

/* Page header */
.page-header {
    display:flex; justify-content:space-between; align-items:center;
    margin-bottom:24px;
}
.page-title { font-size:22px; font-weight:700; color:#0f172a; margin:0; }
.page-subtitle { font-size:13px; color:#64748b; margin:2px 0 0; }
.btn-back {
    display:inline-flex; align-items:center; gap:6px;
    background:#f1f5f9; color:#374151; border:none;
    padding:9px 18px; border-radius:10px;
    font-size:13.5px; font-weight:600;
    text-decoration:none; transition:background 0.15s;
}
.btn-back:hover { background:#e2e8f0; color:#111827; }

.alert-success {
    background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d;
    padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:13.5px;
}

/* Danger zone */
.danger-notice {
    background:#fef2f2; border:1px solid #fecaca;
    border-radius:10px; padding:14px 16px;
    font-size:13px; color:#b91c1c; margin-bottom:16px;
}
</style>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="settings" style="width:22px;height:22px;vertical-align:middle;margin-top:-4px;margin-right:4px;"></i> Account Settings</h1>
        <p class="page-subtitle">Kelola informasi dan keamanan akun Anda</p>
    </div>
    <a href="{{ route('profile.index') }}" class="btn-back"><i data-lucide="arrow-left" style="width:14px;height:14px;margin-right:2px;vertical-align:middle;margin-top:-2px;"></i> My Profile</a>
</div>

@if(session('status') === 'profile-updated')
    <div class="alert-success"><i data-lucide="check-circle-2" style="width:16px;height:16px;vertical-align:middle;margin-top:-2px;margin-right:4px;"></i> Informasi profil berhasil diperbarui!</div>
@endif
@if(session('status') === 'password-updated')
    <div class="alert-success"><i data-lucide="shield-check" style="width:16px;height:16px;vertical-align:middle;margin-top:-2px;margin-right:4px;"></i> Password berhasil diperbarui!</div>
@endif

<div class="settings-page">
    <div class="settings-container">
        @if($isGuest)
            <div style="background: #eff6ff; border: 1.5px solid #bfdbfe; color: #1e40af; padding: 16px; border-radius: 12px; margin-bottom: 20px; font-size: 13.5px; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="info" style="width: 18px; height: 18px; flex-shrink: 0; color: #3b82f6;"></i>
                Fitur pengaturan akun dinonaktifkan pada akun Demo/Guest untuk menjaga integritas data evaluasi.
            </div>
        @endif

        {{-- ===== 1. Profile Information ===== --}}
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="settings-card-icon icon-blue" style="display:flex;align-items:center;justify-content:center;"><i data-lucide="user" style="width:20px;height:20px;"></i></div>
                <div>
                    <p class="settings-card-title">Informasi Profil</p>
                    <p class="settings-card-sub">Perbarui nama dan alamat email Anda</p>
                </div>
            </div>
            <div class="settings-card-body">
                <form method="post" action="{{ route('profile.update') }}">
                    @csrf
                    @method('patch')

                    <div class="form-group">
                        <label class="form-label" for="name">Nama Lengkap</label>
                        <input id="name" name="name" type="text" class="form-input"
                               value="{{ old('name', $user->name) }}" required autofocus {{ $isGuest ? 'disabled' : '' }}>
                        @error('name', 'updateProfileInformation')
                            <p class="input-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <input id="email" name="email" type="email" class="form-input"
                               value="{{ old('email', $user->email) }}" required {{ $isGuest ? 'disabled' : '' }}>
                        @error('email', 'updateProfileInformation')
                            <p class="input-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn-primary-custom" {{ $isGuest ? 'disabled style=opacity:0.6;cursor:not-allowed;' : '' }}><i data-lucide="save" style="width:15px;height:15px;margin-right:4px;"></i> Simpan</button>
                </form>
            </div>
        </div>

        {{-- ===== 2. Update Password ===== --}}
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="settings-card-icon icon-amber" style="display:flex;align-items:center;justify-content:center;"><i data-lucide="key-round" style="width:20px;height:20px;"></i></div>
                <div>
                    <p class="settings-card-title">Update Password</p>
                    <p class="settings-card-sub">Pastikan menggunakan password yang kuat dan aman</p>
                </div>
            </div>
            <div class="settings-card-body">
                <form method="post" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')

                    <div class="form-group">
                        <label class="form-label" for="current_password">Password Saat Ini</label>
                        <input id="current_password" name="current_password" type="password"
                               class="form-input" autocomplete="current-password" placeholder="••••••••" {{ $isGuest ? 'disabled' : '' }}>
                        @error('current_password', 'updatePassword')
                            <p class="input-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password Baru</label>
                        <input id="password" name="password" type="password"
                               class="form-input" autocomplete="new-password" placeholder="Min. 8 karakter" {{ $isGuest ? 'disabled' : '' }}>
                        @error('password', 'updatePassword')
                            <p class="input-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
                        <input id="password_confirmation" name="password_confirmation" type="password"
                               class="form-input" autocomplete="new-password" placeholder="Ulangi password baru" {{ $isGuest ? 'disabled' : '' }}>
                        @error('password_confirmation', 'updatePassword')
                            <p class="input-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn-primary-custom" {{ $isGuest ? 'disabled style=opacity:0.6;cursor:not-allowed;' : '' }}><i data-lucide="key-round" style="width:15px;height:15px;margin-right:4px;"></i> Update Password</button>
                </form>
            </div>
        </div>

        {{-- ===== 3. Danger Zone ===== --}}
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="settings-card-icon icon-red" style="display:flex;align-items:center;justify-content:center;"><i data-lucide="alert-triangle" style="width:20px;height:20px;"></i></div>
                <div>
                    <p class="settings-card-title">Hapus Akun</p>
                    <p class="settings-card-sub">Tindakan ini tidak dapat dibatalkan</p>
                </div>
            </div>
            <div class="settings-card-body">
                <div class="danger-notice">
                    <i data-lucide="alert-triangle" style="width:14px;height:14px;margin-right:4px;vertical-align:middle;margin-top:-2px;"></i> Setelah akun dihapus, semua data akan hilang permanen. Harap unduh data Anda sebelum menghapus akun.
                </div>
                <button type="button" class="btn-danger" onclick="confirmDeleteAccount()" {{ $isGuest ? 'disabled style=opacity:0.6;cursor:not-allowed;' : '' }}>
                    <i data-lucide="trash-2" style="width:15px;height:15px;margin-right:4px;"></i> Hapus Akun Saya
                </button>

                <form id="delete-account-form" method="post" action="{{ route('profile.destroy') }}" style="display:none;">
                    @csrf @method('delete')
                    <input type="hidden" name="password" id="delete-password-input">
                </form>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('status') === 'profile-updated' || session('status') === 'password-updated')
<script>
Swal.fire({
    icon:'success',
    title:'Berhasil!',
    text: '{{ session('status') === 'password-updated' ? 'Password berhasil diperbarui.' : 'Profil berhasil diperbarui.' }}',
    timer:2000, showConfirmButton:false
});
</script>
@endif
<script>
function confirmDeleteAccount() {
    Swal.fire({
        title: 'Hapus Akun?',
        text: 'Masukkan password Anda untuk konfirmasi penghapusan akun.',
        input: 'password',
        inputPlaceholder: 'Password Anda',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus Akun',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            document.getElementById('delete-password-input').value = result.value;
            document.getElementById('delete-account-form').submit();
        }
    });
}
</script>
@endsection
