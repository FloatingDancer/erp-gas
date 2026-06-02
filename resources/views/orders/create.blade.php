@extends('layouts.app')
@section('content')
<style>
.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;}
.page-title{font-size:22px;font-weight:700;color:#0f172a;margin:0;}
.page-subtitle{font-size:13px;color:#64748b;margin:2px 0 0;}
.btn-primary-custom{display:inline-flex;align-items:center;gap:6px;background:#2563eb;color:white;border:none;padding:9px 18px;border-radius:10px;font-size:13.5px;font-weight:600;text-decoration:none;cursor:pointer;transition:background 0.15s;}
.btn-primary-custom:hover{background:#1d4ed8;color:white;}
.btn-secondary-custom{display:inline-flex;align-items:center;gap:6px;background:#f1f5f9;color:#374151;border:none;padding:9px 18px;border-radius:10px;font-size:13.5px;font-weight:600;text-decoration:none;cursor:pointer;transition:background 0.15s;}
.btn-secondary-custom:hover{background:#e2e8f0;color:#111827;}
.card-clean{background:white;border-radius:16px;border:none;box-shadow:0 1px 4px rgba(0,0,0,0.06);}
.tbl-wrap{overflow-x:auto;}
table.modern-table{width:100%;border-collapse:collapse;}
table.modern-table thead tr{background:#f8fafc;border-bottom:2px solid #e2e8f0;}
table.modern-table thead th{padding:12px 16px;font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#64748b;white-space:nowrap;}
table.modern-table tbody tr{border-bottom:1px solid #f1f5f9;transition:background 0.12s;}
table.modern-table tbody tr:hover{background:#f8fafc;}
table.modern-table tbody td{padding:13px 16px;font-size:13.5px;color:#374151;vertical-align:middle;}
.badge-pill{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11.5px;font-weight:600;}
.badge-orange{background:#ffedd5;color:#c2410c;}
.badge-green{background:#dcfce7;color:#15803d;}
.badge-blue{background:#dbeafe;color:#1d4ed8;}
.badge-gray{background:#f1f5f9;color:#475569;}
.action-edit{display:inline-flex;align-items:center;gap:4px;background:#fef9c3;color:#854d0e;border:none;padding:6px 12px;border-radius:8px;font-size:12.5px;font-weight:600;text-decoration:none;cursor:pointer;}
.action-edit:hover{background:#fef08a;}
.action-delete{display:inline-flex;align-items:center;gap:4px;background:#fee2e2;color:#b91c1c;border:none;padding:6px 12px;border-radius:8px;font-size:12.5px;font-weight:600;cursor:pointer;}
.action-delete:hover{background:#fecaca;}
.form-card{background:white;border-radius:16px;box-shadow:0 1px 4px rgba(0,0,0,0.06);padding:32px;max-width:680px;}
.form-label{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;}
.form-input{width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;color:#1e293b;transition:border-color 0.15s;outline:none;font-family:inherit;}
.form-input:focus{border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,0.12);}
.form-group{margin-bottom:20px;}
.alert-error{background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13.5px;}
.alert-success{background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:12px 16px;border-radius:10px;margin-bottom:20px;}
.empty-state{text-align:center;padding:60px 20px;color:#94a3b8;}
.empty-state .empty-icon{font-size:48px;margin-bottom:12px;}
</style>

<div class="page-header">
  <div>
    <h1 class="page-title">🛒 Buat Order</h1>
    <p class="page-subtitle">Tambah pesanan baru</p>
  </div>
  <a href="{{ route('orders.index') }}" class="btn-secondary-custom">← Kembali</a>
</div>

@if(session('error'))
  <div class="alert-error">⚠️ {{ session('error') }}</div>
@endif

<div class="form-card">
  @if($errors->any())
    <div class="alert-error">
      <strong>⚠️ Ada kesalahan:</strong>
      <ul style="margin:6px 0 0;padding-left:18px;">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="/orders" method="POST">
    @csrf

    <div class="form-group">
      <label class="form-label">Customer</label>
      <select name="customer_id" class="form-input" required>
        <option value="" disabled selected>-- Pilih Customer --</option>
        @foreach($customers as $c)
          <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
            {{ $c->customer_name }}
          </option>
        @endforeach
      </select>
    </div>

    <div class="form-group">
      <label class="form-label">Product</label>
      <select name="product_id" class="form-input" required>
        <option value="" disabled selected>-- Pilih Product --</option>
        @foreach($products as $p)
          <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>
            {{ $p->name }} — Rp {{ number_format($p->price, 0, ',', '.') }} (Stok: {{ $p->stock }})
          </option>
        @endforeach
      </select>
    </div>

    <div class="form-group">
      <label class="form-label">Quantity</label>
      <input
        type="number"
        name="quantity"
        class="form-input"
        value="{{ old('quantity') }}"
        min="1"
        placeholder="Masukkan jumlah"
        required
      >
    </div>

    <div style="display:flex;gap:10px;margin-top:8px;">
      <button type="submit" class="btn-primary-custom">💾 Buat Order</button>
      <a href="{{ route('orders.index') }}" class="btn-secondary-custom">Batal</a>
    </div>
  </form>
</div>
@endsection