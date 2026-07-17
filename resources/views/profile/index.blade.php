@extends('layouts.app')
@section('content')

@php
    $user = auth()->user();
    $name = $user->name;
    $initials = strtoupper(substr($name, 0, 1));
    if (str_contains($name, ' ')) {
        $parts = explode(' ', $name);
        $initials = strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
    }
@endphp

<style>
.profile-page { display:flex; justify-content:center; align-items:flex-start; padding-top:20px; }
.profile-container { width:100%; max-width:640px; }

/* Header card */
.profile-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 20px;
    padding: 36px 32px;
    text-align: center;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
}
.profile-hero::before {
    content:'';
    position:absolute;
    width:220px; height:220px;
    border-radius:50%;
    background: radial-gradient(circle, rgba(99,102,241,0.18), transparent 70%);
    top:-60px; right:-40px;
}
.profile-avatar {
    width: 80px; height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6, #6366f1);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
    font-weight: 700;
    color: white;
    margin-bottom: 14px;
    box-shadow: 0 8px 24px rgba(99,102,241,0.35);
}
.profile-name { font-size: 22px; font-weight: 700; color: #f1f5f9; margin-bottom: 4px; }
.profile-email { font-size: 13.5px; color: #64748b; }
.profile-role-badge {
    display: inline-block;
    margin-top: 10px;
    background: rgba(99,102,241,0.15);
    color: #818cf8;
    border: 1px solid rgba(99,102,241,0.25);
    padding: 3px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Form card */
.form-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.07);
    padding: 28px 32px;
    margin-bottom: 16px;
}
.form-card-title {
    font-size: 14px;
    font-weight: 700;
    color: #374151;
    margin-bottom: 20px;
    padding-bottom: 14px;
    border-bottom: 1px solid #f1f5f9;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.form-label { display:block; font-size:12.5px; font-weight:600; color:#64748b; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.4px; }
.form-input {
    width:100%; padding:10px 14px;
    border:1.5px solid #e2e8f0;
    border-radius:10px;
    font-size:14px;
    color:#1e293b;
    font-family:inherit;
    outline:none;
    transition:border-color 0.15s, box-shadow 0.15s;
}
.form-input:focus { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,0.12); }
.form-input[readonly] { background:#f8fafc; color:#64748b; cursor:default; }
.form-group { margin-bottom:18px; }

.btn-save {
    display:inline-flex; align-items:center; gap:6px;
    background: linear-gradient(135deg, #3b82f6, #6366f1);
    color:white; border:none; padding:10px 22px;
    border-radius:10px; font-size:13.5px; font-weight:600;
    cursor:pointer; font-family:inherit;
    box-shadow: 0 4px 14px rgba(99,102,241,0.3);
    transition:opacity 0.15s, transform 0.15s;
}
.btn-save:hover { opacity:0.9; transform:translateY(-1px); }

.btn-back {
    display:inline-flex; align-items:center; gap:6px;
    background:#f1f5f9; color:#374151; border:none;
    padding:10px 22px; border-radius:10px;
    font-size:13.5px; font-weight:600;
    text-decoration:none; cursor:pointer; font-family:inherit;
    transition:background 0.15s;
}
.btn-back:hover { background:#e2e8f0; color:#111827; }

.alert-success {
    background:#f0fdf4; border:1px solid #bbf7d0;
    color:#15803d; padding:12px 16px;
    border-radius:10px; margin-bottom:16px; font-size:13.5px;
}

/* Info row */
.info-row {
    display:flex; align-items:center;
    padding:12px 0;
    border-bottom:1px solid #f1f5f9;
}
.info-row:last-child { border-bottom:none; }
.info-label { font-size:12px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px; width:120px; flex-shrink:0; }
.info-value { font-size:14px; color:#1e293b; font-weight:500; }
</style>

<div class="profile-page">
    <div class="profile-container">

        {{-- Hero Card --}}
        <div class="profile-hero">
            <div class="profile-avatar">{{ $initials }}</div>
            <div class="profile-name">{{ $user->name }}</div>
            <div class="profile-email">{{ $user->email }}</div>
            <div class="profile-role-badge">{{ $user->role === 'manager' ? 'Manager' : ($user->role === 'driver' ? 'Driver' : 'Administrator') }}</div>
        </div>

        @if(session('status') === 'profile-updated' || session('success'))
            <div class="alert-success"><i data-lucide="check-circle-2" style="width:16px;height:16px;vertical-align:middle;margin-top:-2px;margin-right:4px;"></i> {{ session('success') ?? 'Profile berhasil diperbarui!' }}</div>
        @endif

        {{-- Profile Detail Card --}}
        <div class="form-card">
            <div class="form-card-title"><i data-lucide="user" style="width:18px;height:18px;vertical-align:middle;margin-top:-3px;margin-right:4px;"></i> Detail Profil</div>

            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" class="form-input" value="{{ $user->name }}" readonly>
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" class="form-input" value="{{ $user->email }}" readonly>
            </div>

            <div class="form-group">
                <label class="form-label">Role</label>
                <input type="text" class="form-input" value="{{ $user->role === 'manager' ? 'Manager' : ($user->role === 'driver' ? 'Driver' : 'Administrator') }}" readonly>
            </div>

            <div style="display:flex;gap:10px;margin-top:8px;">
                <a href="{{ route('profile.edit') }}" class="btn-save" style="text-decoration:none;">
                    <i data-lucide="settings" style="width:14px;height:14px;margin-right:2px;vertical-align:middle;margin-top:-2px;"></i> Account Settings
                </a>
                <a href="{{ route('dashboard') }}" class="btn-back"><i data-lucide="arrow-left" style="width:14px;height:14px;margin-right:2px;vertical-align:middle;margin-top:-2px;"></i> Dashboard</a>
            </div>
        </div>

        {{-- Account Info (read-only) --}}
        <div class="form-card">
            <div class="form-card-title"><i data-lucide="info" style="width:18px;height:18px;vertical-align:middle;margin-top:-3px;margin-right:4px;"></i> Informasi Akun</div>
            <div class="info-row">
                <span class="info-label">Bergabung</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($user->created_at)->format('d F Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Terakhir Update</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($user->updated_at)->format('d F Y, H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status</span>
                <span class="info-value">
                    <span style="display:inline-flex;align-items:center;gap:5px;">
                        <span style="width:7px;height:7px;border-radius:50%;background:#22c55e;display:inline-block;"></span>
                        Aktif
                    </span>
                </span>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('status') === 'profile-updated' || session('success'))
<script>
Swal.fire({icon:'success',title:'Berhasil!',text:'{{ session('success') ?? 'Profile berhasil diperbarui.' }}',timer:2000,showConfirmButton:false});
</script>
@endif
@endsection