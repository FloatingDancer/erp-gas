@extends('layouts.app')
@section('content')
<style>
.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;}
.page-title{font-size:22px;font-weight:700;color:#0f172a;margin:0;}
.page-subtitle{font-size:13px;color:#64748b;margin:2px 0 0;}
.btn-primary-custom{display:inline-flex;align-items:center;gap:6px;background:#2563eb;color:white;border:none;padding:9px 18px;border-radius:10px;font-size:13.5px;font-weight:600;text-decoration:none;cursor:pointer;transition:background 0.15s;}
.btn-primary-custom:hover{background:#1d4ed8;color:white;}
.btn-secondary-custom{display:inline-flex;align-items:center;gap:6px;background:#f1f5f9;color:#374151;border:none;padding:9px 18px;border-radius:10px;font-size:13.5px;font-weight:600;text-decoration:none;cursor:pointer;}
.btn-secondary-custom:hover{background:#e2e8f0;color:#111827;}
.form-card{background:white;border-radius:16px;box-shadow:0 1px 4px rgba(0,0,0,0.06);padding:32px;max-width:680px;}
.form-label{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;}
.form-input{width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;color:#1e293b;outline:none;font-family:inherit;}
.form-input:focus{border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,0.12);}
.form-group{margin-bottom:20px;}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.alert-error{background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13.5px;}
</style>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="edit" style="width:22px;height:22px;vertical-align:middle;margin-top:-4px;margin-right:4px;"></i> Edit Delivery</h1>
        <p class="page-subtitle">Perbarui data pengiriman</p>
    </div>
    <a href="{{ route('deliveries.index') }}" class="btn-secondary-custom"><i data-lucide="arrow-left" style="width:15px;height:15px;margin-right:2px;"></i> Kembali</a>
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

    <form action="{{ route('deliveries.update', $delivery->id) }}" method="POST">
        @csrf @method('PUT')

        <div class="form-group">
            <label class="form-label">Order</label>
            <select name="order_id" class="form-input" required>
                @foreach($orders as $o)
                    <option value="{{ $o->id }}" {{ $delivery->order_id == $o->id ? 'selected' : '' }}>
                        Order #{{ $o->id }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Driver / Kurir</label>
            <select name="driver_id" class="form-input" required>
                @foreach($drivers as $d)
                    <option value="{{ $d->id }}" {{ old('driver_id', $delivery->driver_id) == $d->id ? 'selected' : '' }}>
                        {{ $d->name }} ({{ $d->license_plate }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Tanggal Pengiriman</label>
                <input type="date" name="delivery_date" class="form-input"
                       value="{{ old('delivery_date', $delivery->delivery_date) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-input" required>
                    <option value="Scheduled"   {{ $delivery->status === 'Scheduled'   ? 'selected' : '' }}>Scheduled</option>
                    <option value="On Delivery" {{ $delivery->status === 'On Delivery' ? 'selected' : '' }}>On Delivery</option>
                    <option value="Delivered"   {{ $delivery->status === 'Delivered'   ? 'selected' : '' }}>Delivered</option>
                </select>
            </div>
        </div>

        <div style="display:flex;gap:10px;margin-top:8px;">
            <button type="submit" class="btn-primary-custom"><i data-lucide="save" style="width:15px;height:15px;margin-right:4px;"></i> Update Delivery</button>
            <a href="{{ route('deliveries.index') }}" class="btn-secondary-custom">Batal</a>
        </div>
    </form>
</div>
@endsection