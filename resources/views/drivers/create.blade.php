@extends('layouts.app')
@section('content')
<style>
.page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; }
.page-title { font-size:22px; font-weight:700; color:#0f172a; margin:0; }
.page-subtitle { font-size:13px; color:#64748b; margin:2px 0 0; }
.btn-secondary-custom { display:inline-flex; align-items:center; gap:6px; background:#f1f5f9; color:#374151; border:none; padding:9px 18px; border-radius:10px; font-size:13.5px; font-weight:600; text-decoration:none; cursor:pointer; transition:background 0.15s; }
.btn-secondary-custom:hover { background:#e2e8f0; color:#111827; }
.btn-primary-custom { display:inline-flex; align-items:center; gap:6px; background:#2563eb; color:white; border:none; padding:9px 18px; border-radius:10px; font-size:13.5px; font-weight:600; text-decoration:none; cursor:pointer; transition:background 0.15s; }
.btn-primary-custom:hover { background:#1d4ed8; color:white; }
.form-card { background:white; border-radius:16px; box-shadow:0 1px 4px rgba(0,0,0,0.06); padding:32px; max-width:680px; margin-left:auto; margin-right:auto; }
.form-label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; }
.form-input { width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:10px; font-size:14px; color:#1e293b; transition:border-color 0.15s; outline:none; font-family:inherit; }
.form-input:focus { border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,0.12); }
.form-group { margin-bottom:20px; }
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.alert-error { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; padding:12px 16px; border-radius:10px; margin-bottom:20px; font-size:13.5px; }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="user-plus" style="width:22px;height:22px;vertical-align:middle;margin-top:-4px;margin-right:4px;"></i> Tambah Driver</h1>
        <p class="page-subtitle">Pendaftaran driver/kurir pengiriman baru</p>
    </div>
    <a href="{{ route('drivers.index') }}" class="btn-secondary-custom"><i data-lucide="arrow-left" style="width:15px;height:15px;margin-right:2px;"></i> Kembali</a>
</div>

<div class="form-card">
    @if($errors->any())
        <div class="alert-error">
            <strong><i data-lucide="alert-triangle" style="width:16px;height:16px;vertical-align:middle;margin-top:-2px;margin-right:4px;"></i> Ada kesalahan:</strong>
            <ul style="margin:6px 0 0;padding-left:18px;">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('drivers.store') }}">
        @csrf
        <div class="form-group">
            <label class="form-label">Nama Driver</label>
            <input type="text" name="name" class="form-input" value="{{ old('name') }}" placeholder="Masukkan nama driver" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">No. Telepon</label>
            <input type="text" name="phone" class="form-input" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">No. Polisi (Kendaraan)</label>
                <input type="text" name="license_plate" class="form-input" value="{{ old('license_plate') }}" placeholder="cth: B 1234 ABC" required>
            </div>

            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-input" required>
                    <option value="Active" {{ old('status') === 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ old('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>

        <div style="margin-top: 24px; margin-bottom: 16px; border-top: 1px solid #e2e8f0; padding-top: 16px;">
            <h5 style="font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 2px;">Akun Login Driver (Opsional)</h5>
            <p style="font-size: 11px; color: #64748b; margin-bottom: 12px;">Isi untuk membuatkan akun masuk bagi driver ini</p>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Email Login</label>
                <input type="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="driver@email.com">
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-input" placeholder="Minimal 6 karakter">
            </div>
        </div>

        <div style="display:flex;gap:10px;margin-top:8px;">
            <button type="submit" class="btn-primary-custom"><i data-lucide="save" style="width:15px;height:15px;margin-right:4px;"></i> Simpan Driver</button>
            <a href="{{ route('drivers.index') }}" class="btn-secondary-custom">Batal</a>
        </div>
    </form>
</div>
@endsection
